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
            'X-Api-Key' => $this->apiKey,
            'Accept' => 'image/png'
        ])->get("{$this->baseUrl}/api/{$this->session}/auth/qr?format=image");

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

    /**
     * @return \Illuminate\Http\Client\Response
     */
    public function sendMessage($to, $message)
    {
        // Format Phone Number
        // 1. Remove non-numeric characters (handles +62 -> 62)
        $to = preg_replace('/[^0-9]/', '', $to);

        // 2. If starts with 08, replace 0 with 62
        if (substr($to, 0, 2) === '08') {
             $to = '62' . substr($to, 1);
        }

        $response = Http::withHeaders([
            'X-Api-Key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->post("{$this->baseUrl}/api/sendText", [
            'chatId' => $to . '@c.us',
            'text' => $message,
            'session' => $this->session
        ]);

        return $response;
    }
}
