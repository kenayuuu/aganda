<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PackageKegiatan;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'member')
            ->with([
                'parent',
                'calon.packageKegiatan'
            ]);

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

        $packages = PackageKegiatan::orderBy('id')->get();

        $sponsors = User::whereHas('children', function ($q) {
            $q->where('role', 'member');
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
