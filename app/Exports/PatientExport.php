<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PatientExport implements FromCollection, WithHeadings, WithMapping
{
    protected $villageIds;

    public function __construct(array $villageIds)
    {
        $this->villageIds = $villageIds;
    }

    public function collection()
    {
        return User::with(['patient.village', 'patient.posyandu'])
            ->where('role', 'user')
            ->whereHas('patient', function ($q) {
                $q->whereIn('village_id', $this->villageIds);
            })
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Anak',
            'NIK',
            'Nama Ibu',
            'Email',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Alamat',
            'Dusun',
            'Posyandu',
            'No. HP',
        ];
    }

    public function map($user): array
    {
        return [
            $user->patient->name ?? '-',
            $user->patient->nik ?? '-',
            $user->patient->mother_name ?? '-',
            $user->email,
            $user->patient->date_birth ? $user->patient->date_birth->format('Y-m-d') : '-',
            $user->patient->gender == 'male' ? 'Laki-laki' : 'Perempuan',
            $user->patient->address ?? '-',
            $user->patient->village->name ?? '-',
            $user->patient->posyandu->name ?? '-',
            $user->patient->phone ?? '-',
        ];
    }
}
