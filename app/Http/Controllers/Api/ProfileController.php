<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'member_id' => $user->member_id,
                'role' => $user->role,
                'phone' => $user->phone,
                'address' => $user->address,
                'avatar' => $user->avatar,
                'avatar_url' => $user->avatar
                    ? Storage::disk('public')->url($user->avatar)
                    : null,
                'calon_id' => $user->calon_id,
                'parent_id' => $user->parent_id,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $validated['avatar'] = $request->file('avatar')->store(
                'avatars',
                'public'
            );
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        $user->refresh();

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'member_id' => $user->member_id,
                'role' => $user->role,
                'phone' => $user->phone,
                'address' => $user->address,
                'avatar' => $user->avatar,
                'avatar_url' => $user->avatar
                    ? Storage::disk('public')->url($user->avatar)
                    : null,
                'calon_id' => $user->calon_id,
                'parent_id' => $user->parent_id,
            ],
        ]);
    }
}
