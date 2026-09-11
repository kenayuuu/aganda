<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgandaGroupMember;
use App\Models\BonusTransaction;
use App\Models\Calon;
use App\Models\User;
use App\Services\BonusService;
use App\Models\CalonPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalonPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = CalonPayment::with([
            'calon',
            'packageKegiatan',
            'confirmedBy',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->whereHas('calon', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('no_telepon', 'like', "%{$search}%");
            });
        }

        $payments = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view(
            'aganda.bonus.payments.index',
            compact('payments')
        );
    }

    public function create()
    {
        $calons = Calon::with('packageKegiatan')
            ->latest()
            ->get();

        return view(
            'aganda.bonus.payments.create',
            compact('calons')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'calon_id' => ['required', 'exists:calons,id'],
            'payment_type' => ['required', 'in:dp,pelunasan'],
            'amount' => ['required', 'numeric', 'min:1'],
            'paid_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $calon = Calon::with('packageKegiatan')
            ->findOrFail($validated['calon_id']);

        if (!$calon->packageKegiatan) {
            return back()
                ->withInput()
                ->with('error', 'Paket calon tidak ditemukan.');
        }

        $member = \App\Models\User::where('calon_id', $calon->id)
            ->where('role', 'member')
            ->first();

        if (!$member) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Calon ini belum terhubung dengan akun member.'
                );
        }

        if ($validated['payment_type'] === 'dp') {
            $existingDp = CalonPayment::where('calon_id', $calon->id)
                ->where('payment_type', 'dp')
                ->where('status', 'paid')
                ->exists();

            if ($existingDp) {
                return back()
                    ->withInput()
                    ->with('error', 'DP calon ini sudah tercatat.');
            }

            $deposit = (float) $calon->packageKegiatan->deposit;

            if ((float) $validated['amount'] < $deposit) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Nominal DP tidak boleh kurang dari deposit paket.'
                    );
            }
        }

        $groupMember = AgandaGroupMember::where(
            'calon_id',
            $member->calon_id
        )
            ->where('status', 'active')
            ->first();

        if (!$groupMember) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Member belum terdaftar pada struktur AGANDA aktif.'
                );
        }

        $sponsor = null;

        if ($member->parent_id) {
            $sponsor = \App\Models\User::where('id', $member->parent_id)
                ->where('role', 'member')
                ->first();
        }

        DB::transaction(function () use (
            $validated,
            $calon,
            $member,
            $sponsor,
            $groupMember
        ) {
            $payment = CalonPayment::create([
                'calon_id' => $calon->id,
                'package_kegiatan_id' => $calon->package_kegiatan_id,
                'package_price' => $calon->packageKegiatan->harga,
                'deposit_amount' => $calon->packageKegiatan->deposit,
                'payment_type' => $validated['payment_type'],
                'amount' => $validated['amount'],
                'status' => 'paid',
                'paid_at' => $validated['paid_at'],
                'confirmed_by' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            if ($validated['payment_type'] !== 'dp') {
                return;
            }

            if (!$sponsor) {
                return;
            }

            $alreadyGenerated = BonusTransaction::where(
                'source_payment_id',
                $payment->id
            )
                ->where('type', 'line_1')
                ->exists();

            if ($alreadyGenerated) {
                return;
            }

            BonusTransaction::create([
                'user_id' => $sponsor->id,
                'group_id' => $groupMember->group_id,
                'source_user_id' => $member->id,
                'source_payment_id' => $payment->id,
                'type' => 'line_1',
                'amount' => 3000000,
                'status' => 'confirmed',
                'description' => 'Komisi Line 1 dari pembayaran DP ' . $member->name,
            ]);
        });

        return redirect()
            ->route('admin.bonus.payments.index')
            ->with(
                'success',
                'Pembayaran berhasil dicatat dan komisi Line 1 berhasil dibuat.'
            );
    }

    public function allocateBonus(Request $request, User $user)
    {
        $bonusService = app(BonusService::class);

        $allocation = $bonusService->allocateBonusToPackage($user);

        if (!$allocation) {
            return back()->with(
                'error',
                'Tidak ada bonus yang dapat dialokasikan ke pembayaran paket.'
            );
        }

        return back()->with(
            'success',
            'Bonus berhasil dialokasikan untuk pembayaran sisa paket.'
        );
    }
}
