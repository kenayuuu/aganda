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
use Illuminate\Support\Facades\DB;

class PairingController extends Controller
{
    public function create(Request $request, AgandaGroup $group)
    {
        $user = $request->user();

        if ($user->role !== 'member') {
            return response()->json([
                'message' => 'Hanya member yang dapat melakukan pairing.',
            ], 403);
        }

        $isMember = AgandaGroupMember::where('group_id', $group->id)
            ->where('calon_id', $user->calon_id)
            ->where('status', 'active')
            ->exists();

        if (!$isMember) {
            return response()->json([
                'message' => 'Anda bukan anggota aktif group ini.',
            ], 403);
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

        $line1 = collect(
            $childrenMap[$user->id] ?? []
        );

        $line2 = collect();

        foreach ($line1 as $line1User) {
            $line2 = $line2->merge(
                collect($childrenMap[$line1User->id] ?? [])
            );
        }

        $line2 = $line2
            ->unique('id')
            ->sortBy('name')
            ->values();

        $alreadyPairedUserIds = AgandaPair::where('group_id', $group->id)
            ->where('status', 'active')
            ->get()
            ->flatMap(function ($pair) {
                return [
                    $pair->left_member_id,
                    $pair->right_member_id,
                ];
            })
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        $availableLine2 = $line2
            ->filter(function ($member) use ($alreadyPairedUserIds) {
                return !$alreadyPairedUserIds->contains((int) $member->id);
            })
            ->values();

        return response()->json([
            'group' => [
                'id' => $group->id,
                'kode_group' => $group->kode_group,
            ],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'member_id' => $user->member_id,
            ],
            'bonus_amount' => 500000,
            'line_2' => $availableLine2->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'member_id' => $member->member_id,
                    'calon_id' => $member->calon_id,
                ];
            })->values(),
        ]);
    }

    public function store(Request $request, AgandaGroup $group)
    {
        $user = $request->user();

        if ($user->role !== 'member') {
            return response()->json([
                'message' => 'Hanya member yang dapat melakukan pairing.',
            ], 403);
        }

        $isMember = AgandaGroupMember::where('group_id', $group->id)
            ->where('calon_id', $user->calon_id)
            ->where('status', 'active')
            ->exists();

        if (!$isMember) {
            return response()->json([
                'message' => 'Anda bukan anggota aktif group ini.',
            ], 403);
        }

        $validated = $request->validate([
            'left_member_id' => [
                'required',
                'integer',
                'different:right_member_id',
            ],
            'right_member_id' => [
                'required',
                'integer',
                'different:left_member_id',
            ],
        ]);

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

        $line1 = collect(
            $childrenMap[$user->id] ?? []
        );

        $line2 = collect();

        foreach ($line1 as $line1User) {
            $line2 = $line2->merge(
                collect($childrenMap[$line1User->id] ?? [])
            );
        }

        $line2 = $line2
            ->unique('id')
            ->values();

        $leftMemberId = (int) $validated['left_member_id'];
        $rightMemberId = (int) $validated['right_member_id'];

        $leftMember = $line2->firstWhere('id', $leftMemberId);
        $rightMember = $line2->firstWhere('id', $rightMemberId);

        if (!$leftMember || !$rightMember) {
            return response()->json([
                'message' => 'Kedua anggota harus berada tepat dua level di bawah Anda.',
            ], 422);
        }

        $alreadyPaired = AgandaPair::where('group_id', $group->id)
            ->where('status', 'active')
            ->where(function ($query) use ($leftMemberId, $rightMemberId) {
                $query
                    ->where('left_member_id', $leftMemberId)
                    ->orWhere('right_member_id', $leftMemberId)
                    ->orWhere('left_member_id', $rightMemberId)
                    ->orWhere('right_member_id', $rightMemberId);
            })
            ->exists();

        if ($alreadyPaired) {
            return response()->json([
                'message' => 'Salah satu member sudah pernah dipasangkan dan tidak dapat dipasangkan kembali.',
            ], 422);
        }

        $existingPair = AgandaPair::where('group_id', $group->id)
            ->where('status', 'active')
            ->where(function ($query) use ($leftMemberId, $rightMemberId) {
                $query
                    ->where(function ($query) use ($leftMemberId, $rightMemberId) {
                        $query
                            ->where('left_member_id', $leftMemberId)
                            ->where('right_member_id', $rightMemberId);
                    })
                    ->orWhere(function ($query) use ($leftMemberId, $rightMemberId) {
                        $query
                            ->where('left_member_id', $rightMemberId)
                            ->where('right_member_id', $leftMemberId);
                    });
            })
            ->exists();

        if ($existingPair) {
            return response()->json([
                'message' => 'Kedua anggota tersebut sudah dipasangkan.',
            ], 422);
        }

        $pair = null;
        $bonus = null;
        $reward = null;

        DB::transaction(function () use (
            $group,
            $user,
            $leftMemberId,
            $rightMemberId,
            &$pair,
            &$bonus,
            &$reward
        ) {
            $pair = AgandaPair::create([
                'user_id' => $user->id,
                'group_id' => $group->id,
                'left_member_id' => $leftMemberId,
                'right_member_id' => $rightMemberId,
                'bonus_amount' => 500000,
                'status' => 'active',
            ]);

            $bonus = BonusTransaction::create([
                'user_id' => $user->id,
                'group_id' => $group->id,
                'source_user_id' => $leftMemberId,
                'source_payment_id' => null,
                'type' => 'line_2_pairing',
                'amount' => 500000,
                'status' => 'confirmed',
                'description' => 'Bonus Line 2 dari pairing ' .
                    $leftMemberId . ' dan ' . $rightMemberId,
            ]);

            $reward = AgandaReward::create([
                'group_id' => $group->id,
                'user_id' => $user->id,
                'amount' => 500000,
                'type' => 'line_2_pairing',
                'status' => 'pending',
            ]);
        });

        return response()->json([
            'message' => 'Pairing berhasil dan bonus Rp500.000 berhasil ditambahkan.',
            'pairing' => [
                'id' => $pair->id,
                'group_id' => $pair->group_id,
                'left_member_id' => $pair->left_member_id,
                'right_member_id' => $pair->right_member_id,
                'bonus_amount' => (float) $pair->bonus_amount,
                'status' => $pair->status,
            ],
            'bonus' => [
                'id' => $bonus->id,
                'type' => $bonus->type,
                'amount' => (float) $bonus->amount,
                'status' => $bonus->status,
            ],
            'reward' => [
                'id' => $reward->id,
                'amount' => (float) $reward->amount,
                'type' => $reward->type,
                'status' => $reward->status,
            ],
        ], 201);
    }
}
