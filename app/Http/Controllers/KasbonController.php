<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasbonController extends Controller
{
   // Middleware akan mengatur akses di RouteServiceProvider

public function index()
{
    $user = session('user');

    $query = \App\Models\Kasbon::with('user'); // Pakai Eloquent dan eager load relasi

    if ($user->role_name === 'staff' || $user->role_name === 'spv') {
        $query->where('user_id', $user->id);
    } elseif ($user->role_name === 'hr') {
        $query->whereHas('user', function ($q) {
            $q->whereIn('role_name', ['staff', 'spv']);
        });
    } elseif ($user->role_name === 'direktur') {
        $query->whereHas('user', function ($q) {
            $q->where('role_name', 'hr');
        });
    } elseif ($user->role_name === 'holding') {
        $query->whereHas('user', function ($q) {
            $q->where('role_name', 'direktur');
        });
    }

    $kasbons = $query->get();

    return view('dashboard.kasbon.index', compact('kasbons'));
}


    public function create()
    {
        return view('dashboard.kasbon.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_pengajuan' => 'required|date',
            'jumlah' => 'required|numeric|min:1',
            'keperluan' => 'required|string|max:500'
        ]);

        $user = session('user');

        DB::table('kasbons')->insert([
            'user_id' => $user->id,
            'tanggal_pengajuan' => $request->tanggal_pengajuan,
            'jumlah' => $request->jumlah,
            'keperluan' => $request->keperluan,
            'status' => 'menunggu',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('settings.kasbons.index')->with('success', 'Pengajuan kasbon berhasil diajukan.');
    }

    public function show($id)
    {
        $kasbon = DB::table('kasbons')
            ->where('kasbons.id', $id)
            ->first();

        if (!$kasbon) abort(404);

        return view('dashboard.kasbon.show', compact('kasbon'));
    }

    public function edit($id)
    {
        $kasbon = DB::table('kasbons')->where('id', $id)->first();

        if (!$kasbon) abort(404);

        // Cek apakah user berhak approve
        $currentUser = session('user');

        $canApprove = false;

        if (in_array($currentUser->role_name, ['hr']) && in_array($kasbon->user->role_name, ['staff', 'spv'])) {
            $canApprove = true;
        } elseif ($currentUser->role_name === 'direktur' && $kasbon->user->role_name === 'hr') {
            $canApprove = true;
        } elseif ($currentUser->role_name === 'holding' && $kasbon->user->role_name === 'direktur') {
            $canApprove = true;
        }

        if (!$canApprove) {
            abort(403, 'Anda tidak memiliki izin untuk menyetujui pengajuan ini.');
        }

        return view('dashboard.kasbon.edit', compact('kasbon'));
    }

    public function update(Request $request, $id)
    {
        $kasbon = DB::table('kasbons')->where('id', $id)->first();

        if (!$kasbon) abort(404);

        $currentUser = session('user');

        $canApprove = false;

        if (in_array($currentUser->role_name, ['hr']) && in_array($kasbon->user->role_name, ['staff', 'spv'])) {
            $canApprove = true;
        } elseif ($currentUser->role_name === 'direktur' && $kasbon->user->role_name === 'hr') {
            $canApprove = true;
        } elseif ($currentUser->role_name === 'holding' && $kasbon->user->role_name === 'direktur') {
            $canApprove = true;
        }

        if (!$canApprove) {
            abort(403, 'Anda tidak bisa menyetujui pengajuan ini.');
        }

        // Update status
        DB::table('kasbons')
            ->where('id', $id)
            ->update([
                'status' => $request->input('status'),
                'approved_by' => $currentUser->id,
                'approved_at' => now(),
                'updated_at' => now()
            ]);

        return redirect()->route('settings.kasbons.index')->with('success', 'Status pengajuan berhasil diperbarui.');
    }
}
