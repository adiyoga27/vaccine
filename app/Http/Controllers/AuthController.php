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
            'mother_name' => 'required|string|min:2',
        ]);

        // Find patients with matching date of birth
        $patients = Patient::whereDate('date_birth', $request->date_birth)->get();

        if ($patients->isEmpty()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan. Pastikan tanggal lahir benar.']);
            }
            return back()->with('quick_login_error', 'Data tidak ditemukan. Pastikan tanggal lahir benar.');
        }

        // Check if mother_name contains the search term (partial match)
        $searchTerm = strtolower(trim($request->mother_name));
        $matchedPatient = null;

        foreach ($patients as $patient) {
            $motherNameLower = strtolower($patient->mother_name);
            // Check if any word in mother_name matches the search term
            if (str_contains($motherNameLower, $searchTerm)) {
                $matchedPatient = $patient;
                break;
            }
        }

        if (!$matchedPatient) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Nama Ibu tidak cocok. Silahkan coba lagi.']);
            }
            return back()->with('quick_login_error', 'Nama Ibu tidak cocok. Silahkan coba lagi.');
        }

        // Get the user associated with this patient
        $user = User::find($matchedPatient->user_id);
        
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Akun tidak ditemukan. Silahkan hubungi admin.']);
            }
            return back()->with('quick_login_error', 'Akun tidak ditemukan. Silahkan hubungi admin.');
        }

        // Log in the user
        Auth::login($user);
        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true, 
                'message' => 'Selamat datang, ' . $matchedPatient->name . '!',
                'redirect' => route('user.dashboard')
            ]);
        }

        return redirect()->route('user.dashboard');
    }
}
