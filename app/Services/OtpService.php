<?php

namespace App\Services;

use Twilio\Rest\Client;

class OtpService
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
    }

    public function sendOtp($phoneNumber, $otp)
    {
        $message = "Your OTP is: {$otp}";

        $this->twilio->messages->create($phoneNumber, [
            'from' => env('TWILIO_FROM'),
            'body' => $message
        ]);
    }
}
