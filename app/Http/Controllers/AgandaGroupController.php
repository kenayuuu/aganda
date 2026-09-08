<?php

namespace App\Http\Controllers;

use App\Models\AgandaGroup;
use App\Models\PackageKegiatan;
use App\Models\Calon;
use App\Models\AgandaGroupMember;
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

        $calon = Calon::findOrFail($validated['calon_id']);

        // Pastikan calon berasal dari paket yang sama dengan group
        if ($calon->package_kegiatan_id != $group->package_kegiatan_id) {
            return back()
                ->withErrors([
                    'calon_id' => 'Calon tersebut tidak terdaftar pada paket kegiatan group ini.',
                ])
                ->withInput();
        }

        // Pastikan calon belum menjadi anggota group ini
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

        $temporaryPassword = Str::random(10);

        $userBaru = null;

        DB::transaction(function () use (
            $calon,
            $group,
            $temporaryPassword,
            &$userBaru
        ) {

            $user = User::where('calon_id', $calon->id)->first();

            if (!$user) {

                // Generate Member ID unik
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

                    // Sponsor/recruiter otomatis adalah user yang sedang login
                    'parent_id' => auth()->id(),

                    // Hubungkan akun dengan data calon
                    'calon_id' => $calon->id,

                    'password' => Hash::make($temporaryPassword),
                ]);

                $userBaru = $user;
            }

            AgandaGroupMember::create([
                'group_id' => $group->id,

                'calon_id' => $calon->id,

                // Yang mendaftarkan tetap user yang sedang login
                'registered_by' => auth()->id(),

                'status' => 'active',
            ]);
        });

        if ($userBaru) {

            return redirect()
                ->route('aganda.groups.show', $group->id)
                ->with('success', 'Anggota berhasil ditambahkan dan akun AGANDA berhasil dibuat.')
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
                "{$calon->nama_lengkap} berhasil ditambahkan ke group."
            );
    }

    public function structure(AgandaGroup $group)
    {
        $this->ensureGroupAccess($group);

        $group->load([
            'owner',
            'packageKegiatan',
        ]);

        $groupMembers = AgandaGroupMember::with('calon')
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

        foreach ($memberUsers as $user) {
            $usersById->put($user->id, $user);
        }

        foreach ($registeredUsers as $user) {
            $usersById->put($user->id, $user);
        }

        $usersById->put($owner->id, $owner);

        $childrenMap = [];

        foreach ($groupMembers as $groupMember) {
            $user = $memberUsers->get($groupMember->calon_id);

            if (!$user) {
                continue;
            }

            $parentId = $groupMember->registered_by;

            if (!isset($childrenMap[$parentId])) {
                $childrenMap[$parentId] = [];
            }

            $childrenMap[$parentId][] = $user;
        }

        $buildNode = function ($user, $line, $type = 'member') use (
            &$buildNode,
            &$childrenMap
        ) {
            $children = collect(
                $childrenMap[$user->id] ?? []
            )->unique('id')
                ->sortBy('name')
                ->values();

            return [
                'user' => $user,
                'type' => $type,
                'line' => $line,
                'recruiter' => null,
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

        $upline = [];

        $current = $owner->parent;

        while ($current) {
            $upline[] = [
                'user' => $current,
                'type' => 'upline',
                'line' => null,
                'recruiter' => null,
                'children' => [],
            ];

            $current = $current->parent;
        }

        $upline = array_reverse($upline);

        foreach ($upline as $node) {
            $node['children'] = [$tree];
            $tree = $node;
        }

        return view('aganda.structure.index', compact(
            'group',
            'tree'
        ));
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
}
