<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Village;
use App\Models\Vaccine;
use App\Models\VaccineSchedule;
use App\Models\VaccinePatient;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // Villages CRUD
    public function villages()
    {
        $villages = Village::withCount(['patients', 'vaccinePatients'])->with('posyandus')->get();
        return view('dashboard.admin.villages.index', compact('villages'));
    }

    public function getVillagePatients(Village $village)
    {
        $patients = $village->patients()
            ->select('name', 'mother_name', 'gender', 'phone', 'address')
            ->get();

        return response()->json($patients);
    }

    public function storeVillage(Request $request)
    {
        $request->validate(['name' => 'required']);
        Village::create($request->except(['_token', '_method']));
        return back()->with('success', 'Dusun berhasil ditambahkan');
    }

    public function updateVillage(Request $request, Village $village)
    {
        $request->validate(['name' => 'required']);
        $village->update($request->except(['_token', '_method']));
        return back()->with('success', 'Dusun berhasil diperbarui');
    }

    public function destroyVillage(Village $village)
    {
        $village->delete();
        return back()->with('success', 'Dusun berhasil dihapus');
    }

    // Posyandus CRUD
    public function storePosyandu(Request $request)
    {
        $request->validate([
            'village_id' => 'required|exists:villages,id',
            'name' => 'required'
        ]);
        \App\Models\Posyandu::create($request->except(['_token', '_method']));
        return back()->with('success', 'Posyandu berhasil ditambahkan');
    }

    public function updatePosyandu(Request $request, \App\Models\Posyandu $posyandu)
    {
        $request->validate(['name' => 'required']);
        $posyandu->update($request->except(['_token', '_method']));
        return back()->with('success', 'Posyandu berhasil diperbarui');
    }

    public function destroyPosyandu(\App\Models\Posyandu $posyandu)
    {
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
    public function users(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with(['patient.vaccinePatients.vaccine', 'patient.village'])
                ->where('role', 'user')
                ->select('users.*');

            return \Yajra\DataTables\Facades\DataTables::of($users)
                ->addColumn('checkbox', function ($user) {
                    return '<input type="checkbox" value="' . $user->id . '" class="user-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">';
                })
                ->addColumn('peserta', function ($user) {
                    $gender = $user->patient && $user->patient->gender == 'male' ? 'Laki-laki' : 'Perempuan';
                    $name = $user->patient->name ?? '-';
                    return '<div class="text-sm font-bold text-gray-900">' . $name . '</div>
                            <div class="text-xs text-gray-500">' . $gender . '</div>';
                })
                ->addColumn('orang_tua', function ($user) {
                    return '<div class="text-sm text-gray-900">' . ($user->patient->mother_name ?? '-') . '</div>';
                })
                ->addColumn('nik', function ($user) {
                    return '<div class="text-sm text-gray-500">' . ($user->patient->nik ?? '-') . '</div>';
                })
                ->addColumn('usia', function ($user) {
                    if (!$user->patient)
                        return '<span class="text-gray-400">-</span>';
                    return '<div class="text-sm text-gray-900">' . $user->patient->date_birth->format('d M Y') . '</div>
                            <div class="text-xs text-gray-500">' . $user->patient->date_birth->age . ' Tahun</div>';
                })
                ->addColumn('alamat', function ($user) {
                    $village = $user->patient->village->name ?? '-';
                    $address = $user->patient->address ?? '-';
                    return '<div class="text-sm text-gray-900">' . $address . '</div>
                            <div class="text-xs text-gray-600">' . $village . '</div>';
                })
                ->addColumn('posyandu', function ($user) {
                    return '<div class="text-sm text-gray-900">' . ($user->patient->posyandu->name ?? '-') . '</div>';
                })
                ->addColumn('riwayat', function ($user) {
                    if (!$user->patient)
                        return '-';
                    $completed = $user->patient->vaccinePatients->where('status', 'selesai')->unique('vaccine_id');
                    if ($completed->isEmpty())
                        return '<span class="text-xs text-gray-400 italic">Belum ada vaksin selesai</span>';

                    $html = '<div class="flex flex-wrap gap-1 mb-2">';
                    foreach ($completed as $vp) {
                        $html .= '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">' . $vp->vaccine->name . '</span>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('sertifikat', function ($user) {
                    $totalVaccines = Vaccine::count();
                    $completedCount = $user->patient ? $user->patient->vaccinePatients->where('status', 'selesai')->unique('vaccine_id')->count() : 0;
                    $isCompleted = ($totalVaccines > 0 && $completedCount >= $totalVaccines);

                    if ($isCompleted) {
                        $url = route('admin.certificate', urlencode($user->patient->certificate_number));
                        return '<a href="' . $url . '" target="_blank" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">Unduh Sertifikat</a>';
                    }
                    return '<span class="text-xs text-gray-400">Belum Lengkap</span>';
                })
                ->addColumn('action', function ($user) {
                    $editUrl = route('admin.users.edit', $user->id);
                    $deleteUrl = route('admin.users.destroy', $user->id);
                    $csrf = csrf_token();
                    $method = method_field('DELETE');
                    $userJson = htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8');

                    return '
                    <div class="flex justify-center gap-2">
                        <a href="' . $editUrl . '" class="inline-flex items-center px-3 py-1 bg-yellow-500 text-white rounded text-xs hover:bg-yellow-600 transition">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit
                        </a>
                        <button onclick=\'openDetailModal(' . $userJson . ')\' class="inline-flex items-center px-3 py-1 bg-cyan-600 text-white rounded text-xs hover:bg-cyan-700 transition">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Detail
                        </button>
                        <form action="' . $deleteUrl . '" method="POST" onsubmit="return confirm(\'Apakah Anda yakin ingin menghapus data ini? Data akan diarsip (Soft Delete).\');" class="inline-block">
                            ' . csrf_field() . $method . '
                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700 transition">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Hapus
                            </button>
                        </form>
                    </div>';
                })
                ->rawColumns(['checkbox', 'peserta', 'orang_tua', 'nik', 'usia', 'alamat', 'posyandu', 'riwayat', 'sertifikat', 'action'])
                ->make(true);
        }

        $totalVaccines = Vaccine::count();
        return view('dashboard.admin.users.index', compact('totalVaccines'));
    }

    public function exportUsersPdf()
    {
        $users = User::with(['patient.vaccinePatients.vaccine', 'patient.village'])
            ->where('role', 'user')
            ->get();
        $totalVaccines = Vaccine::count();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.users', compact('users', 'totalVaccines'));
        return $pdf->download('Data_Peserta_' . date('Y-m-d') . '.pdf');
    }

    public function createUser()
    {
        $villages = Village::with('posyandus')->get();
        return view('dashboard.admin.users.create', compact('villages'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            // Patient Data
            'mother_name' => 'required',
            'nik' => 'nullable|string|max:16',
            'date_birth' => 'required|date',
            'address' => 'required',
            'gender' => 'required|in:male,female',
            'village_id' => 'required|exists:villages,id',
            'posyandu_id' => 'nullable|exists:posyandus,id',
            'phone' => 'required',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => \Illuminate\Support\Str::slug($request->name) . rand(1000, 9999) . '@local.test',
                'password' => \Illuminate\Support\Facades\Hash::make('password'), // Default password
                'role' => 'user'
            ]);

            \App\Models\Patient::create([
                'user_id' => $user->id,
                'village_id' => $request->village_id,
                'posyandu_id' => $request->posyandu_id,
                'name' => $request->name,
                'mother_name' => $request->mother_name,
                'nik' => $request->nik,
                'date_birth' => $request->date_birth,
                'address' => $request->address,
                'gender' => $request->gender,
                'phone' => $request->phone,
            ]);
        });

        return redirect()->route('admin.users')->with('success', 'Peserta berhasil didaftarkan');
    }

    public function editUser(User $user)
    {
        $user->load('patient');
        $villages = Village::with('posyandus')->get();

        $completedCount = $user->patient ? $user->patient->vaccinePatients()->where('status', 'selesai')->count() : 0;
        $totalVaccines = Vaccine::count();
        $isCompleted = ($totalVaccines > 0 && $completedCount >= $totalVaccines) || ($user->patient && $user->patient->certificate_number);

        return view('dashboard.admin.users.edit', compact('user', 'villages', 'isCompleted'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            // Patient Data
            'mother_name' => 'required',
            'nik' => 'nullable|string|max:16',
            'date_birth' => 'required|date',
            'address' => 'required',
            'gender' => 'required|in:male,female',
            'village_id' => 'required|exists:villages,id',
            'posyandu_id' => 'nullable|exists:posyandus,id',
            'phone' => 'required',
            'certificate_number' => 'nullable|string'
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $user) {
            $user->update([
                'name' => $request->name
            ]);

            $user->patient()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'village_id' => $request->village_id,
                    'posyandu_id' => $request->posyandu_id,
                    'name' => $request->name,
                    'mother_name' => $request->mother_name,
                    'nik' => $request->nik,
                    'date_birth' => $request->date_birth,
                    'address' => $request->address,
                    'gender' => $request->gender,
                    'phone' => $request->phone,
                ]
            );

            if ($request->has('certificate_number')) {
                $user->patient()->update(['certificate_number' => $request->certificate_number]);
            }
        });

        return redirect()->route('admin.users')->with('success', 'Data peserta berhasil diperbarui');
    }

    public function destroyUser(User $user)
    {
        DB::transaction(function () use ($user) {
            if ($user->patient) {
                $user->patient()->delete(); // Soft delete patient
            }
            $user->delete(); // Soft delete user
        });

        return redirect()->route('admin.users')->with('success', 'Data peserta berhasil dihapus (Arsip).');
    }

    public function bulkDeleteUsers(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        DB::transaction(function () use ($request) {
            User::whereIn('id', $request->ids)->each(function ($user) {
                if ($user->patient) {
                    $user->patient()->delete();
                }
                $user->delete();
            });
        });

        return response()->json(['success' => true, 'message' => count($request->ids) . ' data peserta berhasil dihapus.']);
    }

    public function exportUsers()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PatientExport, 'data_peserta_' . date('Y-m-d') . '.xlsx');
    }

    public function importUsers(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\PatientImport, $request->file('file'));
            return redirect()->route('admin.users')->with('success', 'Data peserta berhasil diimport!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()->route('admin.users')->with('error', 'Gagal import: ' . implode(' | ', $errors));
        } catch (\Exception $e) {
            return redirect()->route('admin.users')->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function downloadImportTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            public function array(): array
            {
                return [
                    ['Anak Contoh', '1234567890123456', 'Ibu Contoh', '2023-01-15', 'Laki-laki', 'Jl. Contoh No. 1', 'Dusun Contoh', 'Posyandu Melati', '08123456789'],
                ];
            }

            public function headings(): array
            {
                return ['nama_anak', 'nik', 'nama_ibu', 'tanggal_lahir', 'jenis_kelamin', 'alamat', 'dusun', 'posyandu', 'no_hp'];
            }
        }, 'template_import_peserta.xlsx');
    }

    public function logs()
    {
        $logs = Activity::latest()->get();
        return view('dashboard.admin.logs.index', compact('logs'));
    }

    public function history(Request $request)
    {
        // Check if it's an AJAX request for DataTables
        if ($request->ajax()) {
            $status = $request->get('status', 'jadwal');
            
            // Pass status to only fetch the data we need
            $data = $this->getVaccinationData($request, $status);

            // Map frontend status names to internal collection names
            $statusMap = [
                'jadwal' => 'active',
                'akan' => 'upcoming',
                'sudah' => 'done',
                'terlewat' => 'overdue',
                'schedule' => 'schedule'
            ];
            
            $internalStatus = $statusMap[$status] ?? $status;
            $collection = $data[$internalStatus] ?? collect([]);

            return \Yajra\DataTables\Facades\DataTables::of($collection)
                ->addIndexColumn()
                ->addColumn('peserta', function ($row) {
                    return '<div class="font-medium text-gray-900">' . $row->patient->name . '</div>
                            <div class="text-xs text-gray-500 mt-1">Ibu: ' . $row->mother_name . ' <span class="mx-1">|</span> Umur: ' . $row->age . '</div>';
                })
                ->addColumn('vaccine_name', function ($row) {
                    return $row->vaccine->name;
                })
                ->addColumn('jadwal_range', function ($row) {
                    // For Schedule
                    if (isset($row->schedule_at)) {
                         return '<span class="text-blue-600 font-bold">' . $row->schedule_at->format('d M Y') . '</span>';
                    }
                    // For Active, Overdue
                    if (isset($row->start_date) && isset($row->end_date)) {
                        $start = $row->start_date->format('d M Y');
                        $end = $row->end_date->format('d M Y');
                        // Highlight logic could be here, but we keep it simple for now or match view
                        if (now()->between($row->start_date, $row->end_date)) {
                            return '<span class="text-green-600 font-bold">' . $start . '</span> - ' . $end;
                        }
                        if (now()->greaterThan($row->end_date)) {
                            return '<span class="text-red-600 font-bold">' . $start . ' - ' . $end . '</span>';
                        }
                        return '<span class="text-gray-500">' . $start . '</span>';
                    }
                    // For Done
                    if (isset($row->date)) {
                        return \Carbon\Carbon::parse($row->date)->format('d M Y H:i');
                    }
                    return '-';
                })
                ->addColumn('village_name', function ($row) {
                return $row->patient->village->name ?? '-';
            })
            ->addColumn('posyandu_name', function ($row) {
                // Check if posyandu exists on the row (Done status) or patient (others)
                if (isset($row->patient->posyandu)) {
                    return $row->patient->posyandu->name ?? '-';
                }
                // For 'done' rows, posyandu might be directly on the record or we fallback to patient's current
                if (isset($row->status) && $row->status == 'Selesai') {
                     return $row->posyandu ?? '-';
                }
                return '-';
            })
            ->addColumn('status_badge', function ($row) use ($status) {
                    if ($status === 'sudah')
                        return '<span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">Selesai</span>';
                    if ($status === 'terlewat')
                        return '<span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">Terlewat</span>';
                    return '-';
                })
                ->addColumn('kipi', function ($row) {
                    if (empty($row->kipi)) return '-';
                    $kipi = json_decode($row->kipi, true);
                    if (!is_array($kipi)) return '-';
                    
                    $badges = '';
                    foreach ($kipi as $k) {
                        $badges .= '<span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded-full text-xs mr-1 mb-1 inline-block">' . htmlspecialchars($k) . '</span>';
                    }
                    return $badges;
                })
                ->addColumn('action', function ($row) use ($status) {
                    $btn = '';
                    // Approve Button (Active, Upcoming, Overdue, Schedule)
                    if (in_array($status, ['jadwal', 'akan', 'terlewat', 'schedule'])) {
                        $villageId = $row->patient->village_id;
                        $params = sprintf(
                            "'%s', '%s', '%s', '%s', '%s'",
                            $row->patient->id,
                            $row->vaccine->id,
                            addslashes($row->patient->name),
                            addslashes($row->vaccine->name),
                            $villageId
                        );
                        $btn .= '<button onclick="openApproveModal(' . $params . ')" class="px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition flex items-center mr-1 mb-1">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Approve
                                </button>';
                    }
                    // Schedule Button (Jadwal, Akan, Terlewat)
                    if (in_array($status, ['jadwal', 'akan', 'terlewat'])) {
                         $params = sprintf(
                            "'%s', '%s', '%s', '%s'",
                            $row->patient->id,
                            $row->vaccine->id,
                            addslashes($row->patient->name),
                            addslashes($row->vaccine->name)
                        );
                        $btn .= '<button onclick="openScheduleModal(' . $params . ')" class="px-3 py-1 bg-indigo-600 text-white rounded text-xs hover:bg-indigo-700 transition flex items-center mr-1 mb-1">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    Schedule
                                </button>';
                    }
                    
                    // Detail & Rollback (Done)
                    if ($status === 'sudah') {
                        $json = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                        // Encode kipi explicitly to avoid issues if needed, but row has it.
                        // We will pass the full row to openKipiModal as well.
                        
                        $rollbackUrl = route('admin.history.rollback', $row->id);
                        $csrf = csrf_field();
                        $method = method_field('DELETE');

                        $btn .= '<div class="flex items-center gap-2">
                                <button onclick=\'openKipiModal(' . $json . ')\' class="px-3 py-1 bg-yellow-500 text-white rounded text-xs hover:bg-yellow-600 transition flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    KIPI
                                </button>
                                <button onclick=\'openDetailModal(' . $json . ')\' class="px-3 py-1 bg-cyan-600 text-white rounded text-xs hover:bg-cyan-700 transition flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Detail
                                </button>
                                <form action="' . $rollbackUrl . '" method="POST" onsubmit="return confirm(\'Apakah anda yakin akan mengembalikan data belum di approve?\');">
                                    ' . $csrf . $method . '
                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700 transition flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                        Rollback
                                    </button>
                                </form>
                                </div>';
                    }
                    return $btn;
                })
                ->rawColumns(['peserta', 'jadwal_range', 'status_badge', 'kipi', 'action'])
                ->make(true);
        }

        // Standard GET request: Calculate counts only
        // To be safe and mostly because filtering affects counts, we run the same logic.
        $data = $this->getVaccinationData($request);
        $villages = Village::with('posyandus')->get();
        $vaccines = Vaccine::orderBy('minimum_age')->get();

        return view('dashboard.admin.history.index', [
            'active_count' => $data['active']->count(),
            'upcoming_count' => $data['upcoming']->count(),
            'done_count' => $data['done']->count(),
            'overdue_count' => $data['overdue']->count(),
            'schedule_count' => $data['schedule']->count(),
            'villages' => $villages,
            'vaccines' => $vaccines
        ]);
    }

    public function exportHistoryExcel(Request $request, $status)
    {
        $data = $this->getVaccinationData($request);
        
        $collection = match ($status) {
            'jadwal' => $data['active'],
            'akan' => $data['upcoming'],
            'sudah' => $data['done'],
            'terlewat' => $data['overdue'],
            'schedule' => $data['schedule'],
            default => collect([]),
        };

        $filename = 'riwayat_vaksin_' . $status . '_' . date('Y-m-d_His') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\HistoryExport($collection, $status),
            $filename
        );
    }

    public function exportHistoryPdf(Request $request, $status)
    {
        $data = $this->getVaccinationData($request);
        
        $collection = match ($status) {
            'jadwal' => $data['active'],
            'akan' => $data['upcoming'],
            'sudah' => $data['done'],
            'terlewat' => $data['overdue'],
            'schedule' => $data['schedule'],
            default => collect([]),
        };

        $statusLabels = [
            'schedule' => 'Schedule',
            'jadwal' => 'Jadwal Vaksin (Active)',
            'akan' => 'Akan Vaksin (Upcoming)',
            'sudah' => 'Sudah Vaksin (Done)',
            'terlewat' => 'Terlewat (Overdue)',
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.history-pdf', [
            'data' => $collection,
            'status' => $status,
            'statusLabel' => $statusLabels[$status] ?? $status,
        ])->setPaper('a4', 'landscape');

        $filename = 'riwayat_vaksin_' . $status . '_' . date('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }


    private function getVaccinationData($request, $status = null)
    {
        $query = \App\Models\Patient::with(['vaccinePatients.vaccine', 'vaccinePatients.posyandu', 'village', 'posyandu']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('mother_name', 'like', '%' . $search . '%');
            });
        }
        
        if ($request->filled('village_id')) {
            $query->where('village_id', $request->village_id);
        }

        if ($request->filled('posyandu_id')) {
            $query->where('posyandu_id', $request->posyandu_id);
        }

        $patients = $query->get();
        
        $vaccineQuery = Vaccine::orderBy('minimum_age');
        
        if ($request->filled('vaccine_id')) {
            $vaccineQuery->where('id', $request->vaccine_id);
        }
        
        $vaccines = $vaccineQuery->get();

        // Map frontend status names to internal names
        $statusMap = [
            'jadwal' => 'active',
            'akan' => 'upcoming', 
            'sudah' => 'done',
            'terlewat' => 'overdue',
            'schedule' => 'schedule'
        ];
        
        $internalStatus = $status ? ($statusMap[$status] ?? $status) : null;

        // Initialize collections based on what's needed (using INTERNAL names)
        $result = [
            'active' => collect(),
            'upcoming' => collect(),
            'done' => collect(),
            'overdue' => collect(),
            'schedule' => collect()
        ];

        foreach ($patients as $patient) {
            $doneVaccineIds = $patient->vaccinePatients->where('status', 'selesai')->pluck('vaccine_id')->toArray();

            foreach ($vaccines as $vaccine) {
                // ========== SCHEDULE STATUS ==========
                if (!$status || $internalStatus === 'schedule') {
                    $scheduledRecord = $patient->vaccinePatients
                        ->where('vaccine_id', $vaccine->id)
                        ->where('status', '!=', 'selesai')
                        ->filter(fn($r) => $r->schedule_at !== null)
                        ->first();
                    
                    if ($scheduledRecord) {
                        if (!$status || $internalStatus === 'schedule') {
                            $result['schedule']->push((object) [
                                'patient' => $patient,
                                'vaccine' => $vaccine,
                                'schedule_at' => $scheduledRecord->schedule_at,
                                'age' => number_format(\Carbon\Carbon::parse($patient->date_birth)->floatDiffInMonths(now()), 1) . ' Bulan',
                                'mother_name' => $patient->mother_name
                            ]);
                        }
                        if ($status === 'schedule') continue; // Only skip if specifically requesting schedule
                    }
                }

                // ========== DONE STATUS ==========
                if (in_array($vaccine->id, $doneVaccineIds)) {
                    if (!$status || $internalStatus === 'done') {
                        $record = $patient->vaccinePatients->where('vaccine_id', $vaccine->id)->first();
                        
                        // Filter Done Date
                        if ($request->filled('start_date') && $record->vaccinated_at < $request->start_date) continue;
                        if ($request->filled('end_date') && $record->vaccinated_at > $request->end_date . ' 23:59:59') continue;

                        $result['done']->push((object) [
                            'id' => $record->id,
                            'patient' => $patient,
                            'vaccine' => $vaccine,
                            'date' => $record->vaccinated_at,
                            'status' => 'Selesai',
                            'posyandu' => $record->posyandu->name ?? '-',
                            'mother_name' => $patient->mother_name,
                            'dob' => \Carbon\Carbon::parse($patient->date_birth)->format('d M Y'),
                            'gender' => $patient->gender == 'male' ? 'Laki-laki' : 'Perempuan',
                            'address' => $patient->address,
                            'kipi' => $record->kipi,
                            'age' => number_format(\Carbon\Carbon::parse($patient->date_birth)->floatDiffInMonths($record->vaccinated_at), 1) . ' Bulan'
                        ]);
                    }
                    continue;
                }

                // ========== ACTIVE / UPCOMING / OVERDUE ==========
                if (!$status || in_array($internalStatus, ['active', 'upcoming', 'overdue'])) {
                    // Check if already scheduled (skip for active/upcoming/overdue)
                    $isScheduled = $patient->vaccinePatients
                        ->where('vaccine_id', $vaccine->id)
                        ->where('status', '!=', 'selesai')
                        ->filter(fn($r) => $r->schedule_at !== null)
                        ->isNotEmpty();
                    
                    if ($isScheduled) continue;

                    // Calculate Window
                    $startDate = \Carbon\Carbon::parse($patient->date_birth)->addMonths((int) $vaccine->minimum_age);
                    $duration = (int) ($vaccine->duration_days ?? 7);
                    $endDate = $startDate->copy()->addDays($duration);

                    // Filter Active/Upcoming/Overdue Ranges
                    if ($request->filled('start_date') && $endDate < \Carbon\Carbon::parse($request->start_date)) continue;
                    if ($request->filled('end_date') && $startDate > \Carbon\Carbon::parse($request->end_date)->endOfDay()) continue;

                    // Current Age
                    $currentAge = number_format(\Carbon\Carbon::parse($patient->date_birth)->floatDiffInMonths(now()), 1) . ' Bulan';

                    $data = (object) [
                        'patient' => $patient,
                        'vaccine' => $vaccine,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'age' => $currentAge,
                        'mother_name' => $patient->mother_name
                    ];

                    $isActive = now()->between($startDate, $endDate);
                    $isOverdue = now()->greaterThan($endDate);

                    if ($isActive) {
                        if (!$status || $internalStatus === 'active') {
                            $result['active']->push($data);
                        }
                    } elseif ($isOverdue) {
                        if (!$status || $internalStatus === 'overdue') {
                            $result['overdue']->push($data);
                        }
                    } else {
                        if (!$status || $internalStatus === 'upcoming') {
                            $result['upcoming']->push($data);
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function storePatientSchedule(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'vaccine_id' => 'required|exists:vaccines,id',
            'schedule_at' => 'required|date',
        ]);

        // Fetch patient to get their village_id and posyandu_id
        $patient = \App\Models\Patient::findOrFail($request->patient_id);

        \App\Models\VaccinePatient::updateOrCreate(
            [
                'patient_id' => $request->patient_id,
                'vaccine_id' => $request->vaccine_id,
            ],
            [
                'schedule_at' => $request->schedule_at,
                'village_id' => $patient->village_id,
                'posyandu_id' => $patient->posyandu_id,
                'request_date' => now(),
            ]
        );

        return back()->with('success', 'Jadwal berhasil disimpan');
    }

    public function storeHistory(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'vaccine_id' => 'required|exists:vaccines,id',
            'village_id' => 'required|exists:villages,id',
            'posyandu_id' => 'nullable|exists:posyandus,id',
            'vaccinated_at' => 'required|date',
        ]);

        $vaccinePatient = \App\Models\VaccinePatient::updateOrCreate(
            [
                'patient_id' => $request->patient_id,
                'vaccine_id' => $request->vaccine_id,
            ],
            [
                'village_id' => $request->village_id,
                'posyandu_id' => $request->posyandu_id,
                'vaccinated_at' => $request->vaccinated_at,
                'status' => 'selesai',
                'request_date' => now(), // Ensure request_date is set if creating new
            ]
        );

        // Send "Approved" Notification
        $patient = \App\Models\Patient::with('vaccinePatients', 'village')->find($request->patient_id);
        $vaccine = \App\Models\Vaccine::find($request->vaccine_id);

        if ($patient->phone) {
            $approvedTemplate = \App\Models\NotificationTemplate::where('slug', 'vaccine_approved')->first();
            if ($approvedTemplate) {
                try {
                    $posyandu = \App\Models\Posyandu::find($request->posyandu_id);
                    $posyanduName = $posyandu ? $posyandu->name : 'Posyandu';

                    $msg = \App\Models\NotificationTemplate::parse($approvedTemplate->content, $patient, [
                        'parent_name' => $patient->mother_name, // Changed from patient_name (and explicit mapping)
                        'child_name' => $patient->name,
                        'vaccine_name' => $vaccine->name,
                        'vaccinated_at' => \Carbon\Carbon::parse($request->vaccinated_at)->format('d-m-Y'),
                        'posyandu_name' => $posyanduName,
                    ]);

                    // Send via Waha
                    $waha = app(\App\Services\WahaService::class);
                    $response = $waha->sendMessage($patient->phone, $msg);
                    $body = $response->json();

                    if ($response->successful() && isset($body['id']['fromMe'])) {
                        \App\Models\NotificationLog::create([
                            'to' => $patient->phone,
                            'message' => $msg,
                            'status' => 'sent',
                            'response' => $response->body(),
                            'sent_at' => now(),
                        ]);
                    } else {
                        \App\Models\NotificationLog::create([
                            'to' => $patient->phone,
                            'message' => $msg,
                            'status' => 'failed',
                            'response' => $response->body()
                        ]);
                    }
                } catch (\Exception $e) {
                    // Log silent error
                }
            }
        }

        // Check Completion
        $patient = \App\Models\Patient::with('vaccinePatients')->find($request->patient_id);
        $allVaccines = \App\Models\Vaccine::all();
        $completedIds = $patient->vaccinePatients->where('status', 'selesai')->pluck('vaccine_id')->toArray();

        // If count matches (User has done ALL vaccines)
        // If count matches (User has done ALL vaccines)
        if ($allVaccines->count() === count($completedIds)) {
            // Generate Certificate (Number & Date)
            $this->generateCertificate($patient);

            $template = \App\Models\NotificationTemplate::where('slug', 'vaccine_completed')->first();
            if ($template && $patient->phone) {
                try {
                    $link = route('admin.certificate', ['patient' => $patient->id]); // Using the public/admin route for download

                    $message = \App\Models\NotificationTemplate::parse($template->content, $patient, [
                        'parent_name' => $patient->mother_name,
                        'child_name' => $patient->name,
                        'certificate_link' => $link
                    ]);

                    // Send via Waha (Instantiate service manually or resolve from container since we are in controller method and didnt inject in constructor yet)
                    $waha = app(\App\Services\WahaService::class);
                    $response = $waha->sendMessage($patient->phone, $message);
                    $body = $response->json();

                    if ($response->successful() && isset($body['id']['fromMe'])) {
                        \App\Models\NotificationLog::create([
                            'to' => $patient->phone,
                            'message' => $message,
                            'status' => 'sent',
                            'response' => $response->body(),
                            'sent_at' => now(),
                        ]);
                    } else {
                        \App\Models\NotificationLog::create([
                            'to' => $patient->phone,
                            'message' => $message,
                            'status' => 'failed',
                            'response' => $response->body()
                        ]);
                    }
                } catch (\Exception $e) {
                    \App\Models\NotificationLog::create([
                        'to' => $patient->phone,
                        'message' => $message ?? 'Error building message',
                        'status' => 'failed',
                        'response' => $e->getMessage()
                    ]);
                }
            }
        }

        return back()->with('success', 'Data vaksinasi berhasil disimpan');
    }

    public function storeKipi(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:vaccine_patients,id',
            'kipi' => 'required|array',
            'kipi_other' => 'nullable|string'
        ]);

        $record = \App\Models\VaccinePatient::findOrFail($request->id);

        $kipiData = $request->kipi;
        if (($key = array_search('Lainnya', $kipiData)) !== false) {
            unset($kipiData[$key]);
            if ($request->filled('kipi_other')) {
                $kipiData[] = $request->kipi_other; // Save custom input
            }
        }

        $record->update([
            'kipi' => json_encode(array_values($kipiData)) // Re-index array
        ]);

        return back()->with('success', 'Data KIPI berhasil disimpan');
    }

    public function certification(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'vaccine_id' => 'required|exists:vaccines,id',
        ]);

        $record = \App\Models\VaccinePatient::where('patient_id', $request->patient_id)
            ->where('vaccine_id', $request->vaccine_id)
            ->where('status', 'selesai')
            ->firstOrFail();

        $data = [
            'record' => $record,
            'patient' => $record->patient,
            'vaccine' => $record->vaccine,
            'village' => $record->village,
            'posyandu' => $record->posyandu,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.certificate', $data);
        return $pdf->download('Sertifikat_Vaksin_' . $record->patient->name . '.pdf');
    }

    public function rollbackHistory(Request $request, $id)
    {
        $record = \App\Models\VaccinePatient::findOrFail($id);
        $patient = $record->patient;

        $record->delete();

        // Check if patient is still complete?
        // If we remove a record, likely they are not complete anymore (unless they had dupes).
        // Safest is to check counts.
        $totalVaccines = Vaccine::count();
        $completedCount = $patient->vaccinePatients()->where('status', 'selesai')->count();

        if ($completedCount < $totalVaccines) {
            $patient->update([
                'certificate_number' => null,
                'completed_vaccination_at' => null
            ]);
        }

        return back()->with('success', 'Data vaksinasi berhasil dikembalikan (rollback)');
    }
    public function approveRequest($id)
    {
        $record = \App\Models\VaccinePatient::findOrFail($id);
        $record->update([
            'status' => 'selesai',
            'vaccinated_at' => now() // Set to now upon approval
        ]);

        // Check for completion
        $patient = $record->patient;
        $totalVaccines = Vaccine::count();
        $completedCount = $patient->vaccinePatients()->where('status', 'selesai')->count();

        if ($totalVaccines > 0 && $completedCount >= $totalVaccines) {
            $this->generateCertificate($patient);

            // Trigger completion notification (reuse logic from storeHistory or call here?)
            // For now, let's keep it simple. If we want notification, we should extract that logic.
            // But user only asked for "Data update" and "Certificate Field".
        }

        return back()->with('success', 'Permintaan vaksinasi disetujui.');
    }

    public function rejectRequest($id)
    {
        $record = \App\Models\VaccinePatient::findOrFail($id);
        $record->delete(); // Or set status to 'rejected' if column exists? Usually delete or 'rejected'.
        // Request table status is enum('pendding', 'selesai'...). If no rejected status, delete.
        // Schema check? Assuming delete for now as per dashboard view using DELETE method.
        return back()->with('success', 'Permintaan vaksinasi ditolak.');
    }

    private function generateCertificate($patient)
    {
        // Avoid regenerating if already exists (as per user implication "simpan nomor")
        if ($patient->certificate_number)
            return;

        $lastRecord = $patient->vaccinePatients()->where('status', 'selesai')->latest('updated_at')->first();
        $completionDate = $lastRecord ? $lastRecord->updated_at : now();
        $month = $completionDate->month;
        $year = $completionDate->year;

        $startOfMonth = $completionDate->copy()->startOfMonth();

        // Sequence Logic: Count COMPLETED patients in this month up to this date
        $previousCount = \App\Models\Patient::whereNotNull('completed_vaccination_at')
            ->whereBetween('completed_vaccination_at', [$startOfMonth, $completionDate])
            ->count();

        $sequence = $previousCount + 1;

        $romanMonths = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
        $romanMonth = $romanMonths[$month] ?? 'I';

        $certNum = sprintf("%03d/%s/ISTG/%s", $sequence, $romanMonth, $year);

        // Get current certificate settings and snapshot to patient
        $settings = \App\Models\CertificateSetting::current();

        $patient->update([
            'completed_vaccination_at' => $completionDate,
            'certificate_number' => $certNum,
            'cert_kepala_upt_name' => $settings->kepala_upt_name,
            'cert_kepala_upt_signature' => $settings->kepala_upt_signature,
            'cert_petugas_jurim_name' => $settings->petugas_jurim_name,
            'cert_petugas_jurim_signature' => $settings->petugas_jurim_signature,
            'cert_background_image' => $settings->background_image,
        ]);
    }

    // Certificate Settings
    public function certificateSettings()
    {
        $settings = \App\Models\CertificateSetting::current();
        return view('dashboard.admin.settings.index', compact('settings'));
    }

    public function updateCertificateSettings(Request $request)
    {
        $request->validate([
            'kepala_upt_name' => 'nullable|string|max:255',
            'petugas_jurim_name' => 'nullable|string|max:255',
            'kepala_upt_signature' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'petugas_jurim_signature' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'background_image' => 'nullable|image|mimes:png,jpg,jpeg|max:5120',
        ]);

        $settings = \App\Models\CertificateSetting::first() ?? new \App\Models\CertificateSetting();

        // Only update name fields if provided
        if ($request->filled('kepala_upt_name')) {
            $settings->kepala_upt_name = $request->kepala_upt_name;
        }
        if ($request->filled('petugas_jurim_name')) {
            $settings->petugas_jurim_name = $request->petugas_jurim_name;
        }

        // Handle file uploads
        if ($request->hasFile('kepala_upt_signature')) {
            $path = $request->file('kepala_upt_signature')->store('certificates', 'public');
            $settings->kepala_upt_signature = '/storage/' . $path;
        }

        if ($request->hasFile('petugas_jurim_signature')) {
            $path = $request->file('petugas_jurim_signature')->store('certificates', 'public');
            $settings->petugas_jurim_signature = '/storage/' . $path;
        }

        if ($request->hasFile('background_image')) {
            $path = $request->file('background_image')->store('certificates', 'public');
            $settings->background_image = '/storage/' . $path;
        }

        $settings->save();

        return back()->with('success', 'Pengaturan sertifikat berhasil disimpan');
    }

    // Admin Users Management
    public function adminUsers()
    {
        $admins = User::where('role', 'admin')->get();
        return view('dashboard.admin.admins.index', compact('admins'));
    }

    public function storeAdminUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'admin',
        ]);

        return back()->with('success', 'Administrator berhasil ditambahkan');
    }

    public function updateAdminUser(Request $request, User $admin)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;

        if ($request->filled('password')) {
            $admin->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $admin->save();

        return back()->with('success', 'Administrator berhasil diperbarui');
    }

    public function destroyAdminUser(User $admin)
    {
        if ($admin->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        $admin->delete();
        return back()->with('success', 'Administrator berhasil dihapus');
    }





    public function kipi(Request $request)
    {
        if ($request->ajax()) {
            $query = \App\Models\VaccinePatient::with(['patient', 'vaccine', 'village', 'posyandu'])
                ->whereNotNull('kipi')
                ->where('kipi', '!=', '[]')
                ->where('kipi', '!=', 'null'); // Safety check

            // Filter: Date Range
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('vaccinated_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            }

            // Filter: KIPI Type
            if ($request->filled('kipi_filter')) {
                $query->whereJsonContains('kipi', $request->kipi_filter);
            }

            // Filter: Village
            if ($request->filled('village_id')) {
                $query->where('village_id', $request->village_id);
            }

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('date', function ($row) {
                    return $row->vaccinated_at ? $row->vaccinated_at->format('d M Y') : '-';
                })
                ->addColumn('patient_name', function ($row) {
                    return $row->patient->name . '<br><span class="text-xs text-gray-500">Ibu: ' . $row->patient->mother_name . '</span>';
                })
                ->addColumn('vaccine', function ($row) {
                    return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">' . $row->vaccine->name . '</span>';
                })
                ->addColumn('kipi_tags', function ($row) {
                    $tags = '';
                    if (is_array($row->kipi)) { // Since we cast it
                        foreach ($row->kipi as $k) {
                            $tags .= '<span class="px-2 py-0.5 mr-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">' . $k . '</span>';
                        }
                    }
                    return $tags;
                })
                ->addColumn('location', function ($row) {
                    return ($row->village->name ?? '-') . '<br><span class="text-xs text-gray-500">' . ($row->posyandu->name ?? '-') . '</span>';
                })
                ->rawColumns(['patient_name', 'vaccine', 'kipi_tags', 'location'])
                ->make(true);
        }

        // Get Unique KIPI values for filter
        $allKipi = \App\Models\VaccinePatient::whereNotNull('kipi')->pluck('kipi');
        $kipiList = [];
        foreach ($allKipi as $kArray) {
            if (is_array($kArray)) {
                foreach ($kArray as $k) {
                    if (!in_array($k, $kipiList)) {
                        $kipiList[] = $k;
                    }
                }
            } else if (is_string($kArray)) {
                 $decoded = json_decode($kArray);
                 if (is_array($decoded)) {
                     foreach ($decoded as $k) {
                        if (!in_array($k, $kipiList)) $kipiList[] = $k;
                     }
                 }
            }
        }
        sort($kipiList);

        $villages = \App\Models\Village::all();

        return view('dashboard.admin.kipi.index', compact('kipiList', 'villages'));
    }

    public function getVillageChartData(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));

        // Logic: Count patients created in that month/year grouped by Village
        // OR Count total active patients? User said "Banyak pesertanya" (Number of participants)
        // usually implies population size. But "per month/year" implies growth or registration.
        // Let's go with: Total Registered Patients UP TO that month/year? 
        // Or Patients Registered IN that month/year.
        // Let's do: Patients Registered IN that month/year.

        $data = DB::table('patients')
            ->join('villages', 'patients.village_id', '=', 'villages.id')
            ->select('villages.name', DB::raw('count(patients.id) as total'))
            ->whereYear('patients.created_at', $year)
            ->whereMonth('patients.created_at', $month)
            ->groupBy('villages.name')
            ->get();

        $labels = $data->pluck('name');
        $counts = $data->pluck('total');

        return response()->json([
            'labels' => $labels,
            'data' => $counts
        ]);
    }


    public function exportKipiExcel(Request $request)
    {
        $filename = 'riwayat_kipi_' . date('Y-m-d_His') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\KipiExport($request), $filename);
    }
}

