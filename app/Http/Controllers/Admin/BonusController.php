<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgandaGroupMember;
use App\Models\BonusAllocation;
use App\Models\BonusTransaction;
use App\Models\User;
use App\Services\BonusService;
use Illuminate\Http\Request;

class BonusController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $accessibleUserIds = User::query()
            ->whereIn('role', ['member', 'karyawan']);

        if ($user->role === 'karyawan') {
            $managedMemberIds = User::query()
                ->where('role', 'member')
                ->whereIn('calon_id', AgandaGroupMember::query()
                    ->where('status', 'active')
                    ->whereHas('group', function ($groupQuery) use ($user) {
                        $groupQuery->where('owner_id', $user->id);
                    })
                    ->select('calon_id'))
                ->select('id');

            $accessibleUserIds->where(function ($scopeQuery) use ($user, $managedMemberIds) {
                $scopeQuery->where('id', $user->id)
                    ->orWhereIn('id', $managedMemberIds);
            });
        }

        $query = User::query()
            ->whereIn('id', (clone $accessibleUserIds)->select('id'))
            ->with([
                'calon.packageKegiatan',
                'parent',
            ]);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('member_id', 'like', "%{$search}%");
            });
        }

        $bonusService = app(BonusService::class);

        $totalUsers = (clone $query)->count();

        $bonusData = $query
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $bonusData->getCollection()->transform(function ($member) use ($bonusService) {
            $totalBonus = $bonusService->getTotalBonus($member);
            $packagePayment = $bonusService->getPackagePayment($member);
            $availableBonus = $bonusService->getAvailableBonus($member);
            $packagePrice = $bonusService->getPackagePrice($member);
            $deposit = $bonusService->getDeposit($member);
            $remainingPackage = $bonusService->getRemainingPackage($member);

            $line1Count = BonusTransaction::where('user_id', $member->id)
                ->where('type', 'line_1')
                ->where('status', 'confirmed')
                ->count();

            $line1Bonus = BonusTransaction::where('user_id', $member->id)
                ->where('type', 'line_1')
                ->where('status', 'confirmed')
                ->sum('amount');

            $line2Count = BonusTransaction::where('user_id', $member->id)
                ->where('type', 'line_2_pairing')
                ->where('status', 'confirmed')
                ->count();

            $line2Bonus = BonusTransaction::where('user_id', $member->id)
                ->where('type', 'line_2_pairing')
                ->where('status', 'confirmed')
                ->sum('amount');

            return [
                'member' => $member,
                'package_price' => $packagePrice,
                'deposit' => $deposit,
                'initial_remaining' => max(
                    0,
                    $packagePrice - $deposit
                ),
                'remaining_package' => $remainingPackage,
                'line1_count' => $line1Count,
                'line1_bonus' => (float) $line1Bonus,
                'line2_count' => $line2Count,
                'line2_bonus' => (float) $line2Bonus,
                'total_bonus' => $totalBonus,
                'package_payment' => $packagePayment,
                'available_bonus' => $availableBonus,
            ];
        });

        $totalBonus = BonusTransaction::whereIn('user_id', (clone $accessibleUserIds)->select('id'))
            ->where('status', 'confirmed')
            ->sum('amount');

        $totalAllocated = BonusAllocation::whereIn('user_id', (clone $accessibleUserIds)->select('id'))
            ->sum('amount');

        $totalAvailableBonus = max(
            0,
            (float) $totalBonus - (float) $totalAllocated
        );

        return view(
            'aganda.bonus.index',
            compact(
                'bonusData',
                'totalUsers',
                'totalBonus',
                'totalAvailableBonus'
            )
        );
    }
}
