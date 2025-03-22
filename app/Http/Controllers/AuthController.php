<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.login');
    }
    public function postlogin(Request $request)
    {
        try {
            if ($request->ajax() || $request->wantsJson()) {
                $request->validate([
                    'username' => 'required|min:4|max:20',
                    'password' => 'required|min:6|max:20',
                ]);
                $credentials = $request->only('username', 'password');
                Log::info('Credentials: ', $credentials);
                if (Auth::attempt($credentials)) {
                    Log::info('Login berhasil untuk user: ' . $request->username);
                    return response()->json([
                        'status' => true,
                        'message' => 'Login Berhasil',
                        'redirect' => url('/')
                    ]);
                }
                Log::info('Login gagal untuk user: ' . $request->username);
                return response()->json([
                    'status' => false,
                    'message' => 'Login Gagal!',
                    'msgField' => [
                        'username' => ['Username atau Password salah'],
                        'password' => ['Username atau Password salah']
                    ]
                ]);
            }
            return redirect('login');
        } catch (\Exception $e) {
            Log::error('Error in postlogin: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan di server: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('login')->with('success', 'Logout Berhasil!');
        } catch (\Exception $e) {
            Log::error('Error in logout: ' . $e->getMessage());
            return redirect('login')->with('error', 'Terjadi kesalahan saat logout: ' . $e->getMessage());
        }
    }
}
