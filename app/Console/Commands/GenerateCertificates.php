<?php

namespace App\Console\Commands;

use App\Models\Patient;
use App\Models\Vaccine;
use App\Services\CertificateService;
use Illuminate\Console\Command;

class GenerateCertificates extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'certificate:generate';

    /**
     * The console command description.
     */
    protected $description = 'Generate certificates for patients who have completed all required vaccines';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $requiredVaccineIds = Vaccine::where('is_required', true)->pluck('id');

        if ($requiredVaccineIds->isEmpty()) {
            $this->info('No required vaccines defined. Skipping.');
            return self::SUCCESS;
        }

        $requiredCount = $requiredVaccineIds->count();

        // Find patients who don't have a certificate yet
        // but have completed all required vaccines
        $eligiblePatients = Patient::whereNull('certificate_number')
            ->whereHas('vaccinePatients', function ($query) use ($requiredVaccineIds) {
                $query->where('status', 'selesai')
                    ->whereIn('vaccine_id', $requiredVaccineIds);
            }, '>=', $requiredCount)
            ->get();

        $generated = 0;

        foreach ($eligiblePatients as $patient) {
            // Double-check: count distinct required vaccines completed
            $completedRequiredCount = $patient->vaccinePatients()
                ->where('status', 'selesai')
                ->whereIn('vaccine_id', $requiredVaccineIds)
                ->distinct('vaccine_id')
                ->count('vaccine_id');

            if ($completedRequiredCount >= $requiredCount) {
                CertificateService::generate($patient);
                $generated++;
                $this->line("  ✓ Generated certificate for: {$patient->name} ({$patient->certificate_number})");
            }
        }

        $this->info("Done. Generated {$generated} certificate(s).");

        return self::SUCCESS;
    }
}
