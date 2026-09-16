<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgandaGroup;
use App\Models\AgandaGroupMember;
use App\Models\AgandaPair;
use App\Models\AgandaReward;
use App\Models\BonusTransaction;
use App\Models\User;
use Illuminate\Http\Request;

class StructureController extends Controller
{
    public function show(Request $request, AgandaGroup $group)
    {
        $user = $request->user();

        if ($user->role === 'karyawan') {
            if ((int) $group->owner_id !== (int) $user->id) {
                return response()->json([
                    'message' => 'Anda tidak memiliki akses ke group ini.',
                ], 403);
            }
        } elseif ($user->role === 'member') {
            $isMember = AgandaGroupMember::where('group_id', $group->id)
                ->where('calon_id', $user->calon_id)
                ->where('status', 'active')
                ->exists();

            if (!$isMember) {
                return response()->json([
                    'message' => 'Anda bukan anggota aktif group ini.',
                ], 403);
            }
        } else {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke struktur group.',
            ], 403);
        }

        $group->load([
            'owner',
            'packageKegiatan',
        ]);

        if (!$group->owner) {
            return response()->json([
                'message' => 'Owner group tidak ditemukan.',
            ], 404);
        }

        $groupMembers = AgandaGroupMember::where('group_id', $group->id)
            ->where('status', 'active')
            ->get();

        $calonIds = $groupMembers
            ->pluck('calon_id')
            ->filter()
            ->unique()
            ->values();

        $memberUsers = User::whereIn('calon_id', $calonIds)
            ->get()
            ->keyBy('calon_id');

        $registeredByIds = $groupMembers
            ->pluck('registered_by')
            ->filter()
            ->unique()
            ->values();

        $registeredUsers = User::whereIn('id', $registeredByIds)
            ->get()
            ->keyBy('id');

        $usersById = collect();

        foreach ($memberUsers as $memberUser) {
            $usersById->put($memberUser->id, $memberUser);
        }

        foreach ($registeredUsers as $registeredUser) {
            $usersById->put($registeredUser->id, $registeredUser);
        }

        $usersById->put($group->owner->id, $group->owner);

        $childrenMap = [];

        foreach ($groupMembers as $groupMember) {
            $memberUser = $memberUsers->get($groupMember->calon_id);

            if (!$memberUser) {
                continue;
            }

            $parentId = (int) $groupMember->registered_by;

            if (!isset($childrenMap[$parentId])) {
                $childrenMap[$parentId] = [];
            }

            $childrenMap[$parentId][] = $memberUser;
        }

        $pairs = AgandaPair::where('group_id', $group->id)
            ->where('status', 'active')
            ->get();

        $visiblePairs = $pairs;

        $currentUser = $user;

        if ($currentUser->role === 'member') {
            $line1Users = collect(
                $childrenMap[$currentUser->id] ?? []
            );

            $line2Users = collect();

            foreach ($line1Users as $line1User) {
                $line2Users = $line2Users->merge(
                    collect($childrenMap[$line1User->id] ?? [])
                );
            }

            $line2UserIds = $line2Users
                ->unique('id')
                ->pluck('id')
                ->map(fn($id) => (int) $id);

            $visiblePairs = $pairs
                ->filter(function ($pair) use ($line2UserIds) {
                    return $line2UserIds->contains((int) $pair->left_member_id)
                        && $line2UserIds->contains((int) $pair->right_member_id);
                })
                ->values();
        }

        $pairByUserId = [];

        foreach ($visiblePairs as $pair) {
            if ($pair->left_member_id) {
                $pairByUserId[(int) $pair->left_member_id] = $pair;
            }

            if ($pair->right_member_id) {
                $pairByUserId[(int) $pair->right_member_id] = $pair;
            }
        }

        $bonusTransactions = BonusTransaction::where('group_id', $group->id)
            ->where('status', 'confirmed')
            ->whereIn('user_id', $usersById->keys())
            ->get()
            ->groupBy('user_id');

        $rewards = AgandaReward::where('group_id', $group->id)
            ->whereIn('user_id', $usersById->keys())
            ->where('status', '!=', 'cancelled')
            ->get()
            ->groupBy('user_id');

        $formatUser = function ($user) {
            if (!$user) {
                return null;
            }

            return [
                'id' => $user->id,
                'name' => $user->name,
                'member_id' => $user->member_id,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'calon_id' => $user->calon_id,
                'parent_id' => $user->parent_id,
            ];
        };

        $buildNode = function ($nodeUser, $line) use (
            &$buildNode,
            &$childrenMap,
            &$pairByUserId,
            &$bonusTransactions,
            &$rewards,
            $formatUser
        ) {
            $children = collect(
                $childrenMap[$nodeUser->id] ?? []
            )
                ->unique('id')
                ->sortBy('name')
                ->values();

            if ($line >= 2) {
                $children = collect();
            }

            $userBonuses = $bonusTransactions->get(
                $nodeUser->id,
                collect()
            );

            $userRewards = $rewards->get(
                $nodeUser->id,
                collect()
            );

            $line1Bonus = (float) $userBonuses
                ->where('type', 'line_1')
                ->sum('amount');

            $line2Bonus = (float) $userBonuses
                ->where('type', 'line_2_pairing')
                ->sum('amount');

            $pair = $pairByUserId[$nodeUser->id] ?? null;

            return [
                'user' => $formatUser($nodeUser),
                'line' => $line,
                'bonus' => [
                    'total' => (float) $userBonuses->sum('amount'),
                    'line_1' => $line1Bonus,
                    'line_2_pairing' => $line2Bonus,
                ],
                'reward' => [
                    'total' => (float) $userRewards->sum('amount'),
                ],
                'pair' => $pair ? [
                    'id' => $pair->id,
                    'bonus_amount' => (float) $pair->bonus_amount,
                    'position' => (int) $pair->left_member_id === (int) $nodeUser->id
                        ? 'left'
                        : 'right',
                    'left_member_id' => $pair->left_member_id,
                    'right_member_id' => $pair->right_member_id,
                ] : null,
                'children' => $children
                    ->map(function ($child) use ($line, $buildNode) {
                        return $buildNode(
                            $child,
                            $line + 1
                        );
                    })
                    ->values()
                    ->all(),
            ];
        };

        if ($currentUser->role === 'karyawan') {
            $tree = $buildNode(
                $group->owner,
                0
            );

            return response()->json([
                'group' => [
                    'id' => $group->id,
                    'kode_group' => $group->kode_group,
                    'status' => $group->status,
                    'owner' => $formatUser($group->owner),
                    'package' => $group->packageKegiatan ? [
                        'id' => $group->packageKegiatan->id,
                        'name' => $group->packageKegiatan->nama_paket
                            ?? $group->packageKegiatan->name
                            ?? null,
                        'harga' => (float) $group->packageKegiatan->harga,
                        'deposit' => (float) $group->packageKegiatan->deposit,
                        'tanggal_berlangsung' => $group->packageKegiatan->tanggal_berlangsung,
                    ] : null,
                    'total_members' => $groupMembers->count(),
                ],
                'perspective' => [
                    'user' => $formatUser($currentUser),
                    'role' => 'karyawan',
                ],
                'structure' => $tree,
            ]);
        }

        $currentGroupMember = $groupMembers->first(function ($groupMember) use ($currentUser) {
            return (int) $groupMember->calon_id === (int) $currentUser->calon_id;
        });

        if (!$currentGroupMember) {
            return response()->json([
                'message' => 'Anda bukan anggota aktif group ini.',
            ], 403);
        }

        $uplineUser = $usersById->get(
            (int) $currentGroupMember->registered_by
        );

        if (!$uplineUser) {
            return response()->json([
                'message' => 'Upline tidak ditemukan.',
            ], 404);
        }

        $memberTree = $buildNode(
            $currentUser,
            0
        );

        $tree = [
            'user' => $formatUser($uplineUser),
            'line' => null,
            'type' => 'upline',
            'bonus' => [
                'total' => 0,
                'line_1' => 0,
                'line_2_pairing' => 0,
            ],
            'reward' => [
                'total' => 0,
            ],
            'pair' => null,
            'children' => [
                $memberTree,
            ],
        ];

        return response()->json([
            'group' => [
                'id' => $group->id,
                'kode_group' => $group->kode_group,
                'status' => $group->status,
                'owner' => $formatUser($group->owner),
                'package' => $group->packageKegiatan ? [
                    'id' => $group->packageKegiatan->id,
                    'name' => $group->packageKegiatan->nama_paket
                        ?? $group->packageKegiatan->name
                        ?? null,
                    'harga' => (float) $group->packageKegiatan->harga,
                    'deposit' => (float) $group->packageKegiatan->deposit,
                    'tanggal_berlangsung' => $group->packageKegiatan->tanggal_berlangsung,
                ] : null,
                'total_members' => $groupMembers->count(),
            ],
            'perspective' => [
                'user' => $formatUser($currentUser),
                'role' => 'member',
                'upline' => $formatUser($uplineUser),
            ],
            'structure' => $tree,
        ]);
    }
}
