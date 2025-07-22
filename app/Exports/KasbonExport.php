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
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

class KasbonExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
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
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Ambil request untuk filter
                $request = $this->request;

                // Format tanggal
                $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->format('d-m-Y') : 'awal';
                $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('d-m-Y') : 'akhir';

                // Sisipkan baris kosong dulu agar ada ruang
                $sheet->insertNewRowBefore(1, 4);

                // Merge cell untuk header
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'LAPORAN PENGAJUAN KASBON SHABAT PRINTING');

                $sheet->mergeCells('A2:H2');
                $sheet->setCellValue('A2', "Periode: {$startDate} s.d. {$endDate}");

                $sheet->mergeCells('A3:H3');
                $sheet->setCellValue('A3', "Tanggal Export: " . now()->format('d F Y, H:i'));

                // Style header
                $sheet->getDelegate()->getStyle('A1:A3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // Atur tinggi baris
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(18);
                $sheet->getRowDimension(3)->setRowHeight(18);

                // Update nomor kolom heading (karena sekarang data mulai dari baris ke-5)
                $sheet->setAutoFilter('A5:H5');
            },
        ];
    }


    /**
     * Styling worksheet
     */
    public function styles(Worksheet $sheet)
    {
    return [
        5 => ['font' => ['bold' => true]], // Baris ke-5 adalah header kolom
    ];
    }
}