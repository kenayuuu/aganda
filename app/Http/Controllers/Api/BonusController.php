<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BonusAllocation;
use App\Models\BonusTransaction;
use App\Models\CalonPayment;
use App\Services\BonusService;
use Illuminate\Http\Request;

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

        $totalBonus = $this->bonusService->getGrossBonus($user);

        $totalAllocated = $this->bonusService->getTotalAllocated($user);

        $availableBonus = $this->bonusService->getAvailableBonus($user);

        $line1Bonus = (float) $transactions
            ->where('type', 'line_1')
            ->sum('amount');

        $line2Bonus = (float) $transactions
            ->where('type', 'line_2_pairing')
            ->sum('amount');

        $adjustmentBonus = (float) $transactions
            ->where('type', 'adjustment')
            ->sum('amount');

        $packagePayment = $this->bonusService
            ->getPackagePayment($user);

        $withdrawalAllocation = $this->bonusService
            ->getTotalCashWithdrawal($user);

        $packagePrice = $this->bonusService
            ->getPackagePrice($user);

        $deposit = $this->bonusService
            ->getDeposit($user);

        $totalPaid = $this->bonusService
            ->getTotalPaid($user);

        $remainingPackage = $this->bonusService
            ->getRemainingPackage($user);

        $isPackagePaidOff = $this->bonusService
            ->isPackagePaidOff($user);

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

        $package = null;

        if ($user->calon_id) {
            $calon = $user->calon()
                ->with('packageKegiatan')
                ->first();

            if ($calon && $calon->packageKegiatan) {
                $packageModel = $calon->packageKegiatan;

                $package = [
                    'id' => $packageModel->id,
                    'name' => $packageModel->nama_paket
                        ?? $packageModel->name
                        ?? null,
                    'package_id' => $packageModel->id,
                    'package_name' => $packageModel->nama_paket
                        ?? $packageModel->name
                        ?? null,
                    'package_price' => $packagePrice,
                    'harga' => $packagePrice,
                    'deposit' => $deposit,
                    'paid_dp' => max(
                        0,
                        $totalPaid - $packagePayment
                    ),
                    'paid_package' => $totalPaid + $packagePayment,
                    'remaining_package' => $remainingPackage,
                    'remaining' => $remainingPackage,
                    'is_paid_off' => $isPackagePaidOff,
                    'tanggal_berlangsung' =>
                        $packageModel->tanggal_berlangsung,
                ];
            }
        }

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
            'package' => $package,
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
            'history' => $transactions
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
                ->values(),
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

        $availableBonus = $this->bonusService
            ->getAvailableBonus($user);

        $remainingPackage = $this->bonusService
            ->getRemainingPackage($user);

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

        $allocation = $this->bonusService
            ->allocateBonusToPackage($user);

        if (!$allocation) {
            return response()->json([
                'message' => 'Bonus tidak dapat dialokasikan ke pembayaran paket.',
            ], 422);
        }

        $totalPackagePayment = $this->bonusService
            ->getPackagePayment($user);

        $newAvailableBonus = $this->bonusService
            ->getAvailableBonus($user);

        $newRemainingPackage = $this->bonusService
            ->getRemainingPackage($user);

        return response()->json([
            'message' => 'Bonus berhasil dialokasikan untuk pembayaran paket.',
            'allocation' => [
                'id' => $allocation->id,
                'bonus_transaction_id' => $allocation->bonus_transaction_id,
                'allocation_type' => $allocation->allocation_type,
                'amount' => (float) $allocation->amount,
                'reference_id' => $allocation->reference_id,
                'notes' => $allocation->notes,
            ],
            'summary' => [
                'allocated_amount' => (float) $allocation->amount,
                'available_bonus' => $newAvailableBonus,
                'package_payment' => $totalPackagePayment,
                'remaining_package' => $newRemainingPackage,
            ],
        ]);
    }
}
