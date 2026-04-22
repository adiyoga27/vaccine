<?php

namespace App\Jobs;

use App\Models\Patient;
use App\Models\Posyandu;
use App\Models\Vaccine;
use App\Models\NotificationTemplate;
use App\Models\NotificationLog;
use App\Services\WahaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendVaccineApprovedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $patientId;
    protected $vaccineId;
    protected $posyanduId;

    public function __construct($patientId, $vaccineId, $posyanduId)
    {
        $this->patientId = $patientId;
        $this->vaccineId = $vaccineId;
        $this->posyanduId = $posyanduId;
    }

    public function handle(WahaService $waha)
    {
        $patient = Patient::with('vaccinePatients', 'village')->find($this->patientId);
        if (!$patient || !$patient->phone) return;

        $vaccine = Vaccine::find($this->vaccineId);
        if (!$vaccine) return;

        $posyandu = Posyandu::find($this->posyanduId);
        $posyanduName = $posyandu ? $posyandu->name : 'Posyandu';
        
        $vp = $patient->vaccinePatients->where('vaccine_id', $this->vaccineId)->first();
        if (!$vp || !$vp->vaccinated_at) return;

        $approvedTemplate = NotificationTemplate::where('slug', 'vaccine_approved')->first();
        if ($approvedTemplate) {
            try {
                $msg = NotificationTemplate::parse($approvedTemplate->content, $patient, [
                    'parent_name' => $patient->mother_name,
                    'child_name' => $patient->name,
                    'vaccine_name' => $vaccine->name,
                    'vaccinated_at' => \Carbon\Carbon::parse($vp->vaccinated_at)->format('d-m-Y'),
                    'posyandu_name' => $posyanduName,
                ]);

                $response = $waha->sendMessage($patient->phone, $msg);
                $body = $response->json();

                if ($response->successful() && isset($body['id']['fromMe'])) {
                    NotificationLog::create([
                        'to' => $patient->phone,
                        'message' => $msg,
                        'status' => 'sent',
                        'response' => $response->body(),
                        'sent_at' => now(),
                    ]);
                } else {
                    NotificationLog::create([
                        'to' => $patient->phone,
                        'message' => $msg,
                        'status' => 'failed',
                        'response' => $response->body()
                    ]);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Job send approved WA error: " . $e->getMessage());
            }
        }
    }
}
