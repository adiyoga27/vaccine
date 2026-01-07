<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Village;
use App\Models\Vaccine;
use App\Models\VaccineSchedule;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Villages CRUD
    public function villages()
    {
        $villages = Village::withCount('vaccinePatients')->get();
        return view('dashboard.admin.villages.index', compact('villages'));
    }

    public function storeVillage(Request $request)
    {
        $request->validate(['name' => 'required']);
        Village::create($request->all());
        return back()->with('success', 'Desa berhasil ditambahkan');
    }

    public function updateVillage(Request $request, Village $village)
    {
        $request->validate(['name' => 'required']);
        $village->update($request->all());
        return back()->with('success', 'Desa berhasil diperbarui');
    }

    public function destroyVillage(Village $village)
    {
        $village->delete();
        return back()->with('success', 'Desa berhasil dihapus');
    }

    // Vaccines CRUD
    public function vaccines()
    {
        $vaccines = Vaccine::all();
        return view('dashboard.admin.vaccines.index', compact('vaccines'));
    }

    public function storeVaccine(Request $request)
    {
        $request->validate(['name' => 'required', 'minimum_age' => 'required|integer']);
        Vaccine::create($request->all());
        return back()->with('success', 'Vaksin berhasil ditambahkan');
    }

    public function updateVaccine(Request $request, Vaccine $vaccine)
    {
        $request->validate(['name' => 'required', 'minimum_age' => 'required|integer']);
        $vaccine->update($request->all());
        return back()->with('success', 'Vaksin berhasil diperbarui');
    }

    public function destroyVaccine(Vaccine $vaccine)
    {
        $vaccine->delete();
        return back()->with('success', 'Vaksin berhasil dihapus');
    }

    // Schedules
    public function schedules()
    {
        $schedules = VaccineSchedule::with('village')->latest()->get();
        $villages = Village::all();
        return view('dashboard.admin.schedules.index', compact('schedules', 'villages'));
    }

    public function storeSchedule(Request $request)
    {
        $request->validate([
            'village_id' => 'required|exists:villages,id',
            'scheduled_at' => 'required|date'
        ]);
        VaccineSchedule::create($request->all());
        return back()->with('success', 'Jadwal berhasil dibuat');
    }

    public function destroySchedule(VaccineSchedule $schedule)
    {
        $schedule->delete();
        return back()->with('success', 'Jadwal berhasil dihapus');
    }

    // Monitoring
    public function users()
    {
        $users = User::with('patient')->where('role', 'user')->latest()->paginate(10);
        return view('dashboard.admin.users.index', compact('users'));
    }

    public function logs()
    {
        $logs = Activity::latest()->paginate(20);
        return view('dashboard.admin.logs.index', compact('logs'));
    }

    public function history()
    {
        $histories = VaccinePatient::with(['patient', 'vaccine', 'village'])
                    ->latest()
                    ->paginate(20);
        return view('dashboard.admin.history.index', compact('histories'));
    }
}
