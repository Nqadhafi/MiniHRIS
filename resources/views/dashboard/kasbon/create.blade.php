@extends('layouts.app')

@section('title', 'Ajukan Kasbon Baru')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ajukan Kasbon Baru</h1>
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
            <form action="{{ route('settings.kasbons.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="tanggal_pengajuan">Tanggal Pengajuan</label>
                    <input type="date" name="tanggal_pengajuan" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="jumlah">Jumlah</label>
                    <input type="number" name="jumlah" class="form-control" placeholder="Masukkan jumlah kasbon" required>
                </div>
                <div class="form-group">
                    <label for="keperluan">Keperluan</label>
                    <textarea name="keperluan" class="form-control" rows="3" placeholder="Contoh: Biaya transportasi minggu ini" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Simpan Pengajuan</button>
            </form>
        </div>
    </div>

</div>
@endsection