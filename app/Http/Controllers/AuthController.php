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
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // status_aktif = 1 memastikan akun nonaktif tidak bisa login
        $ok = Auth::attempt([
            'username' => $data['username'],
            'password' => $data['password'],
            'status_aktif' => 1,
        ]);

        if (! $ok) {
            return back()->withInput($request->only('username'))
                ->withErrors(['login' => 'Username atau password salah.']);
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('sukses', 'Anda telah keluar.');
    }
}
