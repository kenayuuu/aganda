<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BonusAllocation;
use App\Models\BonusTransaction;
use App\Models\BonusWithdrawal;
use App\Models\User;
use App\Services\BonusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BonusWithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            abort(403);
        }

        $query = BonusWithdrawal::with([
            'user',
            'processedBy',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        $withdrawals = $query
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view(
            'aganda.bonus.withdrawals.index',
            compact('withdrawals')
        );
    }

    public function show(BonusWithdrawal $withdrawal)
    {
        $withdrawal->load([
            'user.calon.packageKegiatan',
            'processedBy',
        ]);

        $user = $withdrawal->user;

        if (!$user) {
            abort(404);
        }

        $bonusService = app(BonusService::class);

        $packagePrice = $bonusService->getPackagePrice($user);
        $totalPaid = $bonusService->getTotalPaid($user);
        $remainingPackage = $bonusService->getRemainingPackage($user);
        $isPackagePaidOff = $bonusService->isPackagePaidOff($user);

        $totalBonus = $bonusService->getTotalBonus($user);
        $totalAllocated = $bonusService->getTotalAllocated($user);
        $availableBonus = $bonusService->getAvailableBonus($user);

        return view(
            'aganda.bonus.withdrawals.show',
            compact(
                'withdrawal',
                'packagePrice',
                'totalPaid',
                'remainingPackage',
                'isPackagePaidOff',
                'totalBonus',
                'totalAllocated',
                'availableBonus'
            )
        );
    }

    /**
     * Admin menerima / menyetujui pengajuan.
     *
     * pending -> approved
     */
    public function approve(
        Request $request,
        BonusWithdrawal $withdrawal
    ) {
        $admin = $request->user();

        if ($admin->role !== 'admin') {
            abort(403);
        }

        try {
            DB::transaction(function () use ($request, $withdrawal) {
                $withdrawal = BonusWithdrawal::whereKey($withdrawal->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($withdrawal->status !== 'pending') {
                    throw new \InvalidArgumentException(
                        'Pengajuan pencairan ini sudah diproses.'
                    );
                }

                $user = User::whereKey($withdrawal->user_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $bonusService = app(BonusService::class);

                /*
                 * Karyawan:
                 * tidak perlu lunas paket.
                 *
                 * Member:
                 * wajib lunas paket.
                 */
                if (
                    $user->role === 'member'
                    && !$bonusService->isPackagePaidOff($user)
                ) {
                    throw new \InvalidArgumentException(
                        'Paket member belum lunas. Pengajuan belum dapat diterima.'
                    );
                }

                /*
                 * Pastikan bonus cukup.
                 *
                 * Withdrawal masih berstatus pending,
                 * sehingga belum mengurangi bonus melalui allocation.
                 */
                $availableBonus = $bonusService->getAvailableBonus($user);

                if (
                    $availableBonus < (float) $withdrawal->amount
                ) {
                    throw new \InvalidArgumentException(
                        'Bonus tersedia tidak mencukupi untuk pengajuan ini.'
                    );
                }

                $withdrawal->update([
                    'status' => 'approved',
                    'processed_at' => now(),
                    'processed_by' => $request->user()->id,
                    'notes' => 'Pengajuan pencairan telah diterima dan disetujui admin.',
                ]);
            });

            return redirect()
                ->route(
                    'admin.bonus.withdrawals.show',
                    $withdrawal
                )
                ->with(
                    'success',
                    'Pengajuan pencairan berhasil diterima.'
                );
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route(
                    'admin.bonus.withdrawals.show',
                    $withdrawal
                )
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

    /**
     * Admin melakukan pembayaran.
     *
     * approved -> paid
     *
     * Pada tahap ini bonus benar-benar dialokasikan
     * sebagai withdrawal.
     */
    public function paid(
        Request $request,
        BonusWithdrawal $withdrawal
    ) {
        $admin = $request->user();

        if ($admin->role !== 'admin') {
            abort(403);
        }

        try {
            DB::transaction(function () use ($request, $withdrawal) {
                $withdrawal = BonusWithdrawal::whereKey($withdrawal->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($withdrawal->status !== 'approved') {
                    throw new \InvalidArgumentException(
                        'Pengajuan harus berstatus disetujui sebelum dibayar.'
                    );
                }

                $user = User::whereKey($withdrawal->user_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $bonusService = app(BonusService::class);

                /*
                 * Member tetap harus lunas.
                 *
                 * Karyawan tidak perlu lunas.
                 */
                if (
                    $user->role === 'member'
                    && !$bonusService->isPackagePaidOff($user)
                ) {
                    throw new \InvalidArgumentException(
                        'Paket member belum lunas.'
                    );
                }

                /*
                 * Jangan sampai withdrawal yang sama
                 * dialokasikan dua kali.
                 */
                $existingAllocation = BonusAllocation::where(
                    'user_id',
                    $user->id
                )
                    ->where('allocation_type', 'withdrawal')
                    ->where('reference_id', $withdrawal->id)
                    ->exists();

                if ($existingAllocation) {
                    throw new \InvalidArgumentException(
                        'Withdrawal ini sudah dialokasikan sebelumnya.'
                    );
                }

                /*
                 * Cek bonus terbaru sebelum pembayaran.
                 */
                $availableBonus = $bonusService->getAvailableBonus($user);

                if (
                    $availableBonus < (float) $withdrawal->amount
                ) {
                    throw new \InvalidArgumentException(
                        'Bonus tersedia tidak mencukupi untuk pencairan ini.'
                    );
                }

                /*
                 * Ambil transaksi bonus yang confirmed.
                 */
                $transactions = BonusTransaction::where(
                    'user_id',
                    $user->id
                )
                    ->where('status', 'confirmed')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                $remaining = (float) $withdrawal->amount;

                foreach ($transactions as $bonus) {
                    $allocated = (float) BonusAllocation::where(
                        'bonus_transaction_id',
                        $bonus->id
                    )->sum('amount');

                    $availableFromTransaction = max(
                        0,
                        (float) $bonus->amount - $allocated
                    );

                    if ($availableFromTransaction <= 0) {
                        continue;
                    }

                    $allocationAmount = min(
                        $availableFromTransaction,
                        $remaining
                    );

                    BonusAllocation::create([
                        'user_id' => $user->id,
                        'bonus_transaction_id' => $bonus->id,
                        'allocation_type' => 'withdrawal',
                        'amount' => $allocationAmount,
                        'reference_id' => $withdrawal->id,
                        'notes' => 'Bonus dicairkan dan dibayar oleh admin.',
                    ]);

                    $remaining -= $allocationAmount;

                    if ($remaining <= 0) {
                        break;
                    }
                }

                if ($remaining > 0) {
                    throw new \InvalidArgumentException(
                        'Bonus tersedia tidak mencukupi untuk pencairan ini.'
                    );
                }

                $withdrawal->update([
                    'status' => 'paid',
                    'processed_at' => now(),
                    'processed_by' => $request->user()->id,
                    'notes' => 'Pencairan bonus telah dibayar oleh admin.',
                ]);
            });

            return redirect()
                ->route(
                    'admin.bonus.withdrawals.show',
                    $withdrawal
                )
                ->with(
                    'success',
                    'Pencairan bonus berhasil ditandai sebagai sudah dibayar.'
                );
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route(
                    'admin.bonus.withdrawals.show',
                    $withdrawal
                )
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

    /**
     * Admin menolak pengajuan.
     *
     * pending -> rejected
     */
    public function reject(
        Request $request,
        BonusWithdrawal $withdrawal
    ) {
        $admin = $request->user();

        if ($admin->role !== 'admin') {
            abort(403);
        }

        if ($withdrawal->status !== 'pending') {
            return redirect()
                ->route(
                    'admin.bonus.withdrawals.show',
                    $withdrawal
                )
                ->with(
                    'error',
                    'Pengajuan pencairan ini sudah diproses.'
                );
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $withdrawal->update([
            'status' => 'rejected',
            'processed_at' => now(),
            'processed_by' => $admin->id,
            'notes' => $validated['notes']
                ?? 'Pengajuan pencairan bonus ditolak oleh admin.',
        ]);

        return redirect()
            ->route(
                'admin.bonus.withdrawals.show',
                $withdrawal
            )
            ->with(
                'success',
                'Pengajuan pencairan bonus berhasil ditolak.'
            );
    }
}
