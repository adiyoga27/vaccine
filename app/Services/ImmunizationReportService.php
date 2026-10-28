<?php

namespace App\Services;

use App\Models\VaccinePatient;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ImmunizationReportService
{
    /**
     * Hitung vaksinasi selesai per dusun, vaksin, dan jenis kelamin dalam satu query.
     *
     * @return array<int, array<int, array{L: int, P: int}>> [village_id][vaccine_id] => ['L' => n, 'P' => n]
     */
    public function counts(Collection $villages, Collection $vaccines, int $month, int $year): array
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $rows = VaccinePatient::query()
            ->join('patients', 'patients.id', '=', 'vaccine_patients.patient_id')
            ->whereNull('patients.deleted_at')
            ->whereIn('vaccine_patients.village_id', $villages->pluck('id'))
            ->where('vaccine_patients.status', 'selesai')
            ->whereBetween('vaccine_patients.vaccinated_at', [$start, $end])
            ->whereIn('patients.gender', ['male', 'female'])
            ->groupBy('vaccine_patients.village_id', 'vaccine_patients.vaccine_id', 'patients.gender')
            ->selectRaw('vaccine_patients.village_id, vaccine_patients.vaccine_id, patients.gender, COUNT(*) as total')
            ->get();

        $data = [];
        foreach ($villages as $village) {
            foreach ($vaccines as $vaccine) {
                $data[$village->id][$vaccine->id] = ['L' => 0, 'P' => 0];
            }
        }

        foreach ($rows as $row) {
            if (isset($data[$row->village_id][$row->vaccine_id])) {
                $key = $row->gender === 'male' ? 'L' : 'P';
                $data[$row->village_id][$row->vaccine_id][$key] = (int) $row->total;
            }
        }

        return $data;
    }
}
