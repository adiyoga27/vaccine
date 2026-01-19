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
    public function users(Request $request)
    {
        $search = $request->input('search');
        
        $users = User::with(['patient.vaccinePatients.vaccine', 'patient.village'])
            ->where('role', 'user')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('mother_name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->appends(['search' => $search]);
            
        $totalVaccines = Vaccine::count();
        return view('dashboard.admin.users.index', compact('users', 'totalVaccines', 'search'));
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

    public function editUser(User $user)
    {
        $user->load('patient');
        $villages = Village::all();
        return view('dashboard.admin.users.edit', compact('user', 'villages'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|confirmed|min:6', // Optional password update
            // Patient Data
            'mother_name' => 'required',
            'date_birth' => 'required|date',
            'address' => 'required',
            'gender' => 'required|in:male,female',
            'village_id' => 'required|exists:villages,id',
            'phone' => 'required',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $user) {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
            }

            $user->update($updateData);

            $user->patient()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'village_id' => $request->village_id,
                    'name' => $request->name,
                    'mother_name' => $request->mother_name,
                    'date_birth' => $request->date_birth,
                    'address' => $request->address,
                    'gender' => $request->gender,
                    'phone' => $request->phone,
                ]
            );
        });

        return redirect()->route('admin.users')->with('success', 'Data peserta berhasil diperbarui');
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
                    ['Anak Contoh', 'Ibu Contoh', 'contoh@email.com', 'password123', '2023-01-15', 'Laki-laki', 'Jl. Contoh No. 1', 'Desa Contoh', '08123456789'],
                ];
            }

            public function headings(): array
            {
                return ['nama_anak', 'nama_ibu', 'email', 'password', 'tanggal_lahir', 'jenis_kelamin', 'alamat', 'desa', 'no_hp'];
            }
        }, 'template_import_peserta.xlsx');
    }

    public function logs()
    {
        $logs = Activity::latest()->paginate(20);
        return view('dashboard.admin.logs.index', compact('logs'));
    }

    public function history(Request $request)
    {
        $query = \App\Models\Patient::with(['vaccinePatients', 'village']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('mother_name', 'like', '%' . $search . '%');
            });
        }

        $patients = $query->get();
        $vaccines = Vaccine::orderBy('minimum_age')->get();

        $active = collect();
        $upcoming = collect();
        $done = collect();
        $overdue = collect();

        foreach ($patients as $patient) {
            $doneVaccineIds = $patient->vaccinePatients->where('status', 'selesai')->pluck('vaccine_id')->toArray();

            foreach ($vaccines as $vaccine) {
                // Done
                if (in_array($vaccine->id, $doneVaccineIds)) {
                    $record = $patient->vaccinePatients->where('vaccine_id', $vaccine->id)->first();
                    $done->push((object)[
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
                        // Age at the time of vaccination
                        'age' => number_format(\Carbon\Carbon::parse($patient->date_birth)->floatDiffInMonths($record->vaccinated_at), 1) . ' Bulan'
                    ]);
                    continue;
                }

                // Calculate Window
                $startDate = \Carbon\Carbon::parse($patient->date_birth)->addMonths((int) $vaccine->minimum_age);
                $duration = (int) ($vaccine->duration_days ?? 7);
                $endDate = $startDate->copy()->addDays($duration);

                // Current Age
                $currentAge = number_format(\Carbon\Carbon::parse($patient->date_birth)->floatDiffInMonths(now()), 1) . ' Bulan';

                $data = (object)[
                    'patient' => $patient,
                    'vaccine' => $vaccine,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'age' => $currentAge,
                    'mother_name' => $patient->mother_name
                ];

                if (now()->between($startDate, $endDate)) {
                    // Active (Jadwal Vaksin)
                    $active->push($data);
                } elseif (now()->greaterThan($endDate)) {
                    // Overdue (Terlewat)
                    $overdue->push($data);
                } else {
                    // Upcoming (Akan Vaksin)
                    $upcoming->push($data);
                }
            }
        }

        $villages = Village::with('posyandus')->get();

        return view('dashboard.admin.history.index', compact('active', 'upcoming', 'done', 'overdue', 'villages'));
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
        if ($allVaccines->count() === count($completedIds)) {
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
        $record->delete();

        return back()->with('success', 'Data vaksinasi berhasil dikembalikan (rollback)');
    }
}
