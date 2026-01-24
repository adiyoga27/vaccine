<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        try {
            if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return redirect()->intended('admin/dashboard');
            }

            return redirect()->intended('user/dashboard');
        }

        
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
        } catch (\Throwable $th) {
            throw $th;
        }

       

    }

    public function showRegister()
    {
        $villages = Village::all();
        return view('auth.register', compact('villages'));
    }

    public function register(Request $request)
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
            'phone' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'user'
            ]);

            Patient::create([
                'user_id' => $user->id,
                'village_id' => $request->village_id,
                'name' => $request->name, // Patient name same as User name for now or add field
                'mother_name' => $request->mother_name,
                'date_birth' => $request->date_birth,
                'address' => $request->address,
                'gender' => $request->gender,
                'phone' => $request->phone,
            ]);

            Auth::login($user);
        });

        return redirect('/user/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function quickLogin(Request $request)
    {
        $request->validate([
            'date_birth' => 'required|date',
            'child_name' => 'required|string|min:2',
        ]);

        // Find patients with matching date of birth
        $patients = Patient::with('village')
            ->whereDate('date_birth', $request->date_birth)
            ->get();

        \Illuminate\Support\Facades\Log::info('QuickLogin Debug:', [
            'date_birth' => $request->date_birth,
            'found_count' => $patients->count(),
            'search_term' => $request->child_name,
            'patient_names' => $patients->pluck('name')->toArray()
        ]);

        if ($patients->isEmpty()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan. Pastikan tanggal lahir benar.']);
            }
            return back()->with('quick_login_error', 'Data tidak ditemukan. Pastikan tanggal lahir benar.');
        }

        // Filter by child name (Improved Fuzzy Search)
        $searchTerm = strtolower(trim($request->child_name));
        $searchWords = explode(' ', $searchTerm);
        $searchNoSpaces = str_replace(' ', '', $searchTerm);
        
        $matchedPatients = $patients->filter(function ($patient) use ($searchWords, $searchNoSpaces) {
            $patientName = strtolower($patient->name);
            $patientNameNoSpaces = str_replace(' ', '', $patientName);

            // Check 1: ANY word from search term exists in patient name
            foreach ($searchWords as $word) {
                if (str_contains($patientName, $word)) {
                    return true;
                }
            }

            // Check 2: Match if spaces are removed (e.g. "Su Artini" == "Suartini")
            if (str_contains($patientNameNoSpaces, $searchNoSpaces) || str_contains($searchNoSpaces, $patientNameNoSpaces)) {
                return true;
            }

            return false;
        });

        if ($matchedPatients->isEmpty()) {
            // LOGGING FOR DEBUGGING
             \Illuminate\Support\Facades\Log::info('QuickLogin Failed Search:', [
                'date_birth' => $request->date_birth,
                'search_term' => $request->child_name,
                'available_names_for_dob' => $patients->pluck('name')->toArray()
            ]);

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Nama Anak tidak cocok. Silahkan coba lagi.']);
            }
            return back()->with('quick_login_error', 'Nama Anak tidak cocok. Silahkan coba lagi.');
        }

        // Return the list of matched patients for selection
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'multiple' => true, // Always show selection modal even for 1 result
                'message' => 'Ditemukan ' . $matchedPatients->count() . ' data. Silahkan pilih:',
                'patients' => $matchedPatients->map(function ($patient) {
                    return [
                        'id' => $patient->id,
                        'name' => $patient->name,
                        'mother_name' => $patient->mother_name,
                        'date_birth' => $patient->date_birth->format('d M Y'),
                        'village' => $patient->village->name ?? '-',
                    ];
                })->values()
            ]);
        }

        // Fallback for non-AJAX
        return back()->with('quick_login_error', 'Silahkan gunakan form pencarian.');
    }

    public function confirmQuickLogin(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
        ]);

        $patient = Patient::find($request->patient_id);

        if (!$patient) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']);
        }

        // Redirect to the public URL-based dashboard (no login required)
        return response()->json([
            'success' => true,
            'message' => 'Selamat datang, ' . $patient->name . '!',
            'redirect' => $patient->getAccessUrl()
        ]);
    }
}
