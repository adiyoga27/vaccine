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

        $data = app(\App\Services\ImmunizationReportService::class)->counts($villages, $vaccines, $month, $year);

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
