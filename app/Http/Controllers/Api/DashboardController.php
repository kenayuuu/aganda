<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgandaCommission;
use App\Models\AgandaGroup;
use App\Models\AgandaGroupMember;
use App\Models\AgandaReward;
use App\Models\BonusAllocation;
use App\Models\BonusTransaction;
use App\Models\BonusWithdrawal;
use App\Models\Calon;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return $this->adminDashboard();
        }

        if ($user->role === 'karyawan') {
            return $this->karyawanDashboard($user);
        }

        if ($user->role === 'member') {
            return $this->memberDashboard($user);
        }

        return response()->json([
            'message' => 'Role pengguna tidak dikenali.',
        ], 403);
    }

    private function adminDashboard()
    {
        $totalKaryawan = User::where('role', 'karyawan')->count();

        $totalMember = User::where('role', 'member')->count();

        $totalCalon = Calon::count();

        $totalGroup = AgandaGroup::count();

        $activeGroup = AgandaGroup::where('status', 'active')->count();

        $activeGroupMember = AgandaGroupMember::where('status', 'active')->count();

        $totalBonus = (float) BonusTransaction::where('status', 'confirmed')
            ->sum('amount');

        $totalAllocated = (float) BonusAllocation::sum('amount');

        $availableBonus = max(
            0,
            $totalBonus - $totalAllocated
        );

        $pendingWithdrawal = BonusWithdrawal::where('status', 'pending')
            ->count();

        $approvedWithdrawal = BonusWithdrawal::where('status', 'approved')
            ->count();

        $paidWithdrawal = BonusWithdrawal::where('status', 'paid')
            ->count();

        $pendingWithdrawalAmount = (float) BonusWithdrawal::where('status', 'pending')
            ->sum('amount');

        $approvedWithdrawalAmount = (float) BonusWithdrawal::where('status', 'approved')
            ->sum('amount');

        $paidWithdrawalAmount = (float) BonusWithdrawal::where('status', 'paid')
            ->sum('amount');

        $latestGroups = AgandaGroup::with([
            'owner',
            'packageKegiatan',
        ])
            ->latest('id')
            ->take(5)
            ->get()
            ->map(function ($group) {
                return [
                    'id' => $group->id,
                    'kode_group' => $group->kode_group,
                    'nama_group' => $group->nama_group,
                    'status' => $group->status,
                    'owner' => $group->owner ? [
                        'id' => $group->owner->id,
                        'name' => $group->owner->name,
                        'member_id' => $group->owner->member_id,
                    ] : null,
                    'package' => $group->packageKegiatan ? [
                        'id' => $group->packageKegiatan->id,
                        'name' => $group->packageKegiatan->nama,
                        'harga' => (float) $group->packageKegiatan->harga,
                        'deposit' => (float) $group->packageKegiatan->deposit,
                    ] : null,
                    'created_at' => $group->created_at,
                ];
            })
            ->values();

        $latestMembers = User::whereIn('role', ['karyawan', 'member'])
            ->with('calon')
            ->latest('id')
            ->take(5)
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'member_id' => $member->member_id,
                    'role' => $member->role,
                    'calon_id' => $member->calon_id,
                    'created_at' => $member->created_at,
                ];
            })
            ->values();

        return response()->json([
            'role' => 'admin',
            'summary' => [
                'total_karyawan' => $totalKaryawan,
                'total_member' => $totalMember,
                'total_calon' => $totalCalon,
                'total_group' => $totalGroup,
                'active_group' => $activeGroup,
                'active_group_member' => $activeGroupMember,
                'total_bonus' => $totalBonus,
                'total_allocated' => $totalAllocated,
                'available_bonus' => $availableBonus,
            ],
            'withdrawals' => [
                'pending_count' => $pendingWithdrawal,
                'approved_count' => $approvedWithdrawal,
                'paid_count' => $paidWithdrawal,
                'pending_amount' => $pendingWithdrawalAmount,
                'approved_amount' => $approvedWithdrawalAmount,
                'paid_amount' => $paidWithdrawalAmount,
            ],
            'latest_groups' => $latestGroups,
            'latest_members' => $latestMembers,
        ]);
    }

    private function karyawanDashboard($user)
    {
        $groups = AgandaGroup::where('owner_id', $user->id)
            ->with('packageKegiatan')
            ->withCount([
                'members as active_members_count' => function ($query) {
                    $query->where('status', 'active');
                },
            ])
            ->latest('id')
            ->get();

        $commission = (float) BonusTransaction::where('user_id', $user->id)
            ->where('type', 'line_1')
            ->where('status', 'confirmed')
            ->sum('amount');

        $reward = (float) BonusTransaction::where('user_id', $user->id)
            ->where('type', 'line_2_pairing')
            ->where('status', 'confirmed')
            ->sum('amount');

        $totalBonus = (float) BonusTransaction::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->sum('amount');

        return response()->json([
            'role' => 'karyawan',
            'summary' => [
                'total_group' => $groups->count(),
                'total_member' => $groups->sum('active_members_count'),
                'total_commission' => $commission,
                'total_reward' => $reward,
                'total_bonus' => $totalBonus,
            ],
            'groups' => $groups->map(function ($group) {
                return [
                    'id' => $group->id,
                    'kode_group' => $group->kode_group,
                    'nama_group' => $group->nama_group,
                    'status' => $group->status,
                    'active_members_count' => $group->active_members_count,
                    'package' => $group->packageKegiatan ? [
                        'id' => $group->packageKegiatan->id,
                        'name' => $group->packageKegiatan->nama,
                        'harga' => (float) $group->packageKegiatan->harga,
                        'deposit' => (float) $group->packageKegiatan->deposit,
                    ] : null,
                ];
            })->values(),
        ]);
    }

    private function memberDashboard($user)
    {
        $totalBonus = (float) BonusTransaction::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->sum('amount');

        $line1Bonus = (float) BonusTransaction::where('user_id', $user->id)
            ->where('type', 'line_1')
            ->where('status', 'confirmed')
            ->sum('amount');

        $line2Bonus = (float) BonusTransaction::where('user_id', $user->id)
            ->where('type', 'line_2_pairing')
            ->where('status', 'confirmed')
            ->sum('amount');

        $adjustmentBonus = (float) BonusTransaction::where('user_id', $user->id)
            ->where('type', 'adjustment')
            ->where('status', 'confirmed')
            ->sum('amount');

        $totalAllocated = (float) BonusAllocation::where('user_id', $user->id)
            ->sum('amount');

        $availableBonus = max(
            0,
            $totalBonus - $totalAllocated
        );

        $directMember = User::where('parent_id', $user->id)
            ->whereIn('role', ['karyawan', 'member'])
            ->count();

        $groups = AgandaGroup::whereHas('members', function ($query) use ($user) {
            $query->where('calon_id', $user->calon_id)
                ->where('status', 'active');
        })
            ->with('packageKegiatan')
            ->latest('id')
            ->get();

        return response()->json([
            'role' => 'member',
            'summary' => [
                'total_bonus' => $totalBonus,
                'line_1_bonus' => $line1Bonus,
                'line_2_bonus' => $line2Bonus,
                'adjustment_bonus' => $adjustmentBonus,
                'total_allocated' => $totalAllocated,
                'available_bonus' => $availableBonus,
                'direct_member' => $directMember,
            ],
            'groups' => $groups->map(function ($group) {
                return [
                    'id' => $group->id,
                    'kode_group' => $group->kode_group,
                    'nama_group' => $group->nama_group,
                    'status' => $group->status,
                    'package' => $group->packageKegiatan ? [
                        'id' => $group->packageKegiatan->id,
                        'name' => $group->packageKegiatan->nama,
                        'harga' => (float) $group->packageKegiatan->harga,
                        'deposit' => (float) $group->packageKegiatan->deposit,
                    ] : null,
                ];
            })->values(),
        ]);
    }
}
