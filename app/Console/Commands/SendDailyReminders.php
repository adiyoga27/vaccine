<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendDailyReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vaccine:send-daily-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send WhatsApp reminders for daily vaccinations';

    protected $waha;

    public function __construct(\App\Services\WahaService $waha)
    {
        parent::__construct();
        $this->waha = $waha;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning for daily vaccination reminders...');

        // Logic to find patients who have a vaccine "Scheduled" for TODAY
        // "Scheduled" means VaccinePatient status is 'pengajuan' and schedule_at is today

        $schedules = \App\Models\VaccinePatient::with(['patient', 'vaccine', 'village'])
            ->where('status', 'pengajuan')
            ->whereDate('schedule_at', '>=', \Carbon\Carbon::today())
            ->get();

        $template = \App\Models\NotificationTemplate::where('slug', 'daily_reminder')->first();

        if (!$template) {
            $this->error('Template daily_reminder not found!');
            return;
        }

        $count = 0;
        foreach ($schedules as $schedule) {
            $patient = $schedule->patient;
            $vaccine = $schedule->vaccine;

            if (!$patient || !$patient->phone)
                continue;

            // Start of restored posyandu logic
            // For Posyandu, list all available in the village
            $posyandus = \App\Models\Posyandu::where('village_id', $patient->village_id)->pluck('name')->toArray();
            $posyanduName = empty($posyandus) ? 'Posyandu Terdekat' : implode(', ', $posyandus);
            // End of restored posyandu logic

            // Prepare Message
            $ageMonths = \Carbon\Carbon::parse($patient->date_birth)->floatDiffInMonths(now()); // Age in months
            $message = \App\Models\NotificationTemplate::parse($template->content, $patient, [
                'parent_name' => $patient->mother_name,
                'child_name' => $patient->name,
                'vaccine_name' => $vaccine->name,
                'posyandu_name' => $posyanduName,
                'age' => number_format($ageMonths, 1) . ' Bulan',
            ]);

            // Send
            try {
                $response = $this->waha->sendMessage($patient->phone, $message);
                $body = $response->json();

                if ($response->successful() && isset($body['id']['fromMe'])) {
                    // Log Success
                    \App\Models\NotificationLog::create([
                        'to' => $patient->phone,
                        'message' => $message,
                        'status' => 'sent',
                        'response' => $response->body(),
                        'sent_at' => now(),
                    ]);
                    $this->info("Sent reminder to {$patient->name} ({$patient->phone}) for {$vaccine->name}");
                    $count++;
                } else {
                    // Log API Failure
                    \App\Models\NotificationLog::create([
                        'to' => $patient->phone,
                        'message' => $message,
                        'status' => 'failed',
                        'response' => $response->body()
                    ]);
                    $this->error("Failed to send to {$patient->name}: " . $response->body());
                }
            } catch (\Exception $e) {
                \App\Models\NotificationLog::create([
                    'to' => $patient->phone,
                    'message' => $message,
                    'status' => 'failed',
                    'response' => $e->getMessage()
                ]);
                $this->error("Exception sending to {$patient->name}: " . $e->getMessage());
            }
        }

        $this->info("Done. Sent $count reminders.");
    }
}
