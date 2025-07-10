<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Str;

class AuthController extends Controller
{
public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Key unik berdasarkan IP dan username
        $key = 'login.' . $request->ip() . '.' . Str::lower($request->username);

        // Cek apakah sudah terkena rate limit
        if (RateLimiter::tooManyAttempts($key, $perMinute = 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'login' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        // Cari user
     $user = DB::table('users')->where('username', $request->username)->first();

    if ($user && Hash::check($request->password, $user->password)) {
        session(['user' => $user]);
        RateLimiter::clear($key);
        return redirect()->route('dashboard');
    }

        // Tambahkan hit percobaan gagal
        RateLimiter::hit($key, 60); // Blok selama 60 detik

        return back()->withErrors([
            'login' => 'Username atau password salah.',
        ]);
    }

    public function logout()
    {
        session()->forget('user');
        return redirect('/login');
    }
}
