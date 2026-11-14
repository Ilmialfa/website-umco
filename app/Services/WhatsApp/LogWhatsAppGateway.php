<?php

namespace App\Services\WhatsApp;

use App\Contracts\WhatsAppGateway;
use Illuminate\Support\Facades\Log;

class LogWhatsAppGateway implements WhatsAppGateway
{
    public function send(string $to, string $message): bool
    {
        Log::info('WhatsApp notification (log driver)', [
            'to' => $to,
            'message' => $message,
        ]);

        return true;
    }
}
