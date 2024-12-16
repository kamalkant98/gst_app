<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendEmailJob;
use App\Services\WhatsAppService;
use App\Services\OtpService;


class UserController extends Controller
{
    protected $whatsAppService;
    protected $otpService;


    public function __construct(WhatsAppService $whatsAppService,OtpService $otpService)
    {
        $this->whatsAppService = $whatsAppService;
        $this->otpService = $otpService;
    }

    public function index(){

        $userData  = User::get();

        return response()->json(['message' => 'Data fetched successfully!','data'=> $userData]);


    }

    public function store(Request $request ){

        $request = $request->all();


        // phone send otp start
         // Generate a random 6-digit OTP
        // $otp = rand(100000, 999999);
        // $phone = '+918890889144';

        //  // Send OTP to the provided phone number
        //  $this->otpService->sendOtp($phone, $otp);
 
        //  return response()->json(['message' => 'OTP sent successfully']);
       
        // phone send otp end


        //Send whatsapp message start

        // $to = '+91'; // Recipient's WhatsApp number
        // $message = 'Hello Sumit how are you'; // The message content

        // try {
        //     $this->whatsAppService->sendMessage($to, $message);
        //     return response()->json(['status' => 'Message sent successfully!'], 200);
        // } catch (\Exception $e) {
        //     return response()->json(['error' => $e->getMessage()], 500);
        // }

        //Send whatsapp message end

        //Send email start
        

        // $data = [
        //     'email' => $request['email'],
        //     'title' => 'Welcome to our App',
        //     'message' => 'Thank you for registering with us!',
        // ];
    
        // // Dispatch the job
        // SendEmailJob::dispatch($data);

        //Send email end


        $dataAll = [
            'email' => $request['email'],
            'name' => 'Sumit',
            'password' =>  Hash::make( $request['password']),
        ];

        User::create($dataAll);

        return response()->json(['message' => 'Data fetched successfully!']);

    }


    public function generateOtp()
    {
        // Generate a 6-digit OTP
        $otp = rand(100000, 999999);

        return $otp;
    }

    public function generate_otp(Request $request ){
        $validator = Validator::make($request->all(), [
           'name' => 'required|string',
            'email' => 'required|email',
            'mobile' => 'required|string',
        ]);

        // Check if validation fails
        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(), // Get the error messages
            ], 422);
        }
        $otp = $this->generateOtp();
        $data = [
            'email' => $request['email'],
            'name' =>$request['name'],
            'mobile' =>$request['mobile'],
            'form_type' => $request['form_type'],
            'otp' => $otp
        ];

        if($request->id > 0){
            $inquiry = UserInquiry::where('id', $request->id)->first();
            $inquiry->update($data);
            return response()->json(['message' => 'OTP generated successfully!','data'=> $inquiry->id,'insertData'=>$data]);
        }else{
            $insertData = UserInquiry::create($data);
            return response()->json(['message' => 'OTP generated successfully!','data'=> $insertData->id,"insertData" => $insertData]);
        }
    }

    public function verifyOtp(Request $request)
    {

        // $data = $request->all();
        // return response()->json(['message' => 'OTP verified successfully.','data'=>$data]);
        // Define validation rules for the OTP and email
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'otp' => 'required|numeric',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Find the user inquiry record by email
        $userInquiry = UserInquiry::where('id', $request->id)->first();

        if (!$userInquiry) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        // Check if OTP has expired
        // if ($userInquiry->otp_expires_at && now()->gt($userInquiry->otp_expires_at)) {
        //     return response()->json(['error' => 'OTP has expired.'], 400);
        // }

        // Check if the OTP matches the one in the database
        if ($userInquiry->otp == $request->otp) {

            $userInquiry->update(['is_verified' => 1]);
            return response()->json(['message' => 'OTP verified successfully.','data' => $userInquiry]);
        } else {
            return response()->json(['error' => 'Invalid OTP.'], 400);
        }
    }

}






