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
     // Key rate limiter
    $key = 'login.' . $request->ip() . '.' . Str::lower($request->username);

    if (RateLimiter::tooManyAttempts($key, 5)) {
        return back()->withErrors([
            'login' => 'Terlalu banyak percobaan. Silakan coba lagi nanti.'
        ]);
    }

    $user = DB::table('users')
        ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
        ->select('users.*', 'roles.name as role_name')
        ->where('users.username', $request->username)
        ->first();

    if ($user && Hash::check($request->password, $user->password)) {
        session(['user' => $user]);
        RateLimiter::clear($key);
        return redirect()->route('dashboard');
    }

    RateLimiter::hit($key, 60);
    return back()->withErrors(['login' => 'Username atau password salah.']);
    }

    public function logout()
    {
        session()->forget('user');
        return redirect('/login');
    }
}
