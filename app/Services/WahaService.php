<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WahaService
{
    protected $baseUrl = 'https://waha.galkasoft.id';
    protected $apiKey = 'Galkasoft2025Waha';
    protected $session = 'adiyoga';

    public function getSession()
    {
        $response = Http::withHeaders([
            'X-Api-Key' => $this->apiKey
        ])->get("{$this->baseUrl}/api/sessions/{$this->session}");

        return $response->json();
    }

    public function getQR()
    {
        $response = Http::withHeaders([
            'X-Api-Key' => $this->apiKey
        ])->get("{$this->baseUrl}/api/sessions/{$this->session}/auth/qr?format=image");

        // Return base64 or image data directly?
        // Let's assume we want base64 for easiest display
        if ($response->successful()) {
             return 'data:image/png;base64,' . base64_encode($response->body());
        }
        
        return null;
    }

    public function logout()
    {
        $response = Http::withHeaders([
            'X-Api-Key' => $this->apiKey
        ])->post("{$this->baseUrl}/api/sessions/{$this->session}/logout");

        return $response->json();
    }

    public function sendMessage($to, $message)
    {
        $response = Http::withHeaders([
            'X-Api-Key' => $this->apiKey
        ])->post("{$this->baseUrl}/api/send/whatsapp-text", [
            'chatId' => $to . '@c.us',
            'text' => $message,
            'session' => $this->session
        ]);

        return $response;
    }
}
