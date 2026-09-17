<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    private function authorizeAdmin(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(response()->json([
                'message' => 'Hanya admin yang dapat mengakses data pengguna.',
            ], 403));
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin($request);

        $query = User::query()
            ->with('parent')
            ->whereIn('role', ['karyawan', 'member'])
            ->latest('id');

        if ($request->role) {
            $query->where('role', $request->role);
        }

        if ($request->search) {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('member_id', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->get();

        return response()->json([
            'users' => $users->map(function ($user) {
                return $this->formatUser($user);
            })->values(),
        ]);
    }

    public function show(Request $request, User $user)
    {
        $this->authorizeAdmin($request);

        if (!in_array($user->role, ['karyawan', 'member'])) {
            return response()->json([
                'message' => 'Data pengguna tidak tersedia.',
            ], 404);
        }

        $user->load([
            'parent',
            'calon.packageKegiatan',
        ]);

        return response()->json([
            'user' => $this->formatUser($user, true),
        ]);
    }

    private function formatUser(User $user, bool $detail = false)
    {
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'member_id' => $user->member_id,
            'role' => $user->role,
            'phone' => $user->phone,
            'address' => $user->address,
            'avatar' => $user->avatar,
            'calon_id' => $user->calon_id,
            'parent' => $user->parent ? [
                'id' => $user->parent->id,
                'name' => $user->parent->name,
                'member_id' => $user->parent->member_id,
                'role' => $user->parent->role,
            ] : null,
            'created_at' => $user->created_at,
        ];

        if ($detail) {
            $data['calon'] = $user->calon ? [
                'id' => $user->calon->id,
                'nama_lengkap' => $user->calon->nama_lengkap,
                'email' => $user->calon->email,
                'no_telepon' => $user->calon->no_telepon,
                'package' => $user->calon->packageKegiatan ? [
                    'id' => $user->calon->packageKegiatan->id,
                    'name' => $user->calon->packageKegiatan->nama_paket
                        ?? $user->calon->packageKegiatan->name
                        ?? null,
                    'harga' => (float) $user->calon->packageKegiatan->harga,
                    'deposit' => (float) $user->calon->packageKegiatan->deposit,
                ] : null,
            ] : null;
        }

        return $data;
    }
}
