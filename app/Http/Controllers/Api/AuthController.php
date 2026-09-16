<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $validated['login'];

        $user = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? User::where('email', $login)->first()
            : User::where('member_id', $login)->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['Email/Member ID atau password salah.'],
            ]);
        }

        $user->tokens()->delete();

        $token = $user->createToken('aganda-mobile')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'member_id' => $user->member_id,
                'role' => $user->role,
                'phone' => $user->phone,
                'address' => $user->address,
                'avatar' => $user->avatar,
                'calon_id' => $user->calon_id,
                'parent_id' => $user->parent_id,
            ],
        ]);
    }

    public function me(Request $request)
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
                'calon_id' => $user->calon_id,
                'parent_id' => $user->parent_id,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }
}
