<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgandaGroupMember;
use App\Models\PackageKegiatan;
use App\Models\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = User::where('role', 'member')
            ->with([
                'parent',
                'calon.packageKegiatan',
            ]);

        if ($user->role === 'karyawan') {
            $managedCalonIds = AgandaGroupMember::query()
                ->where('status', 'active')
                ->whereHas('group', function ($groupQuery) use ($user) {
                    $groupQuery->where('owner_id', $user->id);
                })
                ->select('calon_id');

            $query->whereIn('calon_id', $managedCalonIds);
        }

        if ($request->filled('package')) {
            $query->whereHas('calon', function ($q) use ($request) {
                $q->where('package_kegiatan_id', $request->package);
            });
        }

        if ($request->filled('sponsor')) {
            $query->where('parent_id', $request->sponsor);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('member_id', 'like', "%{$search}%");
            });
        }

        $members = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $packages = PackageKegiatan::query()
            ->when($user->role === 'karyawan', function ($packageQuery) use ($user) {
                $packageQuery->whereIn('id', function ($groupQuery) use ($user) {
                    $groupQuery->select('package_kegiatan_id')
                        ->from('aganda_groups')
                        ->where('owner_id', $user->id);
                });
            })
            ->orderBy('id')
            ->get();

        $sponsors = User::whereHas('children', function ($q) {
            $q->where('role', 'member');
        })
            ->when($user->role === 'karyawan', function ($sponsorQuery) use ($user) {
                $sponsorQuery->where(function ($scopeQuery) use ($user) {
                    $scopeQuery->whereKey($user->id)
                        ->orWhereIn('id', function ($memberQuery) use ($user) {
                            $memberQuery->select('users.id')
                                ->from('users')
                                ->join('aganda_group_members', 'aganda_group_members.calon_id', '=', 'users.calon_id')
                                ->join('aganda_groups', 'aganda_groups.id', '=', 'aganda_group_members.group_id')
                                ->where('users.role', 'member')
                                ->where('aganda_group_members.status', 'active')
                                ->where('aganda_groups.owner_id', $user->id);
                        });
                });
            })
            ->orderBy('name')
            ->get();

        return view('admin.memberlist', compact(
            'members',
            'packages',
            'sponsors'
        ));
    }
}
