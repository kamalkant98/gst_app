<?php

namespace App\Services;

// use Twilio\Rest\Client;
use Illuminate\Support\Facades\Http;

class OtpService
{
    protected $apiUrl;
    protected $apiKey;
    protected $senderId;
    protected $user;
    protected $password;
    protected $templateId;

    public function __construct()
    {
        $this->apiUrl = config('services.sms.url');
        $this->apiKey = config('services.sms.api_key');
        $this->senderId = config('services.sms.sender_id');
        $this->user = config('services.sms.user');
        $this->password = config('services.sms.password');
        $this->templateId = config('services.sms.template_id');
    }

    public function sendOtp($phoneNumber, $messageData)
    {
        // $message = strip_tags($messageData);

        // $this->twilio->messages->create($phoneNumber, [
        //     'from' => env('TWILIO_FROM'),
        //     'body' => $message
        // ]);

        $response = Http::get($this->apiUrl, [
            'APIKey' => $this->apiKey,
            'senderid' => $this->senderId,
            'channel' => 'trans',
            'DCS' => '0',
            'flashsms' => '0',
            'number' => $phoneNumber,
            'text' => $messageData,
            'DLTTemplateId' => $this->templateId,
            'user' => $this->user,
            'password' => $this->password,
        ]);

        return $response->json();
    }
}
