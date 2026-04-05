<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\User;
use App\Models\Village;
use App\Models\Vaccine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'offices' => Office::count(),
            'admins' => User::where('role', 'admin')->count(),
            'villages' => Village::count(),
        ];

        $offices = Office::withCount(['villages', 'admins'])->latest()->get();

        return view('dashboard.superadmin.index', compact('stats', 'offices'));
    }

    // ==============================
    // Office CRUD
    // ==============================

    public function offices()
    {
        $offices = Office::withCount(['villages', 'admins'])->latest()->get();
        return view('dashboard.superadmin.offices.index', compact('offices'));
    }

    public function storeOffice(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
        ]);

        Office::create($request->only(['name', 'address']));
        return back()->with('success', 'Kantor berhasil ditambahkan');
    }

    public function updateOffice(Request $request, Office $office)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
        ]);

        $office->update($request->only(['name', 'address']));
        return back()->with('success', 'Kantor berhasil diperbarui');
    }

    public function destroyOffice(Office $office)
    {
        // Unassign admins from this office first
        User::where('office_id', $office->id)->update(['office_id' => null]);
        $office->delete();
        return back()->with('success', 'Kantor berhasil dihapus');
    }

    // ==============================
    // Office-Village Assignment
    // ==============================

    public function manageOfficeVillages(Office $office)
    {
        $villages = Village::orderBy('name')->get();
        $assignedVillageIds = $office->villages()->pluck('villages.id')->toArray();

        return view('dashboard.superadmin.offices.villages', compact('office', 'villages', 'assignedVillageIds'));
    }

    public function updateOfficeVillages(Request $request, Office $office)
    {
        $request->validate([
            'village_ids' => 'nullable|array',
            'village_ids.*' => 'exists:villages,id',
        ]);

        $office->villages()->sync($request->input('village_ids', []));
        return redirect()->route('superadmin.offices')->with('success', 'Dusun untuk kantor "' . $office->name . '" berhasil diperbarui');
    }

    // ==============================
    // Admin-Office Assignment
    // ==============================

    public function admins()
    {
        $admins = User::where('role', 'admin')->with('office')->get();
        $offices = Office::orderBy('name')->get();

        return view('dashboard.superadmin.admins.index', compact('admins', 'offices'));
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'office_id' => 'nullable|exists:offices,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'office_id' => $request->office_id,
        ]);

        return back()->with('success', 'Administrator berhasil ditambahkan');
    }

    public function updateAdminOffice(Request $request, User $admin)
    {
        $request->validate([
            'office_id' => 'nullable|exists:offices,id',
        ]);

        if ($admin->role !== 'admin') {
            return back()->with('error', 'User ini bukan admin');
        }

        $admin->update(['office_id' => $request->office_id]);
        return back()->with('success', 'Kantor untuk admin "' . $admin->name . '" berhasil diperbarui');
    }

    public function updateAdmin(Request $request, User $admin)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'password' => 'nullable|string|min:6|confirmed',
            'office_id' => 'nullable|exists:offices,id',
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->office_id = $request->office_id;

        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();
        return back()->with('success', 'Administrator berhasil diperbarui');
    }

    public function destroyAdmin(User $admin)
    {
        if ($admin->role !== 'admin') {
            return back()->with('error', 'User ini bukan admin');
        }

        $admin->delete();
        return back()->with('success', 'Administrator berhasil dihapus');
    }

    // ==============================
    // Village (Dusun) CRUD
    // ==============================

    public function villages()
    {
        $villages = Village::withCount(['patients', 'vaccinePatients'])->with('posyandus')
            ->orderBy('name')
            ->get();
        return view('dashboard.superadmin.villages.index', compact('villages'));
    }

    public function storeVillage(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Village::create($request->only(['name']));
        return back()->with('success', 'Dusun berhasil ditambahkan');
    }

    public function updateVillage(Request $request, Village $village)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $village->update($request->only(['name']));
        return back()->with('success', 'Dusun berhasil diperbarui');
    }

    public function destroyVillage(Village $village)
    {
        // Check if there are patients in this village
        if ($village->patients()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus dusun yang masih memiliki data peserta');
        }

        $village->delete();
        return back()->with('success', 'Dusun berhasil dihapus');
    }

    // ==============================
    // Vaccine (Jenis Vaksin) CRUD
    // ==============================

    public function vaccines()
    {
        $vaccines = Vaccine::orderBy('minimum_age')->get();
        return view('dashboard.superadmin.vaccines.index', compact('vaccines'));
    }

    public function storeVaccine(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'minimum_age' => 'required|integer|min:0',
            'duration_days' => 'required|integer|min:1',
            'is_required' => 'nullable|boolean'
        ]);

        $data = $request->all();
        $data['is_required'] = $request->has('is_required');

        Vaccine::create($data);
        return back()->with('success', 'Jenis vaksin berhasil ditambahkan');
    }

    public function updateVaccine(Request $request, Vaccine $vaccine)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'minimum_age' => 'required|integer|min:0',
            'duration_days' => 'required|integer|min:1',
            'is_required' => 'nullable|boolean'
        ]);

        $data = $request->all();
        $data['is_required'] = $request->has('is_required');

        $vaccine->update($data);
        return back()->with('success', 'Jenis vaksin berhasil diperbarui');
    }

    public function destroyVaccine(Vaccine $vaccine)
    {
        // Check if there are patient records using this vaccine
        if ($vaccine->vaccinePatients()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus jenis vaksin yang sudah digunakan dalam riwayat peserta');
        }

        $vaccine->delete();
        return back()->with('success', 'Jenis vaksin berhasil dihapus');
    }

    // ==============================
    // Reports
    // ==============================

    public function immunizationReport(Request $request)
    {
        $month = (int) $request->input('month', date('n'));
        $year = (int) $request->input('year', date('Y'));
        $search = $request->input('search');
        $office_id = $request->input('office_id');

        // Get All Vaccines for columns
        $vaccines = Vaccine::orderBy('minimum_age')->get();

        // Get Offices for filter
        $offices = Office::orderBy('name')->get();

        // Get Villages
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

        // Aggregate Data
        $data = [];

        foreach ($villages as $village) {
            // Get completed vaccinations for this village in the selected month/year
            $records = \App\Models\VaccinePatient::where('village_id', $village->id)
                ->where('status', 'selesai')
                ->whereMonth('vaccinated_at', $month)
                ->whereYear('vaccinated_at', $year)
                ->with('patient')
                ->get();

            $villageData = [];
            foreach ($vaccines as $vaccine) {
                // Filter records for this vaccine
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

        return view('dashboard.superadmin.reports.immunization', compact('villages', 'vaccines', 'data', 'month', 'year', 'offices', 'office_id'));
    }

    public function exportImmunization(Request $request)
    {
        $filename = 'laporan_capaian_vaksinasi_superadmin_' . date('Y-m-d_His') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SuperadminImmunizationExport($request), $filename);
    }
}
