<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BonusAllocation;
use App\Models\BonusTransaction;
use App\Models\CalonPayment;
use App\Services\BonusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BonusWithdrawal;

class BonusController extends Controller
{
    public function __construct(
        private BonusService $bonusService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'karyawan', 'member'])) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke data bonus.',
            ], 403);
        }

        $transactions = BonusTransaction::query()
            ->where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->with([
                'group',
                'sourceUser',
            ])
            ->latest()
            ->get();

        $allocations = BonusAllocation::query()
            ->where('user_id', $user->id)
            ->with('bonusTransaction')
            ->latest()
            ->get();

        $totalBonus = (float) $transactions->sum('amount');

        $line1Bonus = (float) $transactions
            ->where('type', 'line_1')
            ->sum('amount');

        $line2Bonus = (float) $transactions
            ->where('type', 'line_2_pairing')
            ->sum('amount');

        $adjustmentBonus = (float) $transactions
            ->where('type', 'adjustment')
            ->sum('amount');

        $totalAllocated = (float) $allocations->sum('amount');

        $packagePayment = (float) $allocations
            ->where('allocation_type', 'package_payment')
            ->sum('amount');

        $withdrawalAllocation = (float) $allocations
            ->where('allocation_type', 'withdrawal')
            ->sum('amount');

        $availableBonus = max(
            0,
            $totalBonus - $totalAllocated
        );

        $packagePrice = 0;
        $deposit = 0;
        $paidDp = 0;
        $paidPackage = 0;
        $remainingPackage = 0;
        $package = null;

        if ($user->calon_id) {
            $calon = $user->calon()
                ->with('packageKegiatan')
                ->first();

            if ($calon && $calon->packageKegiatan) {
                $package = $calon->packageKegiatan;

                $packagePrice = (float) $package->harga;
                $deposit = (float) $package->deposit;

                $paidDp = (float) CalonPayment::query()
                    ->where('calon_id', $user->calon_id)
                    ->where('payment_type', 'dp')
                    ->where('status', 'paid')
                    ->sum('amount');

                $paidPackage = $paidDp + $packagePayment;

                $remainingPackage = max(
                    0,
                    $packagePrice - $paidPackage
                );
            }
        }

        $line1Count = $transactions
            ->where('type', 'line_1')
            ->count();

        $line2Count = $transactions
            ->where('type', 'line_2_pairing')
            ->count();

        $bonusHistory = $transactions
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'amount' => (float) $transaction->amount,
                    'status' => $transaction->status,
                    'description' => $transaction->description,
                    'group' => $transaction->group ? [
                        'id' => $transaction->group->id,
                        'kode_group' => $transaction->group->kode_group,
                    ] : null,
                    'source_user' => $transaction->sourceUser ? [
                        'id' => $transaction->sourceUser->id,
                        'name' => $transaction->sourceUser->name,
                        'member_id' => $transaction->sourceUser->member_id,
                    ] : null,
                    'created_at' => $transaction->created_at,
                ];
            })
            ->values();

        $allocationHistory = $allocations
            ->map(function ($allocation) {
                return [
                    'id' => $allocation->id,
                    'bonus_transaction_id' => $allocation->bonus_transaction_id,
                    'allocation_type' => $allocation->allocation_type,
                    'amount' => (float) $allocation->amount,
                    'reference_id' => $allocation->reference_id,
                    'notes' => $allocation->notes,
                    'created_at' => $allocation->created_at,
                ];
            })
            ->values();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'member_id' => $user->member_id,
                'role' => $user->role,
            ],
            'summary' => [
                'total_bonus' => $totalBonus,
                'line_1_bonus' => $line1Bonus,
                'line_2_bonus' => $line2Bonus,
                'adjustment_bonus' => $adjustmentBonus,
                'total_allocated' => $totalAllocated,
                'package_payment' => $packagePayment,
                'withdrawal_allocation' => $withdrawalAllocation,
                'available_bonus' => $availableBonus,
                'line_1_count' => $line1Count,
                'line_2_count' => $line2Count,
            ],
            'package' => $package ? [
                'id' => $package->id,
                'name' => $package->nama_paket
                    ?? $package->name
                    ?? null,
                'harga' => $packagePrice,
                'deposit' => $deposit,
                'paid_dp' => $paidDp,
                'paid_package' => $paidPackage,
                'remaining' => $remainingPackage,
                'tanggal_berlangsung' => $package->tanggal_berlangsung,
            ] : null,
            'bonus_history' => $bonusHistory,
            'allocation_history' => $allocationHistory,
        ]);
    }

    public function history(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'karyawan', 'member'])) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke riwayat bonus.',
            ], 403);
        }

        $transactions = BonusTransaction::query()
            ->where('user_id', $user->id)
            ->latest()
            ->with([
                'group',
                'sourceUser',
            ])
            ->get();

        return response()->json([
            'history' => $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'amount' => (float) $transaction->amount,
                    'status' => $transaction->status,
                    'description' => $transaction->description,
                    'group' => $transaction->group ? [
                        'id' => $transaction->group->id,
                        'kode_group' => $transaction->group->kode_group,
                    ] : null,
                    'source_user' => $transaction->sourceUser ? [
                        'id' => $transaction->sourceUser->id,
                        'name' => $transaction->sourceUser->name,
                        'member_id' => $transaction->sourceUser->member_id,
                    ] : null,
                    'created_at' => $transaction->created_at,
                ];
            })->values(),
        ]);
    }

    public function allocatePackage(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'karyawan', 'member'])) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menggunakan bonus.',
            ], 403);
        }

        if (!$user->calon_id) {
            return response()->json([
                'message' => 'Akun Anda belum memiliki paket kegiatan.',
            ], 422);
        }

        $availableBonus = $this->bonusService->getAvailableBonus($user);
        $remainingPackage = $this->bonusService->getRemainingPackage($user);

        if ($availableBonus <= 0) {
            return response()->json([
                'message' => 'Bonus tersedia tidak mencukupi.',
                'available_bonus' => $availableBonus,
            ], 422);
        }

        if ($remainingPackage <= 0) {
            return response()->json([
                'message' => 'Paket Anda sudah lunas.',
                'remaining_package' => $remainingPackage,
            ], 422);
        }

        $amount = min(
            $availableBonus,
            $remainingPackage
        );

        $allocation = $this->bonusService->allocateBonusToPackage($user);

        if (!$allocation) {
            return response()->json([
                'message' => 'Bonus tidak dapat dialokasikan ke pembayaran paket.',
            ], 422);
        }

        $totalPackagePayment = $this->bonusService->getPackagePayment($user);
        $newAvailableBonus = $this->bonusService->getAvailableBonus($user);
        $newRemainingPackage = $this->bonusService->getRemainingPackage($user);

        return response()->json([
            'message' => 'Bonus berhasil dialokasikan untuk pembayaran paket.',
            'allocation' => [
                'id' => $allocation->id,
                'bonus_transaction_id' => $allocation->bonus_transaction_id,
                'allocation_type' => $allocation->allocation_type,
                'amount' => (float) $amount,
                'reference_id' => $allocation->reference_id,
                'notes' => $allocation->notes,
            ],
            'summary' => [
                'allocated_amount' => (float) $amount,
                'available_bonus' => $newAvailableBonus,
                'package_payment' => $totalPackagePayment,
                'remaining_package' => $newRemainingPackage,
            ],
        ]);
    }

    public function createWithdrawal(User $user, float $amount): BonusWithdrawal
    {
        return DB::transaction(function () use ($user, $amount) {
            $available = $this->getAvailableBonus($user);

            if ($amount <= 0) {
                throw new \InvalidArgumentException(
                    'Nominal pencairan harus lebih dari 0.'
                );
            }

            if ($amount > $available) {
                throw new \InvalidArgumentException(
                    'Nominal pencairan melebihi bonus yang tersedia.'
                );
            }

            $withdrawal = BonusWithdrawal::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'status' => 'pending',
                'requested_at' => now(),
            ]);

            $transactions = BonusTransaction::where('user_id', $user->id)
                ->where('status', 'confirmed')
                ->orderBy('id')
                ->get();

            $remainingAllocation = $amount;

            foreach ($transactions as $bonus) {
                $alreadyAllocated = (float) $bonus->allocations()
                    ->sum('amount');

                $availableFromTransaction = max(
                    0,
                    (float) $bonus->amount - $alreadyAllocated
                );

                if ($availableFromTransaction <= 0) {
                    continue;
                }

                $allocationAmount = min(
                    $availableFromTransaction,
                    $remainingAllocation
                );

                BonusAllocation::create([
                    'user_id' => $user->id,
                    'bonus_transaction_id' => $bonus->id,
                    'allocation_type' => 'withdrawal',
                    'amount' => $allocationAmount,
                    'reference_id' => $withdrawal->id,
                    'notes' => 'Bonus digunakan untuk pengajuan pencairan.',
                ]);

                $remainingAllocation -= $allocationAmount;

                if ($remainingAllocation <= 0) {
                    break;
                }
            }

            return $withdrawal;
        });
    }
}
