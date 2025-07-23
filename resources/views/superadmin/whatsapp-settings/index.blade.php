@extends('layouts.app')
@section('title', 'Pengaturan WhatsApp')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pengaturan WhatsApp</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="POST" action="{{ route('settings.whatsapp-settings.update') }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Service Provider</label>
                    <select name="service_provider" class="form-control" required>
                        <option value="fontee" {{ $setting->service_provider == 'fontee' ? 'selected' : '' }}>Fontee</option>
                        <option value="zaviago" {{ $setting->service_provider == 'zaviago' ? 'selected' : '' }}>Zaviago</option>
                        <option value="restqa" {{ $setting->service_provider == 'restqa' ? 'selected' : '' }}>RestQA</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>API Key</label>
                    <input type="text" name="api_key" class="form-control" value="{{ old('api_key', $setting->api_key) }}" required>
                </div>

                <div class="form-group">
                    <label>Nomor Pengirim (contoh: 6281234567890)</label>
                    <input type="text" name="sender_phone" class="form-control" value="{{ old('sender_phone', $setting->sender_phone) }}" required>
                </div>

                <div class="form-group">
                    <label>Jeda Antar Pesan (detik)</label>
                    <input type="number" name="delay_between_messages" class="form-control" min="1" max="30" value="{{ old('delay_between_messages', $setting->delay_between_messages) }}" required>
                    <small class="text-muted">Rekomendasi: 2–5 detik untuk hindari spam.</small>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
            </form>
        </div>
    </div>
</div>
@endsection