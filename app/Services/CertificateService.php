<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Vaccine;
use App\Models\CertificateSetting;

class CertificateService
{
    /**
     * Check if a patient has completed all required vaccines and generate certificate if eligible.
     */
    public static function checkAndGenerate(Patient $patient): bool
    {
        // Don't regenerate if already has certificate
        if ($patient->certificate_number) {
            return false;
        }

        $requiredVaccineIds = Vaccine::where('is_required', true)->pluck('id');

        if ($requiredVaccineIds->isEmpty()) {
            return false;
        }

        $completedRequiredCount = $patient->vaccinePatients()
            ->where('status', 'selesai')
            ->whereIn('vaccine_id', $requiredVaccineIds)
            ->count();

        if ($completedRequiredCount >= $requiredVaccineIds->count()) {
            self::generate($patient);
            return true;
        }

        return false;
    }

    /**
     * Generate a certificate for the patient.
     * This sets completed_vaccination_at, certificate_number, and certificate settings snapshot.
     */
    public static function generate(Patient $patient): void
    {
        // Avoid regenerating if already exists
        if ($patient->certificate_number) {
            return;
        }

        $lastRecord = $patient->vaccinePatients()->where('status', 'selesai')->latest('updated_at')->first();
        $completionDate = $lastRecord ? $lastRecord->updated_at : now();
        $month = $completionDate->month;
        $year = $completionDate->year;

        $romanMonths = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
        $romanMonth = $romanMonths[$month] ?? 'I';

        // Find the highest sequence number for this month/year
        $suffix = "/{$romanMonth}/ISTG/{$year}";
        $lastCert = Patient::whereNotNull('certificate_number')
            ->where('certificate_number', 'like', '%' . $suffix)
            ->orderByRaw("CAST(SUBSTRING_INDEX(certificate_number, '/', 1) AS UNSIGNED) DESC")
            ->value('certificate_number');

        $sequence = 1;
        if ($lastCert) {
            $parts = explode('/', $lastCert);
            $sequence = intval($parts[0]) + 1;
        }

        $certNum = sprintf("%03d/%s/ISTG/%s", $sequence, $romanMonth, $year);

        // Get current certificate settings and snapshot to patient
        $settings = CertificateSetting::current();

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

    /**
     * Check if patient should lose their certificate (e.g. after rollback).
     * Clears certificate fields if required vaccines are no longer all completed.
     */
    public static function checkAndRevoke(Patient $patient): void
    {
        $requiredCount = Vaccine::where('is_required', true)->count();
        $completedRequiredCount = $patient->vaccinePatients()
            ->where('status', 'selesai')
            ->whereHas('vaccine', fn($q) => $q->where('is_required', true))
            ->count();

        if ($completedRequiredCount < $requiredCount) {
            $patient->update([
                'certificate_number' => null,
                'completed_vaccination_at' => null,
            ]);
        }
    }
}
