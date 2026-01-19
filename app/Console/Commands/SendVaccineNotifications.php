<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Patient;
use App\Models\Vaccine;
use App\Models\VaccinePatient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendVaccineNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-vaccine-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send WhatsApp notifications for upcoming vaccinations via WAHA';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting vaccine notification check...');

        $patients = Patient::with(['user', 'vaccinePatients'])->get();
        $vaccines = Vaccine::orderBy('minimum_age')->get();
        
        $wahaUrl = env('WAHA_API_URL', 'http://localhost:3000');
        // $wahaKey = env('WAHA_API_KEY'); // If needed

        foreach ($patients as $patient) {
            
            // Check valid phone number
            if (!$patient->phone) {
                continue;
            }

            foreach ($vaccines as $vaccine) {
                // Calculate Start Date (Birth Date + Min Age)
                $startDate = Carbon::parse($patient->date_birth)->addMonths($vaccine->minimum_age)->startOfDay();
                $today = now()->startOfDay();

                // Logic 1: Exact Start Date (Send Notification Today)
                // "pas dia sudah masuk langsung cronjobnya mengirimkan notif"
                if ($today->equalTo($startDate)) {
                    
                    // Check if already vaccinated (Done)
                    $isDone = $patient->vaccinePatients
                                ->where('vaccine_id', $vaccine->id)
                                ->where('status', 'selesai')
                                ->isNotEmpty();

                    if ($isDone) {
                        continue;
                    }

                    // Send Notification
                    $this->sendNotification($wahaUrl, $patient, $vaccine, $startDate);
                }
            }
        }

        $this->info('Notification check completed.');
    }

    private function sendNotification($url, $patient, $vaccine, $date)
    {
        $message = "Halo Bunda {$patient->mother_name},\n\n";
        $message .= "Ini adalah pengingat dari TANDU GEMAS.\n";
        $message .= "Saat ini Ananda *{$patient->name}* sudah memasuki jadwal untuk imunisasi *{$vaccine->name}*.\n";
        $message .= "Silakan kunjungi Posyandu terdekat segera.\n\n";
        $message .= "Jadwal dimulai: " . $date->format('d F Y') . "\n";
        $message .= "Terima kasih.";

        try {
            // Adjust payload format based on WAHA documentation
            // Assuming WAHA supports POST /api/sendText
            $response = Http::post("{$url}/api/sendText", [
                'chatId' => $this->formatPhoneNumber($patient->phone),
                'text' => $message,
                'session' => 'default' // Default session name usually
            ]);

            if ($response->successful()) {
                $this->info("Notification sent to {$patient->name} for {$vaccine->name}");
                Log::info("WAHA Notification sent to {$patient->phone}");
            } else {
                $this->error("Failed to send to {$patient->phone}: " . $response->body());
                Log::error("WAHA Failed: " . $response->body());
            }

        } catch (\Exception $e) {
            $this->error("Error sending notification: " . $e->getMessage());
            Log::error("WAHA Error: " . $e->getMessage());
        }
    }

    private function formatPhoneNumber($phone)
    {
        // Standardize to WAHA format (e.g., 628123456789@c.us)
        // Basic Indonesia format handling
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        return $phone . '@c.us';
    }
}
