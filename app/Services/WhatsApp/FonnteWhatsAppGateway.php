<?php

namespace App\Services\WhatsApp;

use App\Contracts\WhatsAppGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteWhatsAppGateway implements WhatsAppGateway
{
    public function __construct(private readonly string $token)
    {
    }

    public function send(string $to, string $message): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->timeout(8)->asForm()->post('https://api.fonnte.com/send', [
                'target' => $to,
                'message' => $message,
            ]);

            if (! $response->successful()) {
                Log::warning('Fonnte WhatsApp send failed', ['status' => $response->status(), 'body' => $response->body()]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('Fonnte WhatsApp exception', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
