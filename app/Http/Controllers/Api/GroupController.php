<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgandaGroup;
use App\Models\AgandaGroupMember;
use App\Models\PackageKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

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
                $query->where(function ($query) use ($user) {
                    $query->where('owner_id', $user->id)
                        ->orWhereHas('members', function ($memberQuery) use ($user) {
                            $memberQuery
                                ->where('calon_id', $user->calon_id)
                                ->where('status', 'active');
                        });
                });
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('kode_group', 'like', "%{$search}%")
                        ->orWhereHas('owner', function ($ownerQuery) use ($search) {
                            $ownerQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('member_id', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->get();

        return response()->json([
            'groups' => $groups->map(function ($group) {
                return $this->formatGroup($group);
            }),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'karyawan') {
            return response()->json([
                'message' => 'Hanya karyawan yang dapat membuat group.',
            ], 403);
        }

        $validated = $request->validate([
            'package_kegiatan_id' => [
                'required',
                'exists:package_kegiatans,id',
            ],
        ]);

        $package = PackageKegiatan::where('id', $validated['package_kegiatan_id'])
            ->where('is_active', true)
            ->first();

        if (!$package) {
            return response()->json([
                'message' => 'Paket kegiatan tidak aktif atau tidak ditemukan.',
            ], 422);
        }

        do {
            $kodeGroup = 'AGR-' . strtoupper(Str::random(8));
        } while (
            AgandaGroup::where('kode_group', $kodeGroup)->exists()
        );

        $group = AgandaGroup::create([
            'kode_group' => $kodeGroup,
            'owner_id' => $user->id,
            'package_kegiatan_id' => $package->id,
            'status' => 'active',
        ]);

        $group->load([
            'owner',
            'packageKegiatan',
        ]);

        return response()->json([
            'message' => "Group {$group->kode_group} berhasil dibuat.",
            'group' => $this->formatGroup($group),
        ], 201);
    }

    public function show(Request $request, AgandaGroup $group)
    {
        $access = $this->checkGroupAccess($request->user(), $group);

        if (!$access) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke group ini.',
            ], 403);
        }

        $group->load([
            'owner',
            'packageKegiatan',
            'members.calon',
            'members.registeredBy',
        ]);

        return response()->json([
            'group' => $this->formatGroup($group),
            'members' => $group->members
                ->where('status', 'active')
                ->values()
                ->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'calon_id' => $member->calon_id,
                        'status' => $member->status,
                        'registered_by' => $member->registered_by,
                        'registered_by_user' => $member->registeredBy ? [
                            'id' => $member->registeredBy->id,
                            'name' => $member->registeredBy->name,
                            'member_id' => $member->registeredBy->member_id,
                        ] : null,
                        'calon' => $member->calon ? [
                            'id' => $member->calon->id,
                            'nama_lengkap' => $member->calon->nama_lengkap,
                            'email' => $member->calon->email,
                            'no_telepon' => $member->calon->no_telepon,
                        ] : null,
                    ];
                }),
        ]);
    }

    public function destroy(Request $request, AgandaGroup $group)
    {
        $user = $request->user();

        if ($user->role !== 'karyawan' || (int) $group->owner_id !== (int) $user->id) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menghapus group ini.',
            ], 403);
        }

        DB::transaction(function () use ($group) {
            $group->members()->delete();
            $group->delete();
        });

        return response()->json([
            'message' => 'Group berhasil dihapus.',
        ]);
    }

    private function checkGroupAccess($user, AgandaGroup $group)
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'karyawan') {
            return (int) $group->owner_id === (int) $user->id;
        }

        if ($user->role === 'member') {
            return (int) $group->owner_id === (int) $user->id
                || $group->members()
                ->where('calon_id', $user->calon_id)
                ->where('status', 'active')
                ->exists();
        }

        return false;
    }

    private function formatGroup(AgandaGroup $group)
    {
        return [
            'id' => $group->id,
            'kode_group' => $group->kode_group,
            'status' => $group->status,
            'owner' => $group->owner ? [
                'id' => $group->owner->id,
                'name' => $group->owner->name,
                'member_id' => $group->owner->member_id,
                'role' => $group->owner->role,
            ] : null,
            'package' => $group->packageKegiatan ? [
                'id' => $group->packageKegiatan->id,
                'name' => $group->packageKegiatan->nama_paket ?? $group->packageKegiatan->name ?? null,
                'harga' => $group->packageKegiatan->harga,
                'deposit' => $group->packageKegiatan->deposit,
                'tanggal_berlangsung' => $group->packageKegiatan->tanggal_berlangsung,
            ] : null,
            'total_members' => (int) ($group->total_members ?? 0),
            'created_at' => $group->created_at,
        ];
    }
}
