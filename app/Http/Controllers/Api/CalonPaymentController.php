<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Calon;
use App\Models\CalonPayment;
use App\Models\PackageKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalonPaymentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'karyawan'])) {
            return response()->json([
                'message' => 'Hanya admin atau karyawan yang dapat melihat pembayaran calon.',
            ], 403);
        }

        $payments = CalonPayment::with([
            'calon',
            'packageKegiatan',
            'confirmedBy',
            'member',
        ])
            ->where('payment_type', 'dp')
            ->latest('id')
            ->get();

        if ($user->role === 'karyawan') {
            $payments = $payments->filter(function ($payment) use ($user) {
                return $payment->member
                    && (int) $payment->member->parent_id === (int) $user->id;
            })->values();
        }

        return response()->json([
            'payments' => $payments->map(function ($payment) {
                return $this->formatPayment($payment);
            })->values(),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'karyawan'])) {
            return response()->json([
                'message' => 'Hanya admin atau karyawan yang dapat mencatat pembayaran DP.',
            ], 403);
        }

        $validated = $request->validate([
            'calon_id' => [
                'required',
                'exists:calons,id',
            ],
        ]);

        $calon = Calon::with('packageKegiatan')
            ->findOrFail($validated['calon_id']);

        if (!$calon->packageKegiatan) {
            return response()->json([
                'message' => 'Paket kegiatan calon tidak ditemukan.',
            ], 422);
        }

        $package = $calon->packageKegiatan;

        if (!$package->is_active) {
            return response()->json([
                'message' => 'Paket kegiatan calon tidak aktif.',
            ], 422);
        }

        if ((float) $package->deposit <= 0) {
            return response()->json([
                'message' => 'Deposit paket belum tersedia atau bernilai tidak valid.',
            ], 422);
        }

        $existingDp = CalonPayment::where('calon_id', $calon->id)
            ->where('payment_type', 'dp')
            ->where('status', 'paid')
            ->latest('id')
            ->first();

        if ($existingDp) {
            return response()->json([
                'message' => 'DP calon tersebut sudah tercatat.',
                'payment' => $this->formatPayment($existingDp->load([
                    'calon',
                    'packageKegiatan',
                    'confirmedBy',
                    'member',
                ])),
            ], 422);
        }

        $member = User::where('calon_id', $calon->id)->first();

        if (
            $user->role === 'karyawan'
            && $member
            && (int) $member->parent_id !== (int) $user->id
        ) {
            return response()->json([
                'message' => 'Calon tersebut tidak berada di bawah karyawan ini.',
            ], 403);
        }

        $payment = DB::transaction(function () use ($calon, $package, $user) {
            return CalonPayment::create([
                'calon_id' => $calon->id,
                'package_kegiatan_id' => $package->id,
                'package_price' => (float) $package->harga,
                'deposit_amount' => (float) $package->deposit,
                'payment_type' => 'dp',
                'amount' => (float) $package->deposit,
                'status' => 'paid',
                'paid_at' => now(),
                'confirmed_by' => $user->id,
                'notes' => 'Pembayaran DP dicatat secara manual.',
            ]);
        });

        $payment->load([
            'calon',
            'packageKegiatan',
            'confirmedBy',
            'member',
        ]);

        return response()->json([
            'message' => 'Pembayaran DP berhasil dicatat.',
            'payment' => $this->formatPayment($payment),
        ], 201);
    }

    private function formatPayment(CalonPayment $payment)
    {
        return [
            'id' => $payment->id,
            'calon' => $payment->calon ? [
                'id' => $payment->calon->id,
                'nama_lengkap' => $payment->calon->nama_lengkap,
                'email' => $payment->calon->email,
                'no_telepon' => $payment->calon->no_telepon,
            ] : null,
            'member' => $payment->member ? [
                'id' => $payment->member->id,
                'name' => $payment->member->name,
                'member_id' => $payment->member->member_id,
                'role' => $payment->member->role,
            ] : null,
            'package' => $payment->packageKegiatan ? [
                'id' => $payment->packageKegiatan->id,
                'name' => $payment->packageKegiatan->nama_paket
                    ?? $payment->packageKegiatan->name
                    ?? null,
                'harga' => (float) $payment->packageKegiatan->harga,
                'deposit' => (float) $payment->packageKegiatan->deposit,
            ] : null,
            'payment_type' => $payment->payment_type,
            'package_price' => (float) $payment->package_price,
            'deposit_amount' => (float) $payment->deposit_amount,
            'amount' => (float) $payment->amount,
            'status' => $payment->status,
            'paid_at' => $payment->paid_at,
            'confirmed_by' => $payment->confirmedBy ? [
                'id' => $payment->confirmedBy->id,
                'name' => $payment->confirmedBy->name,
            ] : null,
            'notes' => $payment->notes,
            'created_at' => $payment->created_at,
        ];
    }
}
