<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserCanAccessKasbon
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Ambil data user dari session
        $user = auth()->user();

        // Daftar role yang diizinkan akses ke kasbon
        $allowedRoles = ['staff', 'spv', 'hr', 'direktur', 'holding'];

        // Cek apakah user login dan memiliki role yang diizinkan
        if (!$user || !in_array($user->role->name, $allowedRoles)) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
        return $next($request);
    }
}
