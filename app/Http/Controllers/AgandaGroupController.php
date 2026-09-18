<?php

namespace App\Http\Controllers;

use App\Models\AgandaGroup;
use App\Models\AgandaGroupMember;
use App\Models\AgandaPair;
use App\Models\AgandaReward;
use App\Models\AgandaCommission;
use App\Models\BonusTransaction;
use App\Models\PackageKegiatan;
use App\Models\Calon;
use App\Models\CalonPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class AgandaGroupController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $user = auth()->user();

        $groups = AgandaGroup::query()
            ->with([
                'owner',
                'packageKegiatan',
            ])
            ->withCount([
                'members as total_members' => function ($query) {
                    $query->where('status', 'active');
                },
            ])
            ->when($user->role === 'karyawan', function ($query) use ($user) {
                $query->where('owner_id', $user->id);
            })
            ->when($user->role === 'member', function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('owner_id', $user->id)
                        ->orWhereHas('members', function ($memberQuery) use ($user) {
                            $memberQuery
                                ->where('calon_id', $user->calon_id)
                                ->where('status', 'active');
                        });
                });
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('kode_group', 'like', "%{$search}%")
                        ->orWhereHas('owner', function ($ownerQuery) use ($search) {
                            $ownerQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('member_id', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('aganda.groups.index', compact(
            'groups',
            'search',
            'status'
        ));
    }

    public function create()
    {
        $packages = PackageKegiatan::where('is_active', true)
            ->orderBy('tanggal_berlangsung')
            ->get();

        return view('aganda.groups.create', compact('packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'package_kegiatan_id' => [
                'required',
                'exists:package_kegiatans,id',
            ],
        ]);

        $package = PackageKegiatan::findOrFail(
            $validated['package_kegiatan_id']
        );

        do {
            $kodeGroup = 'AGR-' . strtoupper(Str::random(8));
        } while (
            AgandaGroup::where('kode_group', $kodeGroup)->exists()
        );

        $group = AgandaGroup::create([
            'kode_group' => $kodeGroup,
            'owner_id' => auth()->id(),
            'package_kegiatan_id' => $package->id,
            'status' => 'active',
        ]);

        return redirect()
            ->route('aganda.groups.index')
            ->with(
                'success',
                "Group {$group->kode_group} berhasil dibuat."
            );
    }

    public function show(AgandaGroup $group)
    {
        $this->ensureGroupAccess($group);

        $group->load([
            'owner',
            'packageKegiatan',
            'members.calon',
            'members.registeredBy',
        ]);

        return view('aganda.groups.show', compact('group'));
    }

    public function destroy(AgandaGroup $group)
    {
        abort_unless(auth()->id() === $group->owner_id, 403);

        DB::transaction(function () use ($group) {
            $group->members()->delete();
            $group->delete();
        });

        return redirect()
            ->route('aganda.groups.index')
            ->with('success', 'Group berhasil dihapus.');
    }

    public function createMember(AgandaGroup $group)
    {
        // Ambil calon yang sesuai dengan paket kegiatan group
        // dan belum menjadi anggota group ini
        $calons = Calon::where(
            'package_kegiatan_id',
            $group->package_kegiatan_id
        )
            ->whereNotIn('id', function ($query) use ($group) {
                $query->select('calon_id')
                    ->from('aganda_group_members')
                    ->where('group_id', $group->id);
            })
            ->orderBy('nama_lengkap')
            ->get();

        return view(
            'aganda.groups.members.create',
            compact('group', 'calons')
        );
    }

    public function storeMember(Request $request, AgandaGroup $group)
    {
        $validated = $request->validate([
            'calon_id' => [
                'required',
                'exists:calons,id',
            ],
        ]);

        $calon = Calon::with('packageKegiatan')
            ->findOrFail($validated['calon_id']);

        if (!$calon->packageKegiatan) {
            return back()
                ->withErrors([
                    'calon_id' => 'Paket kegiatan calon tidak ditemukan.',
                ])
                ->withInput();
        }

        if ((int) $calon->package_kegiatan_id !== (int) $group->package_kegiatan_id) {
            return back()
                ->withErrors([
                    'calon_id' => 'Calon tersebut tidak terdaftar pada paket kegiatan group ini.',
                ])
                ->withInput();
        }

        $sudahTerdaftar = AgandaGroupMember::where('group_id', $group->id)
            ->where('calon_id', $calon->id)
            ->exists();

        if ($sudahTerdaftar) {
            return back()
                ->withErrors([
                    'calon_id' => 'Calon tersebut sudah terdaftar sebagai anggota group ini.',
                ])
                ->withInput();
        }

        $existingDp = CalonPayment::where('calon_id', $calon->id)
            ->where('payment_type', 'dp')
            ->where('status', 'paid')
            ->exists();

        if ($existingDp) {
            return back()
                ->withErrors([
                    'calon_id' => 'DP calon tersebut sudah tercatat.',
                ])
                ->withInput();
        }

        $deposit = (float) $calon->packageKegiatan->deposit;
        $packagePrice = (float) $calon->packageKegiatan->harga;

        if ($deposit <= 0) {
            return back()
                ->withErrors([
                    'calon_id' => 'Deposit paket belum tersedia atau bernilai tidak valid.',
                ])
                ->withInput();
        }

        $temporaryPassword = Str::random(10);
        $userBaru = null;
        $sponsor = auth()->user();

        DB::transaction(function () use (
            $calon,
            $group,
            $temporaryPassword,
            $deposit,
            $packagePrice,
            $sponsor,
            &$userBaru
        ) {
            $user = User::where('calon_id', $calon->id)
                ->lockForUpdate()
                ->first();

            if (!$user) {
                do {
                    $memberId = 'AGD-' . strtoupper(Str::random(8));
                } while (
                    User::where('member_id', $memberId)->exists()
                );

                $user = User::create([
                    'member_id' => $memberId,
                    'name' => $calon->nama_lengkap,
                    'email' => $calon->email,
                    'phone' => $calon->no_telepon,
                    'role' => 'member',
                    'parent_id' => $sponsor->id,
                    'calon_id' => $calon->id,
                    'password' => Hash::make($temporaryPassword),
                ]);

                $userBaru = $user;
            } else {
                $user->update([
                    'role' => 'member',
                    'parent_id' => $sponsor->id,
                ]);
            }

            $groupMember = AgandaGroupMember::create([
                'group_id' => $group->id,
                'calon_id' => $calon->id,
                'registered_by' => $sponsor->id,
                'status' => 'active',
            ]);

            $payment = CalonPayment::create([
                'calon_id' => $calon->id,
                'package_kegiatan_id' => $calon->package_kegiatan_id,
                'package_price' => $packagePrice,
                'deposit_amount' => $deposit,
                'payment_type' => 'dp',
                'amount' => $deposit,
                'status' => 'paid',
                'paid_at' => now(),
                'confirmed_by' => $sponsor->id,
                'notes' => 'DP otomatis tercatat saat calon didaftarkan menjadi member.',
            ]);

            BonusTransaction::create([
                'user_id' => $sponsor->id,
                'group_id' => $group->id,
                'source_user_id' => $user->id,
                'source_payment_id' => $payment->id,
                'type' => 'line_1',
                'amount' => 3000000,
                'status' => 'confirmed',
                'description' => 'Komisi Line 1 dari pembayaran DP ' . $user->name,
            ]);
        });

        if ($userBaru) {
            return redirect()
                ->route('aganda.groups.show', $group->id)
                ->with('success', 'Anggota berhasil ditambahkan, DP otomatis tercatat, dan bonus Line 1 berhasil dibuat.')
                ->with('account_info', [
                    'name' => $userBaru->name,
                    'member_id' => $userBaru->member_id,
                    'email' => $userBaru->email,
                    'password' => $temporaryPassword,
                ]);
        }

        return redirect()
            ->route('aganda.groups.show', $group->id)
            ->with(
                'success',
                "{$calon->nama_lengkap} berhasil ditambahkan ke group, DP otomatis tercatat, dan bonus Line 1 berhasil dibuat."
            );
    }

    public function structure(
        AgandaGroup $group,
        string $view = 'aganda.structure.index'
    ) {
        $this->ensureGroupAccess($group);

        $group->load([
            'owner',
            'packageKegiatan',
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

        $registeredByIds = $groupMembers
            ->pluck('registered_by')
            ->filter()
            ->unique()
            ->values();

        $registeredUsers = User::whereIn('id', $registeredByIds)
            ->get()
            ->keyBy('id');

        $owner = $group->owner;

        if (!$owner) {
            abort(404, 'Owner group tidak ditemukan.');
        }

        $usersById = collect();

        foreach ($memberUsers as $memberUser) {
            $usersById->put($memberUser->id, $memberUser);
        }

        foreach ($registeredUsers as $registeredUser) {
            $usersById->put($registeredUser->id, $registeredUser);
        }

        $usersById->put($owner->id, $owner);

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

        if (auth()->user()->role === 'member') {
            $currentUser = auth()->user();

            $currentGroupMember = $groupMembers->first(function ($groupMember) use ($currentUser) {
                return (int) $groupMember->calon_id === (int) $currentUser->calon_id;
            });

            if ($currentGroupMember) {
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

                $visiblePairs = $pairs->filter(function ($pair) use ($line2UserIds) {
                    return $line2UserIds->contains((int) $pair->left_member_id)
                        && $line2UserIds->contains((int) $pair->right_member_id);
                })->values();
            }
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

        $commissions = AgandaCommission::where('group_id', $group->id)
            ->whereIn('user_id', $usersById->keys())
            ->get()
            ->groupBy('user_id');

        $rewards = AgandaReward::where('group_id', $group->id)
            ->whereIn('user_id', $usersById->keys())
            ->where('status', '!=', 'cancelled')
            ->get()
            ->groupBy('user_id');

        $buildNode = function ($user, $line, $type = 'member') use (
            &$buildNode,
            &$childrenMap,
            &$pairByUserId,
            &$commissions,
            &$rewards,
            $group
        ) {
            $children = collect(
                $childrenMap[$user->id] ?? []
            )
                ->unique('id')
                ->sortBy('name')
                ->values();

            if ($line >= 2) {
                $children = collect();
            }

            $pair = $pairByUserId[$user->id] ?? null;

            $userCommissions = $commissions->get(
                $user->id,
                collect()
            );

            $userRewards = $rewards->get(
                $user->id,
                collect()
            );

            $commissionAmount = (int) $userCommissions->sum('amount');

            if ($type === 'member' && $line === 1 && $commissionAmount <= 0) {
                $commissionAmount = 3000000;
            }

            $rewardAmount = (int) $userRewards->sum('amount');

            return [
                'user' => $user,
                'type' => $type,
                'line' => $line,
                'commission' => $commissionAmount,
                'reward' => $rewardAmount,
                'rewards' => $userRewards,
                'recruiter' => $user->parent,
                'pair_id' => $pair?->id,
                'pair_bonus' => $pair?->bonus_amount,
                'pair_position' => $pair
                    ? (
                        (int) $pair->left_member_id === (int) $user->id
                        ? 'left'
                        : 'right'
                    )
                    : null,
                'pairable' => false,
                'admin_view' => false,
                'admin_clickable' => false,
                'admin_url' => null,
                'children' => $children
                    ->map(function ($child) use ($line, $buildNode) {
                        return $buildNode(
                            $child,
                            $line + 1,
                            'member'
                        );
                    })
                    ->values()
                    ->all(),
            ];
        };

        $tree = $buildNode(
            $owner,
            0,
            'owner'
        );

        $currentUser = auth()->user();

        $selectedUser = $currentUser;

        $upline = [];

        if ($currentUser->role === 'member') {
            $currentGroupMember = $groupMembers->first(function ($groupMember) use ($currentUser) {
                return (int) $groupMember->calon_id === (int) $currentUser->calon_id;
            });

            if (!$currentGroupMember) {
                abort(403, 'Anda bukan anggota aktif group ini.');
            }

            $uplineUser = $usersById->get(
                (int) $currentGroupMember->registered_by
            );

            if (!$uplineUser) {
                abort(404, 'Upline tidak ditemukan.');
            }

            $memberUser = $currentUser;

            $memberChildren = collect(
                $childrenMap[$memberUser->id] ?? []
            )
                ->unique('id')
                ->sortBy('name')
                ->values();

            $line1Users = $memberChildren;

            $line2Users = collect();

            foreach ($line1Users as $line1User) {
                $line2Users = $line2Users->merge(
                    collect($childrenMap[$line1User->id] ?? [])
                );
            }

            $line2Users = $line2Users
                ->unique('id')
                ->sortBy('name')
                ->values();

            $buildMemberNode = function ($user, $line) use (
                &$buildMemberNode,
                &$childrenMap,
                &$pairByUserId,
                &$commissions,
                &$rewards
            ) {
                $pair = $pairByUserId[$user->id] ?? null;

                $userCommissions = $commissions->get(
                    $user->id,
                    collect()
                );

                $userRewards = $rewards->get(
                    $user->id,
                    collect()
                );

                $commissionAmount = (int) $userCommissions->sum('amount');

                if ($line === 0) {
                    $commissionAmount = 0;
                }

                $rewardAmount = (int) $userRewards->sum('amount');

                $children = collect();

                if ($line < 2) {
                    $children = collect(
                        $childrenMap[$user->id] ?? []
                    )
                        ->unique('id')
                        ->sortBy('name')
                        ->values();
                }

                return [
                    'user' => $user,
                    'type' => 'member',
                    'line' => $line,
                    'commission' => $commissionAmount,
                    'reward' => $rewardAmount,
                    'rewards' => $userRewards,
                    'recruiter' => $user->parent,
                    'pair_id' => $pair?->id,
                    'pair_bonus' => $pair?->bonus_amount,
                    'pair_position' => $pair
                        ? (
                            (int) $pair->left_member_id === (int) $user->id
                            ? 'left'
                            : 'right'
                        )
                        : null,
                    'pairable' => false,
                    'admin_view' => false,
                    'admin_clickable' => false,
                    'admin_url' => null,
                    'children' => $children
                        ->map(function ($child) use (
                            $line,
                            $buildMemberNode
                        ) {
                            return $buildMemberNode(
                                $child,
                                $line + 1
                            );
                        })
                        ->values()
                        ->all(),
                ];
            };

            $memberTree = $buildMemberNode(
                $memberUser,
                0
            );

            $tree = [
                'user' => $uplineUser,
                'type' => 'upline',
                'line' => null,
                'commission' => 0,
                'reward' => 0,
                'rewards' => collect(),
                'recruiter' => null,
                'pair_id' => null,
                'pair_bonus' => null,
                'pair_position' => null,
                'pairable' => false,
                'admin_view' => false,
                'admin_clickable' => false,
                'admin_url' => null,
                'children' => [
                    $memberTree,
                ],
            ];
        } else {
            $tree = $buildNode(
                $owner,
                0,
                'owner'
            );
        }

        foreach (array_reverse($upline) as $node) {
            $node['children'] = [$tree];
            $tree = $node;
        }

        return view($view, compact(
            'group',
            'tree',
            'pairs',
            'selectedUser',
        ));
    }

    public function structures()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return $this->allStructures();
        }

        if ($user->role === 'karyawan') {
            $group = AgandaGroup::where('owner_id', $user->id)
                ->first();

            if (!$group) {
                abort(404, 'Group tidak ditemukan.');
            }

            return $this->structure(
                $group,
                'aganda.structure.index'
            );
        }

        if ($user->role === 'member') {
            $group = AgandaGroup::whereHas('members', function ($query) use ($user) {
                $query->where('calon_id', $user->calon_id)
                    ->where('status', 'active');
            })->first();

            if (!$group) {
                abort(404, 'Group tidak ditemukan.');
            }

            return $this->structure(
                $group,
                'aganda.structure.member'
            );
        }

        abort(403, 'Anda tidak memiliki akses.');
    }

    public function myStructure()
    {
        $user = auth()->user();

        if ($user->role === 'karyawan') {
            $group = AgandaGroup::where('owner_id', $user->id)
                ->first();

            if (!$group) {
                abort(404, 'Group tidak ditemukan.');
            }

            return $this->structure(
                $group,
                'aganda.structure.index'
            );
        }

        if ($user->role === 'member') {
            $group = AgandaGroup::whereHas('members', function ($query) use ($user) {
                $query->where('calon_id', $user->calon_id)
                    ->where('status', 'active');
            })->first();

            if (!$group) {
                abort(404, 'Group tidak ditemukan.');
            }

            return $this->structure(
                $group,
                'aganda.structure.member'
            );
        }

        abort(403, 'Anda tidak memiliki akses.');
    }

    private function ensureGroupAccess(AgandaGroup $group)
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return;
        }

        if ($user->role === 'karyawan' && $group->owner_id === $user->id) {
            return;
        }

        if ($user->role === 'member') {
            $allowed = $group->owner_id === $user->id
                || $group->members()
                ->where('calon_id', $user->calon_id)
                ->where('status', 'active')
                ->exists();

            if ($allowed) {
                return;
            }
        }

        abort(403, 'Anda tidak memiliki akses ke group ini.');
    }

    public function allStructures()
    {
        $groups = AgandaGroup::with([
            'owner',
            'packageKegiatan',
        ])
            ->orderBy('created_at')
            ->get();

        $structures = $groups->map(function ($group) {
            $groupMembers = AgandaGroupMember::with([
                'calon',
                'registeredBy',
            ])
                ->where('group_id', $group->id)
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

            $usersById = collect();

            foreach ($memberUsers as $memberUser) {
                $usersById->put($memberUser->id, $memberUser);
            }

            if ($group->owner) {
                $usersById->put($group->owner->id, $group->owner);
            }

            $childrenMap = [];

            foreach ($groupMembers as $groupMember) {
                $user = $memberUsers->get($groupMember->calon_id);

                if (!$user) {
                    continue;
                }

                $parentId = (int) $groupMember->registered_by;

                if (!isset($childrenMap[$parentId])) {
                    $childrenMap[$parentId] = [];
                }

                $childrenMap[$parentId][] = $user;
            }

            $pairs = AgandaPair::where('group_id', $group->id)
                ->where('status', 'active')
                ->get();

            $pairByUserId = [];

            foreach ($pairs as $pair) {
                if ($pair->left_member_id) {
                    $pairByUserId[(int) $pair->left_member_id] = $pair;
                }

                if ($pair->right_member_id) {
                    $pairByUserId[(int) $pair->right_member_id] = $pair;
                }
            }

            $commissions = AgandaCommission::where('group_id', $group->id)
                ->whereIn('user_id', $usersById->keys())
                ->get()
                ->groupBy('user_id');

            $rewards = AgandaReward::where('group_id', $group->id)
                ->whereIn('user_id', $usersById->keys())
                ->where('status', '!=', 'cancelled')
                ->get()
                ->groupBy('user_id');

            $buildNode = function ($user, $line, $type = 'member') use (
                &$buildNode,
                &$childrenMap,
                &$pairByUserId,
                &$commissions,
                &$rewards,
                $group
            ) {
                $children = collect(
                    $childrenMap[$user->id] ?? []
                )
                    ->unique('id')
                    ->sortBy('name')
                    ->values();

                $pair = $pairByUserId[$user->id] ?? null;

                $userCommissions = $commissions->get(
                    $user->id,
                    collect()
                );

                $userRewards = $rewards->get(
                    $user->id,
                    collect()
                );

                $commissionAmount = (int) $userCommissions->sum('amount');

                if ($type === 'member' && $line === 1 && $commissionAmount <= 0) {
                    $commissionAmount = 3000000;
                }

                $rewardAmount = (int) $userRewards->sum('amount');

                $isAdminClickable = $type !== 'owner';

                return [
                    'user' => $user,
                    'type' => $type,
                    'line' => $line,
                    'commission' => $commissionAmount,
                    'reward' => $rewardAmount,
                    'rewards' => $userRewards,
                    'recruiter' => $user->parent,
                    'pair_id' => $pair?->id,
                    'pair_bonus' => $pair?->bonus_amount,
                    'pair_position' => $pair
                        ? (
                            (int) $pair->left_member_id === (int) $user->id
                            ? 'left'
                            : 'right'
                        )
                        : null,
                    'pairable' => false,
                    'admin_view' => true,
                    'admin_clickable' => $isAdminClickable,
                    'admin_url' => $isAdminClickable
                        ? route('aganda.admin.member.structure', [
                            'group' => $group->id,
                            'user' => $user->id,
                        ])
                        : null,
                    'children' => $children
                        ->map(function ($child) use ($line, $buildNode) {
                            return $buildNode(
                                $child,
                                $line + 1,
                                'member'
                            );
                        })
                        ->values()
                        ->all(),
                ];
            };

            $tree = $buildNode(
                $group->owner,
                0,
                'owner'
            );

            return [
                'group' => $group,
                'tree' => $tree,
            ];
        });

        return view(
            'aganda.structure.admin',
            compact('structures')
        );
    }

    public function pair(Request $request, AgandaGroup $group)
    {
        $user = auth()->user();

        $this->ensureGroupAccess($group);

        $validated = $request->validate([
            'left_member_id' => ['required', 'integer', 'different:right_member_id'],
            'right_member_id' => ['required', 'integer'],
        ]);

        $leftMemberId = (int) $validated['left_member_id'];
        $rightMemberId = (int) $validated['right_member_id'];

        $groupMembers = AgandaGroupMember::where('group_id', $group->id)
            ->where('status', 'active')
            ->get();

        $memberUsers = User::whereIn(
            'calon_id',
            $groupMembers->pluck('calon_id')->filter()->unique()
        )->get();

        $memberByUserId = $memberUsers->keyBy('id');

        $childrenMap = [];

        foreach ($groupMembers as $groupMember) {
            $member = $memberByUserId->first(function ($item) use ($groupMember) {
                return (int) $item->calon_id === (int) $groupMember->calon_id;
            });

            if (!$member) {
                continue;
            }

            $parentId = (int) $groupMember->registered_by;

            if (!isset($childrenMap[$parentId])) {
                $childrenMap[$parentId] = [];
            }

            $childrenMap[$parentId][] = $member;
        }

        $line1 = collect($childrenMap[$user->id] ?? []);

        $line2 = collect();

        foreach ($line1 as $line1Member) {
            $line2 = $line2->merge(
                collect($childrenMap[$line1Member->id] ?? [])
            );
        }

        $line2 = $line2
            ->unique('id')
            ->values();

        $leftMember = $memberByUserId->get($leftMemberId);
        $rightMember = $memberByUserId->get($rightMemberId);

        if (!$leftMember || !$rightMember) {
            return redirect()
                ->route('aganda.structures.index')
                ->with('error', 'Anggota yang dipilih tidak ditemukan dalam group.');
        }

        if (
            !$line2->contains('id', $leftMemberId) ||
            !$line2->contains('id', $rightMemberId)
        ) {
            return redirect()
                ->route('aganda.structures.index')
                ->with('error', 'Kedua anggota harus berada pada Line 2 Anda.');
        }

        $alreadyPaired = AgandaPair::where('group_id', $group->id)
            ->where('status', 'active')
            ->where(function ($query) use ($leftMemberId, $rightMemberId) {
                $query->where('left_member_id', $leftMemberId)
                    ->orWhere('right_member_id', $leftMemberId)
                    ->orWhere('left_member_id', $rightMemberId)
                    ->orWhere('right_member_id', $rightMemberId);
            })
            ->first();

        if ($alreadyPaired) {
            return back()->with('error', 'Salah satu member sudah pernah dipasangkan dan tidak dapat dipasangkan kembali.');
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
            ->first();

        if ($existingPair) {
            return redirect()
                ->route('aganda.structures.index')
                ->with('error', 'Kedua anggota tersebut sudah dipasangkan.');
        }

        DB::transaction(function () use (
            $group,
            $user,
            $leftMemberId,
            $rightMemberId
        ) {
            $pair = AgandaPair::create([
                'user_id' => $user->id,
                'group_id' => $group->id,
                'left_member_id' => $leftMemberId,
                'right_member_id' => $rightMemberId,
                'bonus_amount' => 500000,
                'status' => 'active',
            ]);

            BonusTransaction::create([
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

            AgandaReward::create([
                'group_id' => $group->id,
                'user_id' => $user->id,
                'amount' => 500000,
                'type' => 'line_2_pairing',
                'status' => 'pending',
            ]);
        });

        return redirect()
            ->route('aganda.structures.index')
            ->with('success', 'Pairing berhasil. Bonus Rp500.000 masuk ke akun Anda.');
    }

    public function adminMemberStructure(AgandaGroup $group, User $user)
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);

        $group->load([
            'owner',
            'packageKegiatan',
        ]);

        if (!$group->owner) {
            abort(404, 'Owner group tidak ditemukan.');
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

        $usersById = collect();

        foreach ($memberUsers as $memberUser) {
            $usersById->put($memberUser->id, $memberUser);
        }

        $usersById->put($group->owner->id, $group->owner);

        $selectedUser = $usersById->get($user->id);

        if (!$selectedUser) {
            abort(404, 'Member tidak ditemukan di group ini.');
        }

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

        $pairByUserId = [];

        foreach ($pairs as $pair) {
            if ($pair->left_member_id) {
                $pairByUserId[(int) $pair->left_member_id] = $pair;
            }

            if ($pair->right_member_id) {
                $pairByUserId[(int) $pair->right_member_id] = $pair;
            }
        }

        $commissions = AgandaCommission::where('group_id', $group->id)
            ->whereIn('user_id', $usersById->keys())
            ->get()
            ->groupBy('user_id');

        $rewards = AgandaReward::where('group_id', $group->id)
            ->whereIn('user_id', $usersById->keys())
            ->where('status', '!=', 'cancelled')
            ->get()
            ->groupBy('user_id');

        $buildNode = function ($currentUser, $line) use (
            &$buildNode,
            &$childrenMap,
            &$pairByUserId,
            &$commissions,
            &$rewards,
            $group
        ) {
            $children = collect(
                $childrenMap[$currentUser->id] ?? []
            )
                ->unique('id')
                ->sortBy('name')
                ->values();

            if ($line >= 2) {
                $children = collect();
            }

            $pair = $pairByUserId[$currentUser->id] ?? null;

            $userCommissions = $commissions->get(
                $currentUser->id,
                collect()
            );

            $userRewards = $rewards->get(
                $currentUser->id,
                collect()
            );

            $commissionAmount = (int) $userCommissions->sum('amount');

            $rewardAmount = (int) $userRewards->sum('amount');

            $isAdminClickable = $currentUser->id !== $group->owner->id;

            return [
                'user' => $currentUser,
                'type' => 'member',
                'line' => $line,
                'commission' => $commissionAmount,
                'reward' => $rewardAmount,
                'rewards' => $userRewards,
                'recruiter' => $currentUser->parent,
                'pair_id' => $pair?->id,
                'pair_bonus' => $pair?->bonus_amount,
                'pair_position' => $pair
                    ? (
                        (int) $pair->left_member_id === (int) $currentUser->id
                        ? 'left'
                        : 'right'
                    )
                    : null,
                'pairable' => false,
                'admin_view' => true,
                'admin_clickable' => $isAdminClickable,
                'admin_url' => $isAdminClickable
                    ? route('aganda.admin.member.structure', [
                        'group' => $group->id,
                        'user' => $currentUser->id,
                    ])
                    : null,
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

        $tree = $buildNode(
            $selectedUser,
            0
        );

        $uplineUser = null;

        foreach ($groupMembers as $groupMember) {
            $memberUser = $memberUsers->get($groupMember->calon_id);

            if (!$memberUser) {
                continue;
            }

            if ((int) $memberUser->id === (int) $selectedUser->id) {
                $parentId = (int) $groupMember->registered_by;

                $uplineUser = $usersById->get($parentId);

                break;
            }
        }

        if ($uplineUser) {
            $upline = [
                'user' => $uplineUser,
                'type' => 'upline',
                'line' => null,
                'commission' => 0,
                'reward' => 0,
                'rewards' => collect(),
                'recruiter' => null,
                'pair_id' => null,
                'pair_bonus' => null,
                'pair_position' => null,
                'pairable' => false,
                'admin_view' => true,
                'admin_clickable' => true,
                'admin_url' => route('aganda.admin.member.structure', [
                    'group' => $group->id,
                    'user' => $uplineUser->id,
                ]),
                'children' => [$tree],
            ];

            $tree = $upline;
        }

        return view('aganda.structure.member', [
            'group' => $group,
            'tree' => $tree,
            'selectedUser' => $selectedUser,
            'pairs' => $pairs,
        ]);
    }
}
