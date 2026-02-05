<?php

namespace App\Exports;

use App\Models\Vaccine;
use App\Models\Village;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;

class ImmunizationExport implements FromView
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $month = (int) $this->request->input('month', date('n'));
        $year = (int) $this->request->input('year', date('Y'));
        $search = $this->request->input('search');

        // Reuse Logic (In a real app, extract to Service)
        $vaccines = Vaccine::orderBy('minimum_age')->get();

        $villages = Village::query();
        if ($search) {
            $villages->where('name', 'like', "%{$search}%");
        }
        $villages = $villages->get();

        $data = [];

        foreach ($villages as $village) {
            $records = \App\Models\VaccinePatient::where('village_id', $village->id)
                ->where('status', 'selesai')
                ->whereMonth('vaccinated_at', $month)
                ->whereYear('vaccinated_at', $year)
                ->with('patient')
                ->get();

            $villageData = [];
            foreach ($vaccines as $vaccine) {
                $vaccineRecords = $records->where('vaccine_id', $vaccine->id);
                $male = $vaccineRecords->filter(fn($r) => $r->patient && $r->patient->gender == 'male')->count();
                $female = $vaccineRecords->filter(fn($r) => $r->patient && $r->patient->gender == 'female')->count();

                $villageData[$vaccine->id] = [
                    'L' => $male,
                    'P' => $female
                ];
            }
            $data[$village->id] = $villageData;
        }

        return view('dashboard.admin.reports.immunization-export', [
            'villages' => $villages,
            'vaccines' => $vaccines,
            'data' => $data,
            'month' => $month,
            'year' => $year,
            'isExport' => true // Optional flag if view needs to change slightly for export
        ]);
    }
}
