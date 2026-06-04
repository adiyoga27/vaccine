<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WahaService
{
    protected $baseUrl;
    protected $apiKey;
    protected $session;

    public function __construct()
    {
        $this->baseUrl = config('services.waha.api_url');
        $this->apiKey = config('services.waha.api_key');
        $this->session = config('services.waha.session');
    }

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

    public function start()
    {
        try {
            $response = Http::timeout(5)->withHeaders([
                'X-Api-Key' => $this->apiKey
            ])->post("{$this->baseUrl}/api/sessions/{$this->session}/start");

            return $response->json();
        } catch (\Exception $e) {
            Log::error("WAHA start error: " . $e->getMessage());
            return ['status' => 'ERROR'];
        }
    }

    public function stop()
    {
        try {
            $response = Http::timeout(5)->withHeaders([
                'X-Api-Key' => $this->apiKey
            ])->post("{$this->baseUrl}/api/sessions/{$this->session}/stop");

            return $response->json();
        } catch (\Exception $e) {
            Log::error("WAHA stop error: " . $e->getMessage());
            return ['status' => 'ERROR'];
        }
    }

    public function restart()
    {
        try {
            $response = Http::timeout(5)->withHeaders([
                'X-Api-Key' => $this->apiKey
            ])->post("{$this->baseUrl}/api/sessions/{$this->session}/restart");

            return $response->json();
        } catch (\Exception $e) {
            Log::error("WAHA restart error: " . $e->getMessage());
            return ['status' => 'ERROR'];
        }
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
