@extends('layouts.app')

@section('title', 'Tambah Role')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Role</h1>
        <a href="{{ route('settings.roles.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    @if($errors->any())
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
            <form action="{{ route('settings.roles.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nama Role</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                </div>
                <button type="submit" class="btn btn-primary mt-3">Simpan</button>
            </form>
        </div>
    </div>

</div>
@endsection