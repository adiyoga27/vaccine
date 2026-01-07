<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Models\Vaccine;
use App\Models\Village;
use App\Models\Patient;
use App\Models\VaccinePatient;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// User Dashboard
Route::middleware(['auth'])->prefix('user')->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        $patient = $user->patient;
        
        $histories = $patient ? $patient->vaccinePatients()->with('vaccine')->get() : collect([]);

        // Get IDs of vaccines already done or in progress
        $doneVaccineIds = $patient ? $histories->where('status', 'selesai')->pluck('vaccine_id')->toArray() : [];
        $pendingVaccineIds = $patient ? $histories->where('status', 'pengajuan')->pluck('vaccine_id')->toArray() : [];
        
        // Fetch upcoming schedules (events)
        $schedules = [];
        if($patient && $patient->village_id) {
            $schedules = App\Models\VaccineSchedule::with('vaccines')
                        ->where('village_id', $patient->village_id)
                        ->whereDate('scheduled_at', '>=', now())
                        ->orderBy('scheduled_at')
                        ->get();
        }
        
        // Process all vaccines to determine status for this patient
        $allVaccines = Vaccine::orderBy('minimum_age')->get();
        $vaccineStatus = [];
        
        $patientAgeMonths = $patient ? \Carbon\Carbon::parse($patient->date_birth)->diffInMonths(now()) : 0;

        foreach($allVaccines as $vac) {
            $status = 'upcoming'; // Default
            $isEligible = false;

            if (in_array($vac->id, $doneVaccineIds)) {
                $status = 'selesai';
            } elseif (in_array($vac->id, $pendingVaccineIds)) {
                $status = 'pengajuan';
            } else {
                // Check eligibility based on age
                if ($patientAgeMonths >= $vac->minimum_age) {
                    $status = 'bisa_diajukan'; // Eligible (Missed or Ready)
                    $isEligible = true;
                } else {
                    $status = 'belum_waktunya'; // In future
                }
            }
            
            $vaccineStatus[] = (object) [
                'vaccine' => $vac,
                'status' => $status,
                'min_age' => $vac->minimum_age,
                'is_eligible' => $isEligible
            ];
        }
        
        // Filter vaccines for dropdown: only those 'bisa_diajukan'
        $eligibleVaccines = collect($vaccineStatus)->where('is_eligible', true)->pluck('vaccine');

        return view('dashboard.user.index', compact('user', 'patient', 'histories', 'vaccineStatus', 'eligibleVaccines', 'schedules'));
    })->name('user.dashboard');
    
    Route::post('/request-vaccine', function (Request $request) {
        $request->validate([
           'vaccine_id' => 'required',
           'schedule_id' => 'required|exists:vaccine_schedules,id' 
        ]);
        
        $schedule = App\Models\VaccineSchedule::findOrFail($request->schedule_id);
        
        VaccinePatient::create([
            'village_id' => $schedule->village_id,
            'patient_id' => Auth::user()->patient->id,
            'vaccine_id' => $request->vaccine_id,
            'request_date' => $schedule->scheduled_at,
            'status' => 'pengajuan'
        ]);
        
        return back()->with('success', 'Pengajuan vaksin berhasil dikirim!');
    })->name('user.request');
});

// Admin Dashboard
Route::middleware(['auth'])->prefix('admin')->group(function () {
    // Dashboard Home
    Route::get('/dashboard', function () {
        $stats = [
            'users' => Patient::count(),
            'villages' => Village::count(),
            'vaccines' => Vaccine::count(),
            'pending' => VaccinePatient::where('status', 'pengajuan')->count(),
        ];
        
        $requests = VaccinePatient::with(['patient', 'vaccine', 'village'])
                    ->where('status', 'pengajuan')
                    ->latest()
                    ->get();
                    
        return view('dashboard.admin.index', compact('stats', 'requests'));
    })->name('admin.dashboard');

    // Villages
    Route::get('/villages', [\App\Http\Controllers\AdminController::class, 'villages'])->name('admin.villages');
    Route::post('/villages', [\App\Http\Controllers\AdminController::class, 'storeVillage'])->name('admin.villages.store');
    Route::put('/villages/{village}', [\App\Http\Controllers\AdminController::class, 'updateVillage'])->name('admin.villages.update');
    Route::delete('/villages/{village}', [\App\Http\Controllers\AdminController::class, 'destroyVillage'])->name('admin.villages.destroy');

    // Vaccines
    Route::get('/vaccines', [\App\Http\Controllers\AdminController::class, 'vaccines'])->name('admin.vaccines');
    Route::post('/vaccines', [\App\Http\Controllers\AdminController::class, 'storeVaccine'])->name('admin.vaccines.store');
    Route::put('/vaccines/{vaccine}', [\App\Http\Controllers\AdminController::class, 'updateVaccine'])->name('admin.vaccines.update');
    Route::delete('/vaccines/{vaccine}', [\App\Http\Controllers\AdminController::class, 'destroyVaccine'])->name('admin.vaccines.destroy');

    // Schedules
    Route::get('/schedules', [\App\Http\Controllers\AdminController::class, 'schedules'])->name('admin.schedules');
    Route::post('/schedules', [\App\Http\Controllers\AdminController::class, 'storeSchedule'])->name('admin.schedules.store');
    Route::put('/schedules/{schedule}', [\App\Http\Controllers\AdminController::class, 'updateSchedule'])->name('admin.schedules.update');
    Route::delete('/schedules/{schedule}', [\App\Http\Controllers\AdminController::class, 'destroySchedule'])->name('admin.schedules.destroy');

    // Monitoring
    Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
    Route::get('/history', [\App\Http\Controllers\AdminController::class, 'history'])->name('admin.history');
    Route::get('/logs', [\App\Http\Controllers\AdminController::class, 'logs'])->name('admin.logs');
    
    Route::post('/approve/{id}', function ($id) {
        $vp = VaccinePatient::findOrFail($id);
        $age = $vp->patient->date_birth->diffInMonths(now());
        $vp->update([
            'status' => 'selesai',
            'vaccinated_at' => now(),
            'age_in_months' => $age
        ]);
        return back()->with('success', 'Status updated to Selesai');
    })->name('admin.approve');
});
