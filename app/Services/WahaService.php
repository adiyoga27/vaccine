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
        try {
            $response = Http::timeout(5)->withHeaders([
                'X-Api-Key' => $this->apiKey
            ])->get("{$this->baseUrl}/api/sessions/{$this->session}");

            return $response->json();
        } catch (\Exception $e) {
            Log::error("WAHA getSession error: " . $e->getMessage());
            return ['status' => 'OFFLINE', 'error' => $e->getMessage()];
        }
    }

    public function getQR()
    {
        try {
            $response = Http::timeout(5)->withHeaders([
                'X-Api-Key' => $this->apiKey,
                'Accept' => 'image/png'
            ])->get("{$this->baseUrl}/api/{$this->session}/auth/qr?format=image");

            if ($response->successful()) {
                return 'data:image/png;base64,' . base64_encode($response->body());
            }
        } catch (\Exception $e) {
            Log::error("WAHA getQR error: " . $e->getMessage());
        }
        
        return null;
    }

    public function logout()
    {
        try {
            $response = Http::timeout(5)->withHeaders([
                'X-Api-Key' => $this->apiKey
            ])->post("{$this->baseUrl}/api/sessions/{$this->session}/logout");

            return $response->json();
        } catch (\Exception $e) {
            Log::error("WAHA logout error: " . $e->getMessage());
            return ['status' => 'ERROR'];
        }
    }

    /**
     * @return \Illuminate\Http\Client\Response|null
     */
    public function sendMessage($to, $message)
    {
        try {
            // Format Phone Number
            $to = preg_replace('/[^0-9]/', '', $to);

            if (substr($to, 0, 2) === '08') {
                $to = '62' . substr($to, 1);
            }

            $response = Http::timeout(5)->withHeaders([
                'X-Api-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post("{$this->baseUrl}/api/sendText", [
                'chatId' => $to . '@c.us',
                'text' => $message,
                'session' => $this->session
            ]);

            return $response;
        } catch (\Exception $e) {
            Log::error("WAHA sendMessage error: " . $e->getMessage());
            return null;
        }
    }

    public function isConnected(): bool
    {
        $session = $this->getSession();
        return isset($session['status']) && $session['status'] === 'WORKING';
    }
}
