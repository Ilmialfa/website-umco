<?php

namespace App\Services\WhatsApp;

use App\Contracts\WhatsAppGateway;
use App\Support\Settings;

class WhatsAppManager
{
    public static function gateway(): WhatsAppGateway
    {
        $driver = (string) Settings::get('whatsapp_templates.gateway_driver', 'log');
        $token = trim((string) Settings::get('whatsapp_templates.gateway_token', ''));

        if ($token === '') {
            return new LogWhatsAppGateway;
        }

        return match ($driver) {
            'fonnte' => new FonnteWhatsAppGateway($token),
            'wablas' => new WablasWhatsAppGateway($token),
            default => new LogWhatsAppGateway,
        };
    }
}
