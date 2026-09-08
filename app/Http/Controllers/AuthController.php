<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        /*
    |--------------------------------------------------------------------------
    | Tentukan apakah input berupa email atau Member ID
    |--------------------------------------------------------------------------
    */

        $field = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'member_id';

        /*
    |--------------------------------------------------------------------------
    | Coba login
    |--------------------------------------------------------------------------
    */

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

        /*
    |--------------------------------------------------------------------------
    | Regenerate session
    |--------------------------------------------------------------------------
    */

        $request->session()->regenerate();

        $user = Auth::user();

        /*
    |--------------------------------------------------------------------------
    | Redirect berdasarkan role
    |--------------------------------------------------------------------------
    */

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'karyawan') {
            return redirect()->route('karyawan.dashboard');
        }

        if ($user->role === 'member') {
            return redirect()->route('member.dashboard');
        }

        /*
    |--------------------------------------------------------------------------
    | Role tidak valid
    |--------------------------------------------------------------------------
    */

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

        return redirect()->route('login');
    }
}
