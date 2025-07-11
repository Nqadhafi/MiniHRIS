@extends('layouts.app')

@section('title', 'Proses Pengajuan Kasbon')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Proses Pengajuan Kasbon</h1>
        <a href="{{ route('settings.kasbons.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('settings.kasbons.update', $kasbon->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Pengaju</label>
                    <input type="text" class="form-control" value="{{ $kasbon->username }}" readonly>
                </div>

                <div class="form-group">
                    <label>Tanggal Pengajuan</label>
                    <input type="date" name="tanggal_pengajuan" class="form-control" value="{{ $kasbon->tanggal_pengajuan }}" readonly>
                </div>

                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" name="jumlah" class="form-control" value="{{ $kasbon->jumlah }}" readonly>
                </div>

                <div class="form-group">
                    <label>Keperluan</label>
                    <textarea name="keperluan" class="form-control" rows="3" readonly>{{ $kasbon->keperluan }}</textarea>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="disetujui" {{ $kasbon->status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ $kasbon->status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Update Status</button>
            </form>
        </div>
    </div>

</div>
@endsection