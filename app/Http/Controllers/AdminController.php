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
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
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
                'email' => \Illuminate\Support\Str::slug($request->name) . rand(1000, 9999) . '@local.test',
                'password' => \Illuminate\Support\Facades\Hash::make('password'), // Default password
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
            'date_birth' => 'required|date',
            'address' => 'required',
            'gender' => 'required|in:male,female',
            'village_id' => 'required|exists:villages,id',
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
                    'name' => $request->name,
                    'mother_name' => $request->mother_name,
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
                    ['Anak Contoh', 'Ibu Contoh', '2023-01-15', 'Laki-laki', 'Jl. Contoh No. 1', 'Desa Contoh', '08123456789'],
                ];
            }

            public function headings(): array
            {
                return ['nama_anak', 'nama_ibu', 'tanggal_lahir', 'jenis_kelamin', 'alamat', 'desa', 'no_hp'];
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
        if ($patient->certificate_number) return;

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
        
        $romanMonths = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
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
}

