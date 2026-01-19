<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Patient;
use App\Models\Village;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PatientImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Find village by name
        $village = Village::where('name', 'LIKE', '%' . trim($row['desa']) . '%')->first();

        // Auto-generate unique email (not used for login, just for DB uniqueness)
        $autoEmail = 'peserta_' . Str::random(8) . '@tandu-gemas.local';

        // Create User with auto-generated email
        $user = User::create([
            'name' => $row['nama_anak'],
            'email' => $autoEmail,
            'password' => Hash::make(Str::random(16)), // Random password, not used
            'role' => 'user',
        ]);

        // Create Patient
        Patient::create([
            'user_id' => $user->id,
            'village_id' => $village->id ?? null,
            'name' => $row['nama_anak'],
            'mother_name' => $row['nama_ibu'],
            'date_birth' => $row['tanggal_lahir'],
            'gender' => strtolower($row['jenis_kelamin']) == 'laki-laki' ? 'male' : 'female',
            'address' => $row['alamat'],
            'phone' => $row['no_hp'],
        ]);

        return $user;
    }

    public function rules(): array
    {
        return [
            'nama_anak' => 'required|string',
            'nama_ibu' => 'required|string',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'desa' => 'required',
            'no_hp' => 'required',
        ];
    }
}
