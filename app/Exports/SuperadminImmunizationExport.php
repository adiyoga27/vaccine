<?php

namespace App\Exports;

use App\Models\Vaccine;
use App\Models\Village;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;

class SuperadminImmunizationExport implements FromView
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
        $office_id = $this->request->input('office_id');

        $vaccines = Vaccine::orderBy('minimum_age')->get();

        $villages = Village::query();
        if ($search) {
            $villages->where('name', 'like', "%{$search}%");
        }
        if ($office_id) {
            $villages->whereHas('offices', function($q) use ($office_id) {
                $q->where('offices.id', $office_id);
            });
        }
        $villages = $villages->get();

        $data = app(\App\Services\ImmunizationReportService::class)->counts($villages, $vaccines, $month, $year);

        return view('dashboard.superadmin.reports.immunization-export', [
            'villages' => $villages,
            'vaccines' => $vaccines,
            'data' => $data,
            'month' => $month,
            'year' => $year,
            'office_id' => $office_id,
            'isExport' => true
        ]);
    }
}
