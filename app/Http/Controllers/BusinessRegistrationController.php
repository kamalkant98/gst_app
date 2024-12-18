<?php

namespace App\Http\Controllers;

use App\Models\BusinessRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendEmailJob;
use App\Services\WhatsAppService;
use App\Services\OtpService;

class BusinessRegistrationController extends Controller
{

    protected $whatsAppService;
    protected $otpService;


    public function __construct(WhatsAppService $whatsAppService,OtpService $otpService)
    {
        $this->whatsAppService = $whatsAppService;
        $this->otpService = $otpService;
    }

    public function businessStore(Request $request)
    {
        // Validate the form input
        // $request->validate([
        //     'plan' => 'required', // Assuming QueryType is an array (from the multi-select)
        //     'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // Modify file types and max size as needed
        // ]);

        // Save the selected registration type
        $registrationType = $request->plan;

        // Handle file uploads
        $uploadedFiles = [];
        if ($request->hasFile('files')) {
            
            foreach ($request->file('files') as $file) {
                // Store the file and get the path
                $filePath = $file->store('business_documents', 'public');
                $uploadedFiles[] = $filePath;
            }
        }

        // Here you can save the data to the database if needed
        // Example of saving to the database (assuming a 'business_registrations' table exists):

        BusinessRegistration::create([
            'registration_type' => $registrationType,
            'documents' => json_encode($uploadedFiles), // Store files as JSON
            'user_inquiry_id' => $request['user_inquiry_id'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $plan = explode(",",$registrationType);
        $planData = [];
        foreach($plan as $value){

            $planData[] = [
                'plan_id' => $value,
                'plan' => config('global.business_registration.'.$value),
                'price' => config('global.business_registration_price.'.$value),
            ];
        }

        return response()->json(['message' => 'Payment Success', 'status' => 'success','data' => $planData], 200);
       
    }

    public function handlePaySuccess(Request $request)
    {
         // phone send otp start
         // Generate a random 6-digit OTP
        $otp = rand(100000, 999999);
        $phone = '+918890889144';

        //  // Send OTP to the provided phone number
         $this->otpService->sendOtp($phone, $otp);

         return response()->json(['message' => 'OTP sent successfully']);

        // phone send otp end


        //Send whatsapp message start

        $to = '+91'; // Recipient's WhatsApp number
        $message = 'Hello Sumit how are you'; // The message content

        try {
            $this->whatsAppService->sendMessage($to, $message);
            return response()->json(['status' => 'Message sent successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        //Send whatsapp message end

        //Send email start


        $data = [
            'email' => $request['email'],
            'title' => 'Welcome to our App',
            'message' => 'Thank you for registering with us!',
        ];

        // // Dispatch the job
        SendEmailJob::dispatch($data);

        //Send email end

    }


}
