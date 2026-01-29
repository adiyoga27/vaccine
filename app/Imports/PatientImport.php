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
        $village = Village::where('name', 'LIKE', '%' . trim($row['dusun']) . '%')->first();

        // Find Posyandu
        $posyandu = null;
        if ($village && isset($row['posyandu'])) {
            $posyandu = \App\Models\Posyandu::where('village_id', $village->id)
                ->where('name', 'LIKE', '%' . trim($row['posyandu']) . '%')
                ->first();
        }

        // Parse Date
        $dob = $row['tanggal_lahir'];
        try {
            if (is_numeric($dob)) {
                $dob = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dob)->format('Y-m-d');
            } else {
                // Try format d/m/Y
                $dob = \Carbon\Carbon::createFromFormat('d/m/Y', $dob)->format('Y-m-d');
            }
        } catch (\Exception $e) {
            try {
                // Fallback to standard parsing
                $dob = \Carbon\Carbon::parse($dob)->format('Y-m-d');
            } catch (\Exception $ex) {
                // Keep original if all fails
            }
        }

        // Parse Gender
        $genderRaw = strtoupper(trim($row['jenis_kelamin']));
        $gender = 'male'; // Default
        if ($genderRaw == 'L' || $genderRaw == 'LAKI-LAKI') {
            $gender = 'male';
        } elseif ($genderRaw == 'P' || $genderRaw == 'PEREMPUAN') {
            $gender = 'female';
        }

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
            'posyandu_id' => $posyandu->id ?? null,
            'name' => $row['nama_anak'],
            'mother_name' => $row['nama_ibu'],
            'nik' => $row['nik'] ?? null,
            'date_birth' => $dob,
            'gender' => $gender,
            'address' => $row['alamat'],
            'phone' => $row['no_hp'],
        ]);

        return $user;
    }

    /**
     * Prepare data for validation.
     * Force NIK to be string to avoid scientific notation issues and max:16 numeric validation failure.
     *
     * @param array $data
     * @param int   $index
     * @return array
     */
    public function prepareForValidation($data, $index)
    {
        if (isset($data['nik'])) {
            // Explicitly cast to string. 
            // If it's a numeric value from Excel, this treats it as a string for validation rules.
            // Note: Very large numbers might interpret as scientific notation string if not carefully handled,
            // but typical NIK (16 digits) fits in standard conversion or should be treated as string in Excel.
            // Using number_format to be safe against scientific notation for pure numeric inputs.
            if (is_numeric($data['nik'])) {
                $data['nik'] = number_format($data['nik'], 0, '', '');
            } else {
                $data['nik'] = (string) $data['nik'];
            }
        }
        
        // Also ensure dusun and posyandu are strings
        if (isset($data['dusun'])) $data['dusun'] = (string) $data['dusun'];
        if (isset($data['posyandu'])) $data['posyandu'] = (string) $data['posyandu'];

        return $data;
    }

    public function rules(): array
    {
        return [
            'nama_anak' => 'required|string',
            'nama_ibu' => 'required|string',
            'nik' => 'nullable|string|max:16',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'dusun' => 'required',
            'posyandu' => 'nullable|string',
            'no_hp' => 'required',
        ];
    }
}
