<?php

namespace App\Http\Controllers;

use App\Models\Kasbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasbonController extends Controller
{
   // Middleware akan mengatur akses di RouteServiceProvider

public function index(Request $request)
{
    $user = auth()->user();

    // Ambil data kasbon dengan relasi user
    $query = Kasbon::with('user');

    if ($user->role->name === 'staff' || $user->role->name === 'spv') {
        $query->where('user_id', $user->id);
    }elseif ($user->role->name === 'direktur') {
        $query->whereHas('user', function ($q) {
            $q->where('role_id', 4);
        });
    } elseif ($user->role->name === 'holding') {
        $query->whereHas('user', function ($q) {
            $q->where('role_id', 5);
        });
    }

    // Filter berdasarkan status kasbon jika ada
    if ($request->has('status') && $request->status != '') {
        $query->where('status', $request->status);
    }

    // Filter berdasarkan range tanggal pengajuan
    if ($request->has('start_date') && $request->has('end_date')) {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $query->whereBetween('tanggal_pengajuan', [$startDate, $endDate]);
    } else {
        // Jika tidak ada filter, defaultkan ke bulan ini
        $startOfMonth = now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = now()->endOfMonth()->format('Y-m-d');
        $query->whereBetween('tanggal_pengajuan', [$startOfMonth, $endOfMonth]);
    }

    // Fitur pencarian
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('keperluan', 'like', "%$search%")
              ->orWhere('jumlah', 'like', "%$search%")
              ->orWhereDate('tanggal_pengajuan', 'like', "%$search%");
        });
    }

    // Sorting berdasarkan parameter jika ada
    if ($request->has('sort_by') && $request->has('sort_order')) {
        $query->orderBy($request->sort_by, $request->sort_order);
    }

    $kasbons = $query->get()->sortByDesc('tanggal_pengajuan');

    // Kirimkan role user dan data filter ke view
    $canApproveKasbon = in_array($user->role_name, ['hr', 'direktur', 'holding']);
    
    // Mengirim data untuk filter (opsi status)
    $statusOptions = ['menunggu', 'disetujui', 'ditolak'];

    return view('dashboard.kasbon.index', compact('kasbons', 'canApproveKasbon', 'statusOptions'));
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

        $user = auth()->user();

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
    // Ambil data kasbon + user info
    $kasbon = DB::table('kasbons')
        ->join('users', 'kasbons.user_id', '=', 'users.id')
        ->select('kasbons.*', 'users.role_id', 'users.username')
        ->where('kasbons.id', $id)
        ->first();

    if (!$kasbon) {
        abort(404);
    }

    // Ambil semua role dari database
    $roles = DB::table('roles')->pluck('name', 'id');

    $currentUser = auth()->user();

    if (!$currentUser) {
        abort(403, 'Anda harus login untuk mengakses halaman ini.');
    }

    // Dapatkan nama role
    $kasbonUserRoleName = $roles[$kasbon->role_id] ?? null;
    $currentUserRoleName = $roles[$currentUser->role->id] ?? null;

    // Cek apakah user berhak approve
    $canApprove = false;

    if (in_array($kasbonUserRoleName, ['staff', 'spv']) && $currentUserRoleName === 'hr') {
        $canApprove = true;
    } elseif ($kasbonUserRoleName === 'hr' && $currentUserRoleName === 'direktur') {
        $canApprove = true;
    } elseif ($kasbonUserRoleName === 'direktur' && $currentUserRoleName === 'holding') {
        $canApprove = true;
    }

    if (!$canApprove) {
        abort(403, 'Anda tidak memiliki izin untuk menyetujui pengajuan ini.');
    }

    return view('dashboard.kasbon.edit', compact('kasbon'));
}

    public function update(Request $request, $id)
    {
        // Ambil data kasbon + role user
        $kasbon = DB::table('kasbons')
            ->join('users', 'kasbons.user_id', '=', 'users.id')
            ->select('kasbons.*', 'users.role_id')
            ->where('kasbons.id', $id)
            ->first();

        if (!$kasbon) {
            abort(404, 'Pengajuan kasbon tidak ditemukan.');
        }

        // Ambil semua role
        $roles = DB::table('roles')->pluck('name', 'id');

        $currentUser = auth()->user();

        if (!$currentUser) {
            abort(403, 'Anda harus login untuk melakukan aksi ini.');
        }

        $kasbonUserRoleName = $roles[$kasbon->role_id] ?? null;
        $currentUserRoleName = $roles[$currentUser->role->id] ?? null;

        // Cek apakah user berhak approve
        $canApprove = false;

        if (in_array($kasbonUserRoleName, ['staff', 'spv']) && $currentUserRoleName === 'hr') {
            $canApprove = true;
        } elseif ($kasbonUserRoleName === 'hr' && $currentUserRoleName === 'direktur') {
            $canApprove = true;
        } elseif ($kasbonUserRoleName === 'direktur' && $currentUserRoleName === 'holding') {
            $canApprove = true;
        }

        if (!$canApprove) {
            abort(403, 'Anda tidak bisa menyetujui pengajuan ini.');
        }

        // Update status kasbon
        DB::table('kasbons')
            ->where('id', $id)
            ->update([
                'reason' => $request->input('reason', null),
                'jumlah' => $request->input('jumlah', $kasbon->jumlah),
                'status' => $request->input('status'),
                'approved_by' => $currentUser->id,
                'approved_at' => now(),
                'updated_at' => now()
            ]);

        return redirect()->route('settings.kasbons.index')->with('success', 'Status pengajuan berhasil diperbarui.');
    }
}
