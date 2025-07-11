<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnsureUserCanApproveKasbon
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
        // Ambil user dari session
        $user = session('user');

        if (!$user) {
            abort(403, 'Anda harus login untuk melakukan aksi ini.');
        }

        // Ambil ID kasbon dari route
        $kasbonId = $request->route('kasbon');

        // Ambil data kasbon + role_id user pengaju
        $kasbon = DB::table('kasbons')
            ->join('users', 'kasbons.user_id', '=', 'users.id')
            ->select('kasbons.*', 'users.role_id')
            ->where('kasbons.id', $kasbonId)
            ->first();

        if (!$kasbon) {
            abort(404, 'Pengajuan kasbon tidak ditemukan');
        }

        // Ambil semua role dari tabel roles secara dinamis
        $roles = DB::table('roles')->pluck('name', 'id');

        // Dapatkan nama role dari user yang mengajukan kasbon
        $kasbonUserRoleName = $roles[$kasbon->role_id] ?? null;

        // Dapatkan nama role dari user yang login
        $currentUserRoleName = $roles[$user->role_id] ?? null;

        // Cek apakah user berhak approve
        $canApprove = false;

        if (in_array($kasbonUserRoleName, ['staff', 'spv'])) {
            $canApprove = ($currentUserRoleName === 'hr');
        } elseif ($kasbonUserRoleName === 'hr') {
            $canApprove = ($currentUserRoleName === 'direktur');
        } elseif ($kasbonUserRoleName === 'direktur') {
            $canApprove = ($currentUserRoleName === 'holding');
        }

        if (!$canApprove) {
            abort(403, 'Anda tidak memiliki izin untuk menyetujui pengajuan ini.');
        }

        return $next($request);
    }
}
