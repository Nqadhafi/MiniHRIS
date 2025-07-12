<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Rate limiter key
        $key = 'login:' . $request->ip() . ':' . Str::lower($request->username);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'login' => "Terlalu banyak percobaan. Silakan coba lagi dalam $seconds detik."
            ]);
        }

        // Attempt login using Laravel Auth
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            RateLimiter::clear($key);

            // Optional: ambil dan simpan nama role jika dibutuhkan di session
            $user = User::with('role')->where('id', Auth::id())->first();

            $user->load('role'); // pastikan relasi role ada di model User

            session(['role_name' => $user->role->name ?? '']);

            return redirect()->route('dashboard');
        }

        RateLimiter::hit($key, 60);
        return back()->withErrors(['login' => 'Username atau password salah.']);
    }

    public function logout()
    {
        Auth::logout();
        session()->forget('role_name');
        return redirect('/login');
    }
}
