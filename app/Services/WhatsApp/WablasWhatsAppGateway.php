<?php

namespace App\Services\WhatsApp;

use App\Contracts\WhatsAppGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WablasWhatsAppGateway implements WhatsAppGateway
{
    public function __construct(private readonly string $token)
    {
    }

    public function send(string $to, string $message): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->timeout(8)->post('https://pati.wablas.com/api/send-message', [
                'phone' => $to,
                'message' => $message,
            ]);

            if (! $response->successful()) {
                Log::warning('Wablas WhatsApp send failed', ['status' => $response->status(), 'body' => $response->body()]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('Wablas WhatsApp exception', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
