<?php

namespace App\Services;

use Twilio\Rest\Client;

class WhatsAppService
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    }

    public function sendMessage($to, $message)
    {
        return $this->twilio->messages->create(
            'whatsapp:' . $to, // Recipient WhatsApp number with whatsapp: prefix
            [
                'from' => "whatsapp:".env('TWILIO_WHATSAPP_FROM'), // Your Twilio WhatsApp number
                'body' => $message, // Message content
            ]
        );
    }
}
