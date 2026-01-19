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
        // "Scheduled" means today matches their calculated Start Date based on birthdate + min_age
        // However, schedules are typically simpler in this system.
        // Let's iterate all patients and check if any of their "Next Vaccine" start date is Today.
        
        $patients = \App\Models\Patient::with(['vaccinePatients', 'village'])->get();
        $vaccines = \App\Models\Vaccine::all();
        $template = \App\Models\NotificationTemplate::where('slug', 'daily_reminder')->first();

        if (!$template) {
            $this->error('Template daily_reminder not found!');
            return;
        }

        $count = 0;
        foreach ($patients as $patient) {
            $doneVaccineIds = $patient->vaccinePatients->where('status', 'selesai')->pluck('vaccine_id')->toArray();
            
            foreach ($vaccines as $vaccine) {
                if (in_array($vaccine->id, $doneVaccineIds)) continue;

                // Calculate Start Date: DOB + Min Age (Months)
                $startDate = \Carbon\Carbon::parse($patient->date_birth)->addMonths((int) $vaccine->minimum_age);
                
                // If Today IS the Start Date (exact match for "Hari Ini")
                if ($startDate->isToday()) {
                    
                    // Prepare Message
                    $message = $template->content;
                    $message = str_replace('[mother_name]', $patient->mother_name, $message);
                    $message = str_replace('[vaccine_name]', $vaccine->name, $message);
                    $message = str_replace('[patient_name]', $patient->name, $message);
                    $message = str_replace('[village_name]', $patient->village->name ?? '-', $message);
                    // For Posyandu, we might check if there's a scheduled event or just generic
                    // Let's try to find a Schedule for this Village + Vaccine today
                    $schedule = \App\Models\VaccineSchedule::where('village_id', $patient->village_id)
                                ->whereDate('scheduled_at', now())
                                ->whereHas('vaccines', function($q) use ($vaccine) {
                                    $q->where('vaccines.id', $vaccine->id);
                                })->first();

                    // For Posyandu, list all available in the village
                    $posyandus = \App\Models\Posyandu::where('village_id', $patient->village_id)->pluck('name')->toArray();
                    $posyanduName = empty($posyandus) ? 'Posyandu Terdekat' : implode(', ', $posyandus);
                    
                    // Prepare Message
                    $message = \App\Models\NotificationTemplate::parse($template->content, $patient, [
                        'vaccine_name' => $vaccine->name,
                        'posyandu_name' => $posyanduName,
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
            }
        }

        $this->info("Done. Sent $count reminders.");
    }
}
