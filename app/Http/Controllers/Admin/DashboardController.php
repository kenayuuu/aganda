<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgandaCommission;
use App\Models\AgandaGroup;
use App\Models\AgandaGroupMember;
use App\Models\AgandaReward;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalGroups = AgandaGroup::count();
        $activeGroups = AgandaGroup::where('status', 'active')
            ->count();
        $completedGroups = AgandaGroup::where('status', 'completed')
            ->count();
        $cancelledGroups = AgandaGroup::where('status', 'cancelled')
            ->count();
        $totalMembers = User::where('role', 'member')
            ->count();
        $totalKaryawan = User::where('role', 'karyawan')
            ->count();
        $totalAdmin = User::where('role', 'admin')
            ->count();
        $totalGroupMembers = AgandaGroupMember::where(
            'status',
            'active'
        )->count();
        $pendingGroupMembers = AgandaGroupMember::where(
            'status',
            'pending'
        )->count();

        $totalBonus = AgandaCommission::where(
            'status',
            '!=',
            'cancelled'
        )->sum('amount');
        $pendingBonus = AgandaCommission::where(
            'status',
            'pending'
        )->sum('amount');
        $approvedBonus = AgandaCommission::where(
            'status',
            'approved'
        )->sum('amount');
        $paidBonus = AgandaCommission::where(
            'status',
            'paid'
        )->sum('amount');

        $totalReward = AgandaReward::where(
            'status',
            '!=',
            'cancelled'
        )->sum('amount');
        $pendingReward = AgandaReward::where(
            'status',
            'pending'
        )->sum('amount');
        $approvedReward = AgandaReward::where(
            'status',
            'approved'
        )->sum('amount');
        $paidReward = AgandaReward::where(
            'status',
            'paid'
        )->sum('amount');

        $latestGroups = AgandaGroup::with([
            'owner',
            'packageKegiatan',
        ])
            ->latest()
            ->take(5)
            ->get();

        $latestMembers = AgandaGroupMember::with([
            'calon',
            'registeredBy',
            'group',
        ])
            ->latest()
            ->take(5)
            ->get();

        $groupStatus = AgandaGroup::select(
            'status',
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.dashboard', [
            'totalGroups' => $totalGroups,
            'activeGroups' => $activeGroups,
            'completedGroups' => $completedGroups,
            'cancelledGroups' => $cancelledGroups,
            'totalMembers' => $totalMembers,
            'totalKaryawan' => $totalKaryawan,
            'totalAdmin' => $totalAdmin,
            'totalGroupMembers' => $totalGroupMembers,
            'pendingGroupMembers' => $pendingGroupMembers,
            'totalBonus' => $totalBonus,
            'pendingBonus' => $pendingBonus,
            'approvedBonus' => $approvedBonus,
            'paidBonus' => $paidBonus,
            'totalReward' => $totalReward,
            'pendingReward' => $pendingReward,
            'approvedReward' => $approvedReward,
            'paidReward' => $paidReward,
            'latestGroups' => $latestGroups,
            'latestMembers' => $latestMembers,
            'groupStatus' => $groupStatus,
        ]);
    }
}
