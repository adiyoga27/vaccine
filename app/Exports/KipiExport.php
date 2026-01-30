<?php

namespace App\Exports;

use App\Models\VaccinePatient;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class KipiExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = VaccinePatient::with(['patient', 'vaccine', 'village', 'posyandu'])
            ->whereNotNull('kipi')
            ->where('kipi', '!=', '[]')
            ->where('kipi', '!=', 'null');

        if ($this->request->filled('start_date') && $this->request->filled('end_date')) {
            $query->whereBetween('vaccinated_at', [$this->request->start_date . ' 00:00:00', $this->request->end_date . ' 23:59:59']);
        }

        if ($this->request->filled('kipi_filter')) {
            $query->whereJsonContains('kipi', $this->request->kipi_filter);
        }

        if ($this->request->filled('village_id')) {
            $query->where('village_id', $this->request->village_id);
        }

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        return $query->get();
    }

    public function map($row): array
    {
        $kipiString = is_array($row->kipi) ? implode(', ', $row->kipi) : $row->kipi;

        return [
            $row->vaccinated_at->format('d-m-Y'),
            $row->patient->name,
            $row->patient->mother_name,
            $row->vaccine->name,
            $kipiString,
            $row->village->name ?? '-',
            $row->posyandu->name ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Tanggal Vaksin',
            'Nama Anak',
            'Nama Ibu',
            'Vaksin',
            'Keluhan (KIPI)',
            'Dusun',
            'Posyandu',
        ];
    }
}
