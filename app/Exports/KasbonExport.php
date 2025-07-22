<?php

namespace App\Exports;

use App\Models\Kasbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KasbonExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $user = Auth::user();
        $query = Kasbon::with(['user.userProfile']);

        // Filter berdasarkan role user
        if ($user->role->name === 'staff' || $user->role->name === 'spv') {
            $query->where('user_id', $user->id);
        } elseif ($user->role->name === 'direktur') {
            $query->whereHas('user', function ($q) {
                $q->where('role_id', 4); // Sesuaikan ID role HR
            });
        } elseif ($user->role->name === 'holding') {
            $query->whereHas('user', function ($q) {
                $q->where('role_id', 5); // Sesuaikan ID role Direktur
            });
        }

        // Status filter
        if ($this->request->status && $this->request->status !== '') {
            $query->where('status', $this->request->status);
        }

        // Tanggal filter
        $startDate = $this->request->start_date ?? now()->startOfMonth();
        $endDate = $this->request->end_date ?? now()->endOfMonth();
        $query->whereBetween('tanggal_pengajuan', [$startDate, $endDate]);

        // Search
        if ($this->request->search) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('keperluan', 'like', "%$search%")
                  ->orWhere('jumlah', 'like', "%$search%")
                  ->orWhereDate('tanggal_pengajuan', 'like', "%$search%");
            });
        }

        // Sorting
        if ($this->request->sort_by && $this->request->sort_order) {
            $query->orderBy($this->request->sort_by, $this->request->sort_order);
        } else {
            $query->latest('tanggal_pengajuan');
        }

        return $query->get();
    }

    /**
     * Mapping data ke baris Excel
     */
    public function map($kasbon): array
    {
    static $number = 0;
    $number++;
        return [
            $number,
            $kasbon->user->userProfile->name ?? 'Tidak Diketahui',
            \Carbon\Carbon::parse($kasbon->tanggal_pengajuan)->format('d-m-Y'),
            'Rp' . number_format($kasbon->jumlah),
            $kasbon->keperluan,
            ucfirst($kasbon->status),
            $kasbon->reason ?? '-',
            optional($kasbon->approver)->userProfile->name ?? '-'
        ];
    }

    /**
     * Header kolom
     */
    public function headings(): array
    {
        return [
            'No.',
            'Nama Pengaju',
            'Tanggal Pengajuan',
            'Jumlah',
            'Keperluan',
            'Status',
            'Alasan',
            'Disetujui Oleh'
        ];
    }

    /**
     * Styling worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Bold header
        ];
    }
}