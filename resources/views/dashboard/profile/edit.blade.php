@extends('layouts.app')

@section('title', 'Pengaturan Profil')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pengaturan Profil</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Profil</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Nama Lengkap</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $profile->name ?? '') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Nomor Telepon</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $profile->phone ?? '') }}">
                        </div>

                        <div class="form-group">
                            <label for="address">Alamat</label>
                            <textarea name="address" id="address" class="form-control" rows="3">{{ old('address', $profile->address ?? '') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="avatar">Foto Profil</label><br>
                            {{-- @dd($profile) --}}
                            @if($profile->avatar ?? '')
                                <img src="{{ asset($profile->avatar) }}" alt="Avatar" width="100" class="mb-2 img-thumbnail">
                            @else
                                <span class="text-muted">Tidak ada foto profil</span>
                            @endif
                            <input type="file" name="avatar" id="avatar" class="form-control-file mt-2">
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection