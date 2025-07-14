@extends('layouts.app')

@section('title', 'Daftar Pengajuan Kasbon')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pengajuan Kasbon</h1>
        <a href="{{ route('settings.kasbons.create') }}" class="btn btn-primary">Ajukan Kasbon</a>
    </div>

    <!-- Form Search dan Filter -->
    <form method="GET" action="{{ route('settings.kasbons.index') }}">
        <div class="row mb-4">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Cari Kasbon" value="{{ request()->search }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    @foreach ($statusOptions as $status)
                        <option value="{{ $status }}" {{ request()->status == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="start_date" class="form-control" value="{{ request()->start_date ?: now()->startOfMonth()->format('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="end_date" class="form-control" value="{{ request()->end_date ?: now()->endOfMonth()->format('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <select name="sort_by" class="form-control">
                    <option value="tanggal_pengajuan" {{ request()->sort_by == 'tanggal_pengajuan' ? 'selected' : '' }}>Tanggal</option>
                    <option value="jumlah" {{ request()->sort_by == 'jumlah' ? 'selected' : '' }}>Jumlah</option>
                </select>
            </div>
            <div class="col-md-1">
                <select name="sort_order" class="form-control">
                    <option value="asc" {{ request()->sort_order == 'asc' ? 'selected' : '' }}>Asc</option>
                    <option value="desc" {{ request()->sort_order == 'desc' ? 'selected' : '' }}>Desc</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="{{ route('settings.kasbons.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Jumlah</th>
                            <th>Keperluan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kasbons as $kasbon)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($kasbon->tanggal_pengajuan)->format('d-m-Y') }}</td>
                            <td>Rp{{ number_format($kasbon->jumlah) }}</td>
                            <td>{{ Str::limit($kasbon->keperluan, 50) }}</td>
                            <td>
                                {{ ucfirst($kasbon->status) }}
                                @if ($kasbon->approved_by)
                                    <br>
                                    <small class="text-muted">
                                        Oleh: {{ optional($kasbon->approver)->userProfile->name ?? '-' }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $currentUser = auth()->user();
                                    $canApprove = false;

                                    if (
                                        in_array($kasbon->user->role->name, ['staff', 'spv']) &&
                                        $currentUser->role->name === 'hr'
                                    ) {
                                        $canApprove = true;
                                    } elseif (
                                        $kasbon->user->role->name === 'hr' &&
                                        $currentUser->role->name === 'direktur'
                                    ) {
                                        $canApprove = true;
                                    } elseif (
                                        $kasbon->user->role->name === 'direktur' &&
                                        $currentUser->role->name === 'holding'
                                    ) {
                                        $canApprove = true;
                                    }
                                @endphp

                                @if ($kasbon->status === 'menunggu')
                                    @if ($canApprove)
                                        <a href="{{ route('kasbons.edit' ,$kasbon->id) }}" class="btn btn-sm btn-warning">Proses</a>
                                    @else
                                        <span class="badge badge-secondary">Menunggu</span>
                                    @endif
                                @else
                                    {{ $kasbon->status === 'disetujui' ? '✅ Disetujui' : '❌ Ditolak' }}
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
