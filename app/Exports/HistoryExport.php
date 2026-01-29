<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HistoryExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;
    protected $status;

    public function __construct(Collection $data, string $status)
    {
        $this->data = $data;
        $this->status = $status;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return match ($this->status) {
            'schedule' => [
                '#',
                'Nama Anak',
                'Nama Ibu',
                'Vaksin',
                'Jadwal Tanggal',
                'Dusun',
                'Posyandu',
            ],
            'jadwal' => [
                '#',
                'Nama Anak',
                'Nama Ibu',
                'Vaksin',
                'Jadwal Mulai',
                'Jadwal Selesai',
                'Dusun',
                'Posyandu',
            ],
            'akan' => [
                '#',
                'Nama Anak',
                'Nama Ibu',
                'Vaksin',
                'Rencana Jadwal',
                'Dusun',
                'Posyandu',
            ],
            'sudah' => [
                '#',
                'Nama Anak',
                'Nama Ibu',
                'Vaksin',
                'Tanggal Vaksin',
                'Posyandu',
                'Status',
                'KIPI',
            ],
            'terlewat' => [
                '#',
                'Nama Anak',
                'Nama Ibu',
                'Vaksin',
                'Seharusnya',
                'Dusun',
                'Posyandu',
                'Status',
            ],
            default => ['#', 'Nama Anak', 'Vaksin'],
        };
    }

    public function map($row): array
    {
        static $index = 0;
        $index++;

        return match ($this->status) {
            'schedule' => [
                $index,
                $row->patient->name ?? '-',
                $row->mother_name ?? $row->patient->mother_name ?? '-',
                $row->vaccine->name ?? '-',
                isset($row->schedule_at) ? \Carbon\Carbon::parse($row->schedule_at)->format('d M Y') : '-',
                $row->patient->village->name ?? '-',
                $row->patient->posyandu->name ?? '-',
            ],
            'jadwal' => [
                $index,
                $row->patient->name ?? '-',
                $row->mother_name ?? $row->patient->mother_name ?? '-',
                $row->vaccine->name ?? '-',
                isset($row->start_date) ? \Carbon\Carbon::parse($row->start_date)->format('d M Y') : '-',
                isset($row->end_date) ? \Carbon\Carbon::parse($row->end_date)->format('d M Y') : '-',
                $row->patient->village->name ?? '-',
                $row->patient->posyandu->name ?? '-',
            ],
            'akan' => [
                $index,
                $row->patient->name ?? '-',
                $row->mother_name ?? $row->patient->mother_name ?? '-',
                $row->vaccine->name ?? '-',
                isset($row->start_date) ? \Carbon\Carbon::parse($row->start_date)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($row->end_date)->format('d M Y') : '-',
                $row->patient->village->name ?? '-',
                $row->patient->posyandu->name ?? '-',
            ],
            'sudah' => [
                $index,
                $row->patient->name ?? '-',
                $row->mother_name ?? $row->patient->mother_name ?? '-',
                $row->vaccine->name ?? '-',
                isset($row->date) ? \Carbon\Carbon::parse($row->date)->format('d M Y') : '-',
                $row->posyandu ?? '-',
                'Selesai',
                $this->formatKipi($row->kipi ?? null),
            ],
            'terlewat' => [
                $index,
                $row->patient->name ?? '-',
                $row->mother_name ?? $row->patient->mother_name ?? '-',
                $row->vaccine->name ?? '-',
                isset($row->start_date) ? \Carbon\Carbon::parse($row->start_date)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($row->end_date)->format('d M Y') : '-',
                $row->patient->village->name ?? '-',
                $row->patient->posyandu->name ?? '-',
                'Terlewat',
            ],
            default => [
                $index,
                $row->patient->name ?? '-',
                $row->vaccine->name ?? '-',
            ],
        };
    }

    protected function formatKipi($kipi): string
    {
        if (empty($kipi)) return '-';
        $data = json_decode($kipi, true);
        if (!is_array($data)) return '-';
        return implode(', ', $data);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
