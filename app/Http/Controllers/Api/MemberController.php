<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgandaGroup;
use App\Models\AgandaGroupMember;
use App\Models\Calon;
use App\Models\CalonPayment;
use App\Models\User;
use App\Models\BonusTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function create(Request $request, AgandaGroup $group)
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'karyawan'])) {
            return response()->json([
                'message' => 'Hanya admin atau karyawan yang dapat melihat calon.',
            ], 403);
        }

        if (
            $user->role !== 'admin'
            && (int) $group->owner_id !== (int) $user->id
        ) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke group ini.',
            ], 403);
        }

        $calons = Calon::with('packageKegiatan')
            ->where('package_kegiatan_id', $group->package_kegiatan_id)
            ->whereNotIn('id', function ($query) use ($group) {
                $query->select('calon_id')
                    ->from('aganda_group_members')
                    ->where('group_id', $group->id);
            })
            ->orderBy('nama_lengkap')
            ->get();

        return response()->json([
            'group' => [
                'id' => $group->id,
                'kode_group' => $group->kode_group,
                'package_kegiatan_id' => $group->package_kegiatan_id,
            ],
            'calons' => $calons->map(function ($calon) {
                $dpPayment = CalonPayment::where('calon_id', $calon->id)
                    ->where('payment_type', 'dp')
                    ->where('status', 'paid')
                    ->latest('id')
                    ->first();

                return [
                    'id' => $calon->id,
                    'nama_lengkap' => $calon->nama_lengkap,
                    'email' => $calon->email,
                    'no_telepon' => $calon->no_telepon,
                    'package_kegiatan_id' => $calon->package_kegiatan_id,
                    'dp_status' => $dpPayment ? $dpPayment->status : null,
                    'dp_amount' => $dpPayment ? (float) $dpPayment->amount : 0,
                    'package' => $calon->packageKegiatan ? [
                        'id' => $calon->packageKegiatan->id,
                        'name' => $calon->packageKegiatan->nama_paket
                            ?? $calon->packageKegiatan->name
                            ?? null,
                        'harga' => (float) $calon->packageKegiatan->harga,
                        'deposit' => (float) $calon->packageKegiatan->deposit,
                    ] : null,
                ];
            })->values(),
        ]);
    }

    public function store(Request $request, AgandaGroup $group)
    {
        $sponsor = $request->user();

        if ($sponsor->role !== 'karyawan') {
            return response()->json([
                'message' => 'Hanya karyawan yang dapat mendaftarkan calon.',
            ], 403);
        }

        if ((int) $group->owner_id !== (int) $sponsor->id) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke group ini.',
            ], 403);
        }

        $validated = $request->validate([
            'calon_id' => [
                'required',
                'exists:calons,id',
            ],
        ]);

        $calon = Calon::with('packageKegiatan')
            ->findOrFail($validated['calon_id']);

        if (!$calon->packageKegiatan) {
            return response()->json([
                'message' => 'Paket kegiatan calon tidak ditemukan.',
            ], 422);
        }

        if (
            (int) $calon->package_kegiatan_id
            !== (int) $group->package_kegiatan_id
        ) {
            return response()->json([
                'message' => 'Calon tersebut tidak terdaftar pada paket kegiatan group ini.',
            ], 422);
        }

        $sudahTerdaftar = AgandaGroupMember::where('group_id', $group->id)
            ->where('calon_id', $calon->id)
            ->exists();

        if ($sudahTerdaftar) {
            return response()->json([
                'message' => 'Calon tersebut sudah terdaftar sebagai anggota group ini.',
            ], 422);
        }

        $deposit = (float) $calon->packageKegiatan->deposit;

        if ($deposit <= 0) {
            return response()->json([
                'message' => 'Deposit paket belum tersedia atau bernilai tidak valid.',
            ], 422);
        }

        $dpPayment = CalonPayment::where('calon_id', $calon->id)
            ->where('payment_type', 'dp')
            ->where('status', 'paid')
            ->latest('id')
            ->first();

        if (!$dpPayment) {
            return response()->json([
                'message' => 'Calon belum melakukan pembayaran DP.',
            ], 422);
        }

        if ((float) $dpPayment->amount < $deposit) {
            return response()->json([
                'message' => 'Nominal DP calon belum memenuhi jumlah deposit paket.',
            ], 422);
        }

        $existingBonus = BonusTransaction::where(
            'source_payment_id',
            $dpPayment->id
        )
            ->where('type', 'line_1')
            ->first();

        if ($existingBonus) {
            return response()->json([
                'message' => 'Bonus Line 1 untuk pembayaran DP ini sudah tercatat.',
            ], 422);
        }

        $temporaryPassword = Str::random(10);

        $memberUser = null;
        $userBaru = false;
        $bonus = null;

        DB::transaction(function () use (
            $calon,
            $group,
            $temporaryPassword,
            $sponsor,
            $dpPayment,
            &$memberUser,
            &$userBaru,
            &$bonus
        ) {
            $memberUser = User::where('calon_id', $calon->id)
                ->lockForUpdate()
                ->first();

            if (!$memberUser) {
                do {
                    $memberId = 'AGD-' . strtoupper(Str::random(8));
                } while (
                    User::where('member_id', $memberId)->exists()
                );

                $memberUser = User::create([
                    'member_id' => $memberId,
                    'name' => $calon->nama_lengkap,
                    'email' => $calon->email,
                    'phone' => $calon->no_telepon,
                    'role' => 'member',
                    'parent_id' => $sponsor->id,
                    'calon_id' => $calon->id,
                    'password' => Hash::make($temporaryPassword),
                ]);

                $userBaru = true;
            } else {
                $memberUser->update([
                    'role' => 'member',
                    'parent_id' => $sponsor->id,
                ]);

                $memberUser->refresh();
            }

            AgandaGroupMember::create([
                'group_id' => $group->id,
                'calon_id' => $calon->id,
                'registered_by' => $sponsor->id,
                'status' => 'active',
            ]);

            $bonus = BonusTransaction::create([
                'user_id' => $sponsor->id,
                'group_id' => $group->id,
                'source_user_id' => $memberUser->id,
                'source_payment_id' => $dpPayment->id,
                'type' => 'line_1',
                'amount' => 3000000,
                'status' => 'confirmed',
                'description' => 'Komisi Line 1 dari pembayaran DP ' . $memberUser->name,
            ]);
        });

        return response()->json([
            'message' => 'Calon berhasil didaftarkan sebagai member dan bonus Line 1 berhasil dibuat.',
            'member' => [
                'id' => $memberUser->id,
                'name' => $memberUser->name,
                'member_id' => $memberUser->member_id,
                'email' => $memberUser->email,
                'phone' => $memberUser->phone,
                'role' => $memberUser->role,
                'calon_id' => $memberUser->calon_id,
                'parent_id' => $memberUser->parent_id,
            ],
            'payment' => [
                'id' => $dpPayment->id,
                'payment_type' => $dpPayment->payment_type,
                'amount' => (float) $dpPayment->amount,
                'status' => $dpPayment->status,
                'paid_at' => $dpPayment->paid_at,
            ],
            'bonus' => [
                'id' => $bonus->id,
                'type' => $bonus->type,
                'amount' => (float) $bonus->amount,
                'status' => $bonus->status,
            ],
            'account' => $userBaru ? [
                'name' => $memberUser->name,
                'member_id' => $memberUser->member_id,
                'email' => $memberUser->email,
                'temporary_password' => $temporaryPassword,
            ] : null,
        ], 201);
    }
}
