<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BonusAllocation;
use App\Models\BonusTransaction;
use App\Models\BonusWithdrawal;
use App\Models\User;
use App\Services\BonusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminWithdrawalController extends Controller
{
    /**
     * Pastikan hanya admin yang dapat mengakses endpoint ini.
     */
    private function authorizeAdmin(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(response()->json([
                'message' => 'Hanya admin yang dapat mengakses data pencairan.',
            ], 403));
        }
    }

    /**
     * Daftar seluruh withdrawal.
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin($request);

        $withdrawals = BonusWithdrawal::query()
            ->with([
                'user',
                'processedBy',
            ])
            ->latest('id')
            ->get();

        return response()->json([
            'withdrawals' => $withdrawals->map(function ($withdrawal) {
                return [
                    'id' => $withdrawal->id,
                    'amount' => (float) $withdrawal->amount,
                    'status' => $withdrawal->status,
                    'requested_at' => $withdrawal->requested_at,
                    'processed_at' => $withdrawal->processed_at,
                    'notes' => $withdrawal->notes,

                    'user' => $withdrawal->user ? [
                        'id' => $withdrawal->user->id,
                        'name' => $withdrawal->user->name,
                        'email' => $withdrawal->user->email,
                        'member_id' => $withdrawal->user->member_id,
                        'role' => $withdrawal->user->role,
                    ] : null,

                    'processed_by' => $withdrawal->processedBy ? [
                        'id' => $withdrawal->processedBy->id,
                        'name' => $withdrawal->processedBy->name,
                    ] : null,
                ];
            })->values(),
        ]);
    }

    /**
     * Approve withdrawal.
     *
     * Status:
     * pending -> approved
     *
     * Aturan:
     * - Karyawan tidak perlu melunasi paket.
     * - Member wajib melunasi paket.
     */
    public function approve(
        Request $request,
        BonusWithdrawal $withdrawal
    ) {
        $this->authorizeAdmin($request);

        if ($withdrawal->status !== 'pending') {
            return response()->json([
                'message' =>
                    'Withdrawal hanya dapat disetujui jika statusnya pending.',
                'status' => $withdrawal->status,
            ], 422);
        }

        try {
            DB::transaction(function () use (
                $request,
                $withdrawal
            ) {
                /*
                 * Lock withdrawal agar tidak diproses
                 * secara bersamaan.
                 */
                $withdrawal = BonusWithdrawal::whereKey(
                    $withdrawal->id
                )
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($withdrawal->status !== 'pending') {
                    throw new \InvalidArgumentException(
                        'Withdrawal sudah diproses.'
                    );
                }

                /*
                 * Lock user pemilik bonus.
                 */
                $user = User::whereKey($withdrawal->user_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $bonusService = app(BonusService::class);

                /*
                 * MEMBER wajib lunas paket.
                 *
                 * KARYAWAN tidak perlu lunas paket.
                 */
                if (
                    $user->role === 'member'
                    && !$bonusService->isPackagePaidOff($user)
                ) {
                    throw new \InvalidArgumentException(
                        'Paket member belum lunas. Withdrawal tidak dapat disetujui.'
                    );
                }

                /*
                 * Pastikan bonus yang tersedia cukup.
                 *
                 * Withdrawal yang masih pending/approved
                 * juga harus diperhitungkan.
                 */
                $availableForWithdrawal =
                    $bonusService->getAvailableBonusForWithdrawal(
                        $user
                    );

                /*
                 * Withdrawal yang sedang diproses ini
                 * termasuk dalam pending balance, sehingga
                 * kurangi kembali nominalnya agar tidak
                 * dianggap sebagai dirinya sendiri.
                 */
                $availableForThisWithdrawal =
                    $availableForWithdrawal
                    + (float) $withdrawal->amount;

                if (
                    (float) $withdrawal->amount
                    > $availableForThisWithdrawal
                ) {
                    throw new \InvalidArgumentException(
                        'Bonus tidak mencukupi untuk withdrawal ini.'
                    );
                }

                $withdrawal->update([
                    'status' => 'approved',
                    'processed_at' => now(),
                    'processed_by' => $request->user()->id,
                ]);
            });

            $withdrawal->refresh();

            return response()->json([
                'message' =>
                    'Pengajuan pencairan berhasil disetujui.',
                'withdrawal' => [
                    'id' => $withdrawal->id,
                    'amount' => (float) $withdrawal->amount,
                    'status' => $withdrawal->status,
                    'processed_at' => $withdrawal->processed_at,
                    'processed_by' => $withdrawal->processed_by,
                ],
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Reject withdrawal.
     *
     * Status:
     * pending -> rejected
     */
    public function reject(
        Request $request,
        BonusWithdrawal $withdrawal
    ) {
        $this->authorizeAdmin($request);

        if ($withdrawal->status !== 'pending') {
            return response()->json([
                'message' =>
                    'Withdrawal hanya dapat ditolak jika statusnya pending.',
                'status' => $withdrawal->status,
            ], 422);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use (
            $request,
            $withdrawal,
            $validated
        ) {
            $withdrawal = BonusWithdrawal::whereKey(
                $withdrawal->id
            )
                ->lockForUpdate()
                ->firstOrFail();

            if ($withdrawal->status !== 'pending') {
                throw new \InvalidArgumentException(
                    'Withdrawal sudah diproses.'
                );
            }

            /*
             * Tidak perlu menghapus BonusAllocation
             * karena allocation baru dibuat ketika paid.
             */
            $withdrawal->update([
                'status' => 'rejected',
                'processed_at' => now(),
                'processed_by' => $request->user()->id,
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        $withdrawal->refresh();

        return response()->json([
            'message' =>
                'Pengajuan pencairan ditolak dan bonus tetap tersedia.',
            'withdrawal' => [
                'id' => $withdrawal->id,
                'amount' => (float) $withdrawal->amount,
                'status' => $withdrawal->status,
                'processed_at' => $withdrawal->processed_at,
                'processed_by' => $withdrawal->processed_by,
                'notes' => $withdrawal->notes,
            ],
        ]);
    }

    /**
     * Mark withdrawal as paid.
     *
     * Status:
     * approved -> paid
     *
     * Pada tahap ini BonusAllocation dibuat sehingga
     * bonus benar-benar berkurang dari saldo tersedia.
     */
    public function paid(
        Request $request,
        BonusWithdrawal $withdrawal
    ) {
        $this->authorizeAdmin($request);

        if ($withdrawal->status !== 'approved') {
            return response()->json([
                'message' =>
                    'Withdrawal harus berstatus approved sebelum ditandai sebagai paid.',
                'status' => $withdrawal->status,
            ], 422);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use (
                $request,
                $withdrawal,
                $validated
            ) {
                /*
                 * Lock withdrawal.
                 */
                $withdrawal = BonusWithdrawal::whereKey(
                    $withdrawal->id
                )
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($withdrawal->status !== 'approved') {
                    throw new \InvalidArgumentException(
                        'Withdrawal sudah diproses.'
                    );
                }

                /*
                 * Lock user.
                 */
                $user = User::whereKey($withdrawal->user_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $bonusService = app(BonusService::class);

                /*
                 * Safety check:
                 *
                 * Member harus tetap lunas.
                 * Karyawan tidak membutuhkan pelunasan paket.
                 */
                if (
                    $user->role === 'member'
                    && !$bonusService->isPackagePaidOff($user)
                ) {
                    throw new \InvalidArgumentException(
                        'Paket member belum lunas. Withdrawal tidak dapat dibayar.'
                    );
                }

                /*
                 * Pastikan belum pernah dibuat allocation
                 * untuk withdrawal ini.
                 */
                $alreadyAllocated = BonusAllocation::where(
                    'allocation_type',
                    'withdrawal'
                )
                    ->where(
                        'reference_id',
                        $withdrawal->id
                    )
                    ->exists();

                if ($alreadyAllocated) {
                    throw new \InvalidArgumentException(
                        'Alokasi bonus untuk withdrawal ini sudah dibuat.'
                    );
                }

                /*
                 * Cek saldo bonus aktual.
                 */
                $availableBonus =
                    $bonusService->getAvailableBonus($user);

                if (
                    (float) $withdrawal->amount
                    > $availableBonus
                ) {
                    throw new \InvalidArgumentException(
                        'Bonus tidak mencukupi untuk pencairan ini.'
                    );
                }

                /*
                 * Ambil transaksi bonus yang sudah confirmed.
                 */
                $transactions = BonusTransaction::where(
                    'user_id',
                    $user->id
                )
                    ->where('status', 'confirmed')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                $remaining =
                    (float) $withdrawal->amount;

                foreach ($transactions as $bonus) {
                    /*
                     * Hitung total allocation dari transaksi bonus.
                     */
                    $allocated = (float) BonusAllocation::where(
                        'bonus_transaction_id',
                        $bonus->id
                    )->sum('amount');

                    $availableFromTransaction = max(
                        0,
                        (float) $bonus->amount
                        - $allocated
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
                        'notes' =>
                            'Bonus dicairkan dan dikonfirmasi admin.',
                    ]);

                    $remaining -= $allocationAmount;

                    if ($remaining <= 0) {
                        break;
                    }
                }

                /*
                 * Pengaman apabila allocation tidak berhasil
                 * memenuhi seluruh nominal withdrawal.
                 */
                if ($remaining > 0) {
                    throw new \InvalidArgumentException(
                        'Bonus tersedia tidak mencukupi untuk pencairan ini.'
                    );
                }

                /*
                 * Setelah allocation berhasil,
                 * withdrawal resmi menjadi paid.
                 */
                $withdrawal->update([
                    'status' => 'paid',
                    'processed_at' => now(),
                    'processed_by' => $request->user()->id,
                    'notes' =>
                        $validated['notes']
                        ?? $withdrawal->notes
                        ?? 'Pencairan bonus telah dibayar dan dikonfirmasi admin.',
                ]);
            });

            $withdrawal->refresh();

            return response()->json([
                'message' =>
                    'Pencairan berhasil ditandai sebagai paid dan bonus telah dikurangi dari saldo tersedia.',
                'withdrawal' => [
                    'id' => $withdrawal->id,
                    'amount' => (float) $withdrawal->amount,
                    'status' => $withdrawal->status,
                    'processed_at' => $withdrawal->processed_at,
                    'processed_by' => $withdrawal->processed_by,
                    'notes' => $withdrawal->notes,
                ],
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}

