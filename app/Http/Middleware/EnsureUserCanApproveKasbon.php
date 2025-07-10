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
                // Ambil user yang sedang login
        $user = session('user');

        // Ambil ID kasbon dari URL
        $kasbonId = $request->route('kasbon');

        // Ambil data kasbon dari database
        $kasbon = DB::table('kasbons')
            ->leftJoin('users', 'kasbons.user_id', '=', 'users.id')
            ->select('kasbons.*', 'users.role_name')
            ->where('kasbons.id', $kasbonId)
            ->first();

        if (!$kasbon) {
            abort(404, 'Pengajuan kasbon tidak ditemukan');
        }

        // Cek apakah user berhak approve
        $canApprove = false;

        if ($kasbon->role_name === 'staff' || $kasbon->role_name === 'spv') {
            $canApprove = $user->role_name === 'hr';
        } elseif ($kasbon->role_name === 'hr') {
            $canApprove = $user->role_name === 'direktur';
        } elseif ($kasbon->role_name === 'direktur') {
            $canApprove = $user->role_name === 'holding';
        }

        if (!$canApprove) {
            abort(403, 'Anda tidak memiliki izin untuk menyetujui pengajuan ini.');
        }
        return $next($request);
    }
}
