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
        $histories = $patient ? $patient->vaccinePatients()->with('vaccine')->get() : [];
        $vaccines = Vaccine::all(); // For dropdown
        
        return view('dashboard.user.index', compact('user', 'patient', 'histories', 'vaccines'));
    })->name('user.dashboard');
    
    Route::post('/request-vaccine', function (Request $request) {
        $request->validate([
           'vaccine_id' => 'required',
           'request_date' => 'required|date' 
        ]);
        
        // Auto assign village (mock logic for now, pick first or random if not managed)
        // Ideally Patient should belongTo Village, but schema linked VaccinePatient to Village.
        // We will assign a default village for now or pick from request if added.
        $village = Village::first(); 

        VaccinePatient::create([
            'village_id' => $village->id,
            'patient_id' => Auth::user()->patient->id,
            'vaccine_id' => $request->vaccine_id,
            'request_date' => $request->request_date,
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
    Route::delete('/schedules/{schedule}', [\App\Http\Controllers\AdminController::class, 'destroySchedule'])->name('admin.schedules.destroy');

    // Monitoring
    Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
    Route::get('/history', [\App\Http\Controllers\AdminController::class, 'history'])->name('admin.history');
    Route::get('/logs', [\App\Http\Controllers\AdminController::class, 'logs'])->name('admin.logs');
    
    Route::post('/approve/{id}', function ($id) {
        $vp = VaccinePatient::findOrFail($id);
        $vp->update([
            'status' => 'selesai',
            'vaccinated_at' => now()
        ]);
        return back()->with('success', 'Status updated to Selesai');
    })->name('admin.approve');
});
