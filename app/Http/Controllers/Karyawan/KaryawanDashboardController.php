<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\AgandaCommission;
use App\Models\AgandaGroup;
use App\Models\AgandaGroupMember;
use App\Models\AgandaReward;
use Illuminate\Support\Facades\Auth;

class KaryawanDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $groups = AgandaGroup::where('owner_id', $user->id)
            ->with('packageKegiatan')
            ->withCount([
                'members as members_count' => function ($query) {
                    $query->where('status', 'active');
                },
            ])
            ->latest()
            ->get();

        $groupIds = $groups->pluck('id');

        $totalGroups = $groups->count();

        $totalMembers = AgandaGroupMember::whereIn('group_id', $groupIds)
            ->where('status', 'active')
            ->count();

        $totalBonus = AgandaCommission::where('user_id', $user->id)
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        $totalReward = AgandaReward::where('user_id', $user->id)
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        $latestGroups = $groups->take(5);

        return view('karyawan.dashboard', compact(
            'totalGroups',
            'totalMembers',
            'totalBonus',
            'totalReward',
            'latestGroups'
        ));
    }
}
