@extends('layouts.app')

@section('title', 'Daftar Pengajuan Kasbon')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pengajuan Kasbon</h1>
        @if (in_array(session('user')->role_name, ['staff', 'spv']))
            <a href="{{ route('settings.kasbons.create') }}" class="btn btn-primary">Ajukan Kasbon</a>
        @endif
    </div>

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
                            <th>ID</th>
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
                            <td>{{ $kasbon->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($kasbon->tanggal_pengajuan)->format('d-m-Y') }}</td>
                            <td>Rp{{ number_format($kasbon->jumlah) }}</td>
                            <td>{{ Str::limit($kasbon->keperluan, 50) }}</td>
                            <td>
                                {{ ucfirst($kasbon->status) }}
                                @if ($kasbon->approved_by)
                                    <br>
                                    <small class="text-muted">
                                        Oleh: {{ optional($kasbon->approver)->username ?? '-' }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $currentUser = session('user');
                                    $canApprove = false;
                                    // dd($kasbon->user);
                                    if (
                                        in_array($kasbon->user->role_name, ['staff', 'spv']) &&
                                        $currentUser->role_name === 'hr'
                                    ) {
                                        $canApprove = true;
                                    } elseif (
                                        $kasbon->user->role_name === 'hr' &&
                                        $currentUser->role_name === 'direktur'
                                    ) {
                                        $canApprove = true;
                                    } elseif (
                                        $kasbon->user->role_name === 'direktur' &&
                                        $currentUser->role_name === 'holding'
                                    ) {
                                        $canApprove = true;
                                    }
                                @endphp

                                @if ($kasbon->status === 'menunggu')
                                    @if ($canApprove)
                                        <a href="{{ route('settings.kasbons.edit', $kasbon->id) }}" class="btn btn-sm btn-warning">Proses</a>
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