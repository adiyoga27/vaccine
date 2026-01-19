<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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
        
        // Process all vaccines to determine Personalized Schedule
        $allVaccines = Vaccine::orderBy('minimum_age')->get();
        $vaccineSchedules = [];
        
        // Calculate age in months accurately with 1 decimal
        $patientAgeMonths = $patient ? number_format(\Carbon\Carbon::parse($patient->date_birth)->floatDiffInMonths(now()), 1) : 0;

        foreach($allVaccines as $vac) {
            $isDone = in_array($vac->id, $doneVaccineIds);
            
            // Calculate Schedule Window
            // Start Date = BirthDate + Minimum Age (Months)
            $startDate = $patient ? \Carbon\Carbon::parse($patient->date_birth)->addMonths($vac->minimum_age) : null;
            $endDate = $startDate ? $startDate->copy()->addDays($vac->duration_days ?? 7) : null;
            
            $status = 'upcoming'; // Default
            
            if ($isDone) {
                $status = 'selesai';
            } elseif ($startDate && now()->between($startDate, $endDate)) {
                $status = 'bisa_diajukan'; // Currently in the vaccination window
            } elseif ($startDate && now()->greaterThan($endDate)) {
                $status = 'terlewat'; // Overdue
            }

            $vaccineSchedules[] = (object) [
                'vaccine' => $vac,
                'status' => $status,
                'min_age' => $vac->minimum_age,
                'start_date' => $startDate,
                'end_date' => $endDate,
                // For calendar event
                'event_title' => "Vaksin: {$vac->name} ({$vac->minimum_age} Bulan)",
                'event_start' => $startDate ? $startDate->format('Y-m-d') : null,
                'event_end' => $endDate ? $endDate->format('Y-m-d') : null, // FullCalendar end date is exclusive, strictly it might need +1 day but keeping simple
            ];
        }
        
        // Prepare events for FullCalendar with Grouping
        $calendarEvents = [];
        $groupedEvents = [];

        foreach($vaccineSchedules as $vs) {
            if ($vs->start_date && $vs->end_date) {
                // Create a unique key for grouping: StartDate_EndDate_Status
                $key = $vs->event_start . '_' . $vs->event_end . '_' . $vs->status;

                if (!isset($groupedEvents[$key])) {
                    $color = '#3B82F6'; // Default Blue
                    if ($vs->status == 'selesai') {
                        $color = '#059669'; // Emerald-600
                    } elseif ($vs->status == 'bisa_diajukan') {
                        $color = '#22c55e'; // Green-500
                    } elseif ($vs->status == 'terlewat') {
                        $color = '#EF4444'; // Red
                    }

                    $groupedEvents[$key] = [
                        'titles' => [$vs->vaccine->name],
                        'start' => $vs->event_start,
                        'end' => $vs->end_date->copy()->addDay()->format('Y-m-d'),
                        'color' => $color,
                        'allDay' => true
                    ];
                } else {
                    $groupedEvents[$key]['titles'][] = $vs->vaccine->name;
                }
            }
        }

        // Convert grouped events to final format
        foreach ($groupedEvents as $group) {
            $title = 'Vaksin: ' . implode(', ', $group['titles']);
            $calendarEvents[] = [
                'title' => $title,
                'start' => $group['start'],
                'end' => $group['end'],
                'color' => $group['color'],
                'allDay' => true
            ];
        }

        // Check completion
        $totalVaccinesCount = $allVaccines->count();
        $completedVaccinesCount = count($doneVaccineIds);
        $allVaccinesCompleted = ($totalVaccinesCount > 0 && $totalVaccinesCount === $completedVaccinesCount);

        return view('dashboard.user.index', compact('user', 'patient', 'vaccineSchedules', 'calendarEvents', 'patientAgeMonths', 'allVaccinesCompleted'));
    })->name('user.dashboard');
    
    // Certificate (Only if all completed)
    Route::get('/certificate', function () {
        $patient = Auth::user()->patient;
        
        $totalVaccinesCount = Vaccine::count();
        $completedVaccinesCount = $patient ? $patient->vaccinePatients()->where('status', 'selesai')->count() : 0;
        
        if ($totalVaccinesCount === 0 || $completedVaccinesCount < $totalVaccinesCount) {
             return redirect()->route('user.dashboard')->with('error', 'Anda belum menyelesaikan semua tahapan imunisasi.');
        }

        return view('dashboard.user.certificate', compact('patient'));
    })->name('user.certificate');
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

    // Posyandus
    Route::post('/posyandus', [\App\Http\Controllers\AdminController::class, 'storePosyandu'])->name('admin.posyandus.store');
    Route::put('/posyandus/{posyandu}', [\App\Http\Controllers\AdminController::class, 'updatePosyandu'])->name('admin.posyandus.update');
    Route::delete('/posyandus/{posyandu}', [\App\Http\Controllers\AdminController::class, 'destroyPosyandu'])->name('admin.posyandus.destroy');



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
    Route::get('/users/create', [\App\Http\Controllers\AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('/users', [\App\Http\Controllers\AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/users/{user}', [\App\Http\Controllers\AdminController::class, 'updateUser'])->name('admin.users.update');
    
    Route::get('/history', [\App\Http\Controllers\AdminController::class, 'history'])->name('admin.history');
    Route::post('/history/store', [\App\Http\Controllers\AdminController::class, 'storeHistory'])->name('admin.history.store');
    Route::post('/history/certification', [\App\Http\Controllers\AdminController::class, 'certification'])->name('admin.history.certification');
    Route::delete('/history/rollback/{id}', [\App\Http\Controllers\AdminController::class, 'rollbackHistory'])->name('admin.history.rollback');
    
    Route::get('/logs', [\App\Http\Controllers\AdminController::class, 'logs'])->name('admin.logs');
    
    // Notifications
    Route::get('/notifications/config', [\App\Http\Controllers\NotificationController::class, 'configuration'])->name('admin.notifications.config'); // Renamed from index
    Route::get('/notifications/templates', [\App\Http\Controllers\NotificationController::class, 'templates'])->name('admin.notifications.templates');
    Route::put('/notifications/templates/{id}', [\App\Http\Controllers\NotificationController::class, 'updateTemplate'])->name('admin.notifications.templates.update');
    Route::get('/notifications/history', [\App\Http\Controllers\NotificationController::class, 'history'])->name('admin.notifications.history');
    
    // AJAX for WAHA
    Route::get('/notifications/scan', [\App\Http\Controllers\NotificationController::class, 'scan'])->name('admin.notifications.scan');
    Route::get('/notifications/status', [\App\Http\Controllers\NotificationController::class, 'status'])->name('admin.notifications.status');
    Route::post('/notifications/logout', [\App\Http\Controllers\NotificationController::class, 'logout'])->name('admin.notifications.logout');
    
    Route::get('/certificate/{patient}', function (App\Models\Patient $patient) {
        return view('dashboard.user.certificate', compact('patient'));
    })->name('admin.certificate');
});
