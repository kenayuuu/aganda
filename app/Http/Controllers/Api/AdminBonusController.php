<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BonusAllocation;
use App\Models\BonusTransaction;
use App\Models\User;
use App\Services\BonusService;
use Illuminate\Http\Request;

class AdminBonusController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Akses hanya untuk admin.',
            ], 403);
        }

        $query = User::whereIn('role', ['member', 'karyawan'])
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

            $line1Query = BonusTransaction::where('user_id', $member->id)
                ->where('type', 'line_1')
                ->where('status', 'confirmed');

            $line1Count = (clone $line1Query)->count();
            $line1Bonus = (clone $line1Query)->sum('amount');

            $line2Query = BonusTransaction::where('user_id', $member->id)
                ->where('type', 'line_2_pairing')
                ->where('status', 'confirmed');

            $line2Count = (clone $line2Query)->count();
            $line2Bonus = (clone $line2Query)->sum('amount');

            return [
                'user' => [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'member_id' => $member->member_id,
                    'role' => $member->role,
                    'avatar' => $member->avatar,
                ],
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

        $memberUserQuery = User::whereIn('role', ['member', 'karyawan'])
            ->select('id');

        $totalBonus = BonusTransaction::whereIn(
            'user_id',
            $memberUserQuery
        )
            ->where('status', 'confirmed')
            ->sum('amount');

        $totalAllocated = BonusAllocation::whereIn(
            'user_id',
            User::whereIn('role', ['member', 'karyawan'])
                ->select('id')
        )
            ->sum('amount');

        $totalAvailableBonus = max(
            0,
            (float) $totalBonus - (float) $totalAllocated
        );

        return response()->json([
            'summary' => [
                'total_users' => $totalUsers,
                'total_bonus' => (float) $totalBonus,
                'total_available_bonus' => $totalAvailableBonus,
            ],
            'data' => $bonusData->items(),
            'pagination' => [
                'current_page' => $bonusData->currentPage(),
                'last_page' => $bonusData->lastPage(),
                'per_page' => $bonusData->perPage(),
                'total' => $bonusData->total(),
                'from' => $bonusData->firstItem(),
                'to' => $bonusData->lastItem(),
            ],
        ]);
    }
}
