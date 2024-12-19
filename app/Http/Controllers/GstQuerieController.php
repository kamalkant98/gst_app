<?php

namespace App\Http\Controllers;

use App\Models\BusinessRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendEmailJob;
use App\Models\GstQuerie;
use App\Services\WhatsAppService;
use App\Services\OtpService;

class GstQuerieController extends Controller
{

    protected $whatsAppService;
    protected $otpService;


    public function __construct(WhatsAppService $whatsAppService,OtpService $otpService)
    {
        $this->whatsAppService = $whatsAppService;
        $this->otpService = $otpService;
    }

    public function gstQuerieStore(Request $request)
    {
       

        print_r($request->all());

    
        
        // GstQuerie::create([
        //     'gst_number'=> $request['user_inquiry_id'],
        //     'type_of_taxpayer'=> $request['user_inquiry_id'],
        //     'return_filling_frequency'=> $request['user_inquiry_id'],
        //     'type_of_return'=> $request['user_inquiry_id'],
        //     'service_type' => $request['user_inquiry_id'],
        //     'user_inquiry_id' => $request['user_inquiry_id']
        // ]);

        // $plan = explode(",",$registrationType);
        $planData = [];
        // foreach($plan as $value){

        //     $planData[] = [
        //         'plan_id' => $value,
        //         'plan' => config('global.business_registration.'.$value),
        //         'price' => config('global.business_registration_price.'.$value),
        //     ];
        // }

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
