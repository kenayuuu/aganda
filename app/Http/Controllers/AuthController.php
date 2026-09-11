<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        $login = $credentials['login'];

        $field = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'member_id';

        if (!Auth::attempt([
            $field => $login,
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {

            return back()
                ->withErrors([
                    'login' => 'Email/Member ID atau password salah.',
                ])
                ->withInput(
                    $request->only('login')
                );
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'karyawan') {
            return redirect()->route('karyawan.dashboard');
        }

        if ($user->role === 'member') {
            return redirect()->route('member.dashboard');
        }

        Auth::logout();

        return back()
            ->withErrors([
                'login' => 'Role akun tidak valid.',
            ])
            ->withInput(
                $request->only('login')
            );
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('homepage');
    }

    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'exists:users,email',
            ],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Email tidak ditemukan dalam sistem.',
        ]);

        $status = Password::sendResetLink([
            'email' => $validated['email'],
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(
                'status',
                'Link reset password berhasil dikirim ke email kamu.'
            );
        }

        return back()->withErrors([
            'email' => __($status),
        ]);
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }
}
