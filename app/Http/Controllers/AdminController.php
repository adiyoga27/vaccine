<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Village;
use App\Models\Vaccine;
use App\Models\VaccineSchedule;
use App\Models\VaccinePatient;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Villages CRUD
    public function villages()
    {
        $villages = Village::withCount('vaccinePatients')->with('posyandus')->get();
        return view('dashboard.admin.villages.index', compact('villages'));
    }

    public function storeVillage(Request $request)
    {
        $request->validate(['name' => 'required']);
        Village::create($request->except(['_token', '_method']));
        return back()->with('success', 'Desa berhasil ditambahkan');
    }

    public function updateVillage(Request $request, Village $village)
    {
        $request->validate(['name' => 'required']);
        $village->update($request->except(['_token', '_method']));
        return back()->with('success', 'Desa berhasil diperbarui');
    }

    public function destroyVillage(Village $village)
    {
        $village->delete();
        return back()->with('success', 'Desa berhasil dihapus');
    }

    // Posyandus CRUD
    public function storePosyandu(Request $request) {
        $request->validate([
            'village_id' => 'required|exists:villages,id',
            'name' => 'required'
        ]);
        \App\Models\Posyandu::create($request->except(['_token', '_method']));
        return back()->with('success', 'Posyandu berhasil ditambahkan');
    }

    public function updatePosyandu(Request $request, \App\Models\Posyandu $posyandu) {
        $request->validate(['name' => 'required']);
        $posyandu->update($request->except(['_token', '_method']));
        return back()->with('success', 'Posyandu berhasil diperbarui');
    }

    public function destroyPosyandu(\App\Models\Posyandu $posyandu) {
        $posyandu->delete();
        return back()->with('success', 'Posyandu berhasil dihapus');
    }

    // Vaccines CRUD
    public function vaccines()
    {
        $vaccines = Vaccine::all();
        return view('dashboard.admin.vaccines.index', compact('vaccines'));
    }

    public function storeVaccine(Request $request)
    {
        $request->validate([
            'name' => 'required', 
            'minimum_age' => 'required|integer',
            'duration_days' => 'required|integer|min:1'
        ]);
        Vaccine::create($request->all());
        return back()->with('success', 'Vaksin berhasil ditambahkan');
    }

    public function updateVaccine(Request $request, Vaccine $vaccine)
    {
        $request->validate([
            'name' => 'required', 
            'minimum_age' => 'required|integer',
            'duration_days' => 'required|integer|min:1'
        ]);
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
        $schedules = VaccineSchedule::with(['village', 'vaccines'])->latest()->get();
        $villages = Village::all();
        $vaccines = Vaccine::all();
        return view('dashboard.admin.schedules.index', compact('schedules', 'villages', 'vaccines'));
    }

    public function storeSchedule(Request $request)
    {
        $request->validate([
            'village_id' => 'required|exists:villages,id',
            'scheduled_at' => 'required|date',
            'vaccine_ids' => 'required|array',
            'vaccine_ids.*' => 'exists:vaccines,id'
        ]);

        $schedule = VaccineSchedule::create([
            'village_id' => $request->village_id,
            'scheduled_at' => $request->scheduled_at
        ]);
        
        $schedule->vaccines()->sync($request->vaccine_ids);
        
        return back()->with('success', 'Jadwal berhasil dibuat');
    }

    public function updateSchedule(Request $request, VaccineSchedule $schedule)
    {
        $request->validate([
            'village_id' => 'required|exists:villages,id',
            'scheduled_at' => 'required|date',
            'vaccine_ids' => 'array',
            'vaccine_ids.*' => 'exists:vaccines,id'
        ]);

        $schedule->update([
            'village_id' => $request->village_id,
            'scheduled_at' => $request->scheduled_at
        ]);

        $schedule->vaccines()->sync($request->input('vaccine_ids', []));

        return back()->with('success', 'Jadwal berhasil diperbarui');
    }

    public function destroySchedule(VaccineSchedule $schedule)
    {
        $schedule->delete();
        return back()->with('success', 'Jadwal berhasil dihapus');
    }

    // Monitoring
    public function users()
    {
        $users = User::with(['patient.vaccinePatients.vaccine'])->where('role', 'user')->latest()->paginate(10);
        $totalVaccines = Vaccine::count();
        return view('dashboard.admin.users.index', compact('users', 'totalVaccines'));
    }

    public function createUser()
    {
        $villages = Village::all();
        return view('dashboard.admin.users.create', compact('villages'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            // Patient Data
            'mother_name' => 'required',
            'date_birth' => 'required|date',
            'address' => 'required',
            'gender' => 'required|in:male,female',
            'village_id' => 'required|exists:villages,id',
            'phone' => 'required',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'role' => 'user'
            ]);

            \App\Models\Patient::create([
                'user_id' => $user->id,
                'village_id' => $request->village_id,
                'name' => $request->name,
                'mother_name' => $request->mother_name,
                'date_birth' => $request->date_birth,
                'address' => $request->address,
                'gender' => $request->gender,
                'phone' => $request->phone,
            ]);
        });

        return redirect()->route('admin.users')->with('success', 'Peserta berhasil didaftarkan');
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
