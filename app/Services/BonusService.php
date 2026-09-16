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
    public function getTotalBonus(User $user): float
    {
        return (float) BonusTransaction::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->sum('amount');
    }

    public function getTotalAllocated(User $user): float
    {
        return (float) BonusAllocation::where('user_id', $user->id)
            ->sum('amount');
    }

    public function getAvailableBonus(User $user): float
    {
        return max(
            0,
            $this->getTotalBonus($user) - $this->getTotalAllocated($user)
        );
    }

    public function getTotalCashWithdrawal(User $user): float
    {
        return (float) BonusAllocation::where('user_id', $user->id)
            ->where('allocation_type', 'withdrawal')
            ->sum('amount');
    }

    public function getPackagePayment(User $user): float
    {
        return (float) BonusAllocation::where('user_id', $user->id)
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

    public function getRemainingPackage(User $user): float
    {
        $packagePrice = $this->getPackagePrice($user);
        $deposit = $this->getDeposit($user);
        $packagePayment = $this->getPackagePayment($user);

        $initialRemaining = max(
            0,
            $packagePrice - $deposit
        );

        return max(
            0,
            $initialRemaining - $packagePayment
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

            $transactions = BonusTransaction::where('user_id', $user->id)
                ->where('status', 'confirmed')
                ->orderBy('id')
                ->get();

            $remainingAllocation = $amount;

            foreach ($transactions as $bonus) {
                $alreadyAllocated = (float) $bonus->allocations()->sum('amount');

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

            return BonusAllocation::where('user_id', $user->id)
                ->where('allocation_type', 'package_payment')
                ->latest('id')
                ->first();
        });
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
