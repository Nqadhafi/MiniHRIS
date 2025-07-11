@foreach($kasbons as $kasbon)
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
                Oleh: {{ optional($kasbon->approver)->username ?? '-' }}
            </small>
        @endif
    </td>
    <td>
        @php
            $currentUser = session('user');
            $canApprove = false;

            if (
                in_array($kasbon->user->role->name, ['staff', 'spv']) &&
                $currentUser->role_name === 'hr'
            ) {
                $canApprove = true;
            } elseif (
                $kasbon->user->role->name === 'hr' &&
                $currentUser->role_name === 'direktur'
            ) {
                $canApprove = true;
            } elseif (
                $kasbon->user->role->name === 'direktur' &&
                $currentUser->role_name === 'holding'
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
@endforeach
