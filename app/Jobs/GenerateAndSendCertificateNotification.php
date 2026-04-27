<?php

namespace App\Jobs;

use App\Models\Patient;
use App\Models\NotificationTemplate;
use App\Models\NotificationLog;
use App\Services\CertificateService;
use App\Services\WahaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateAndSendCertificateNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $patientId;

    public function __construct($patientId)
    {
        $this->patientId = $patientId;
    }

    public function handle(WahaService $waha)
    {
        $patient = Patient::find($this->patientId);
        if (!$patient) return;

        // Generate Certificate
        CertificateService::generate($patient);

        if (!$patient->phone) return;

        $template = NotificationTemplate::where('slug', 'vaccine_completed')->first();
        if ($template) {
            try {
                $link = route('admin.certificate', ['patient' => $patient->id]);

                $message = NotificationTemplate::parse($template->content, $patient, [
                    'parent_name' => $patient->mother_name,
                    'child_name' => $patient->name,
                    'certificate_link' => $link
                ]);

                if (!$waha->isConnected()) {
                    return;
                }

                $response = $waha->sendMessage($patient->phone, $message);
                $body = $response->json();

                if ($response->successful() && isset($body['id']['fromMe'])) {
                    NotificationLog::create([
                        'to' => $patient->phone,
                        'message' => $message,
                        'status' => 'sent',
                        'response' => $response->body(),
                        'sent_at' => now(),
                    ]);
                } else {
                    NotificationLog::create([
                        'to' => $patient->phone,
                        'message' => $message,
                        'status' => 'failed',
                        'response' => $response->body()
                    ]);
                }
            } catch (\Exception $e) {
                NotificationLog::create([
                    'to' => $patient->phone,
                    'message' => $message ?? 'Error building message',
                    'status' => 'failed',
                    'response' => $e->getMessage()
                ]);
            }
        }
    }
}
