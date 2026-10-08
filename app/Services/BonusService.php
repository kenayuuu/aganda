<?php

namespace App\Services;

use App\Models\BonusAllocation;
use App\Models\BonusTransaction;
use App\Models\BonusWithdrawal;
use App\Models\CalonPayment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BonusService
{
    public function getGrossBonus(User $user): float
    {
        return (float) BonusTransaction::query()
            ->where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->sum('amount');
    }

    public function getTotalAllocated(User $user): float
    {
        return (float) BonusAllocation::query()
            ->where('user_id', $user->id)
            ->whereIn('allocation_type', [
                'package_payment',
                'withdrawal',
            ])
            ->sum('amount');
    }

    public function getTotalBonus(User $user): float
    {
        return max(
            0,
            $this->getGrossBonus($user)
            - $this->getTotalAllocated($user)
        );
    }

    public function getAvailableBonus(User $user): float
    {
        return $this->getTotalBonus($user);
    }

    public function getTotalCashWithdrawal(User $user): float
    {
        return (float) BonusAllocation::query()
            ->where('user_id', $user->id)
            ->where('allocation_type', 'withdrawal')
            ->sum('amount');
    }

    public function getPackagePayment(User $user): float
    {
        return (float) BonusAllocation::query()
            ->where('user_id', $user->id)
            ->where('allocation_type', 'package_payment')
            ->sum('amount');
    }

    public function getPackagePrice(User $user): float
    {
        $calon = $user->calon;

        if (!$calon || !$calon->packageKegiatan) {
            return 0;
        }

        return (float) $calon->packageKegiatan->harga;
    }

    public function getDeposit(User $user): float
    {
        $calon = $user->calon;

        if (!$calon || !$calon->packageKegiatan) {
            return 0;
        }

        return (float) $calon->packageKegiatan->deposit;
    }

    public function getTotalPaid(User $user): float
    {
        $calon = $user->calon;

        if (!$calon) {
            return 0;
        }

        return (float) CalonPayment::query()
            ->where('calon_id', $calon->id)
            ->where('status', 'paid')
            ->sum('amount');
    }

    public function isPackagePaidOff(User $user): bool
    {
        $packagePrice = $this->getPackagePrice($user);

        if ($packagePrice <= 0) {
            return false;
        }

        $totalPaid = $this->getTotalPaid($user);

        return $totalPaid >= $packagePrice;
    }

    public function getRemainingPackage(User $user): float
    {
        $packagePrice = $this->getPackagePrice($user);
        $totalPaid = $this->getTotalPaid($user);

        return max(
            0,
            $packagePrice - $totalPaid
        );
    }

    public function allocateBonusToPackage(User $user): ?BonusAllocation
    {
        return DB::transaction(function () use ($user) {
            $available = $this->getAvailableBonus($user);
            $remainingPackage = $this->getRemainingPackage($user);

            if ($available <= 0 || $remainingPackage <= 0) {
                return null;
            }

            $amount = min(
                $available,
                $remainingPackage
            );

            $transactions = BonusTransaction::query()
                ->where('user_id', $user->id)
                ->where('status', 'confirmed')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $remainingAllocation = $amount;

            foreach ($transactions as $bonus) {
                $alreadyAllocated = (float) BonusAllocation::query()
                    ->where('bonus_transaction_id', $bonus->id)
                    ->whereIn('allocation_type', [
                        'package_payment',
                        'withdrawal',
                    ])
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
                    'allocation_type' => 'package_payment',
                    'amount' => $allocationAmount,
                    'reference_id' => $user->calon_id,
                    'notes' => 'Bonus digunakan untuk pembayaran sisa paket.',
                ]);

                $remainingAllocation -= $allocationAmount;

                if ($remainingAllocation <= 0) {
                    break;
                }
            }

            return BonusAllocation::query()
                ->where('user_id', $user->id)
                ->where('allocation_type', 'package_payment')
                ->latest('id')
                ->first();
        });
    }

    public function getPendingWithdrawal(User $user): float
    {
        return (float) BonusWithdrawal::query()
            ->where('user_id', $user->id)
            ->whereIn('status', [
                'pending',
                'approved',
            ])
            ->sum('amount');
    }

    public function getAvailableBonusForWithdrawal(User $user): float
    {
        return max(
            0,
            $this->getAvailableBonus($user)
            - $this->getPendingWithdrawal($user)
        );
    }

    public function createWithdrawal(
        User $user,
        float $amount
    ): BonusWithdrawal {
        return DB::transaction(function () use ($user, $amount) {
            if ($amount <= 0) {
                throw new \InvalidArgumentException(
                    'Nominal pencairan harus lebih dari 0.'
                );
            }

            if (
                $user->role === 'member'
                && !$this->isPackagePaidOff($user)
            ) {
                throw new \InvalidArgumentException(
                    'Bonus member hanya dapat dicairkan setelah paket umroh lunas.'
                );
            }

            $available = $this->getAvailableBonusForWithdrawal($user);

            if ($available <= 0) {
                throw new \InvalidArgumentException(
                    'Bonus yang dapat dicairkan tidak tersedia.'
                );
            }

            if ($amount > $available) {
                throw new \InvalidArgumentException(
                    'Nominal pencairan melebihi bonus yang tersedia.'
                );
            }

            return BonusWithdrawal::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'status' => 'pending',
                'requested_at' => now(),
            ]);
        });
    }
}
