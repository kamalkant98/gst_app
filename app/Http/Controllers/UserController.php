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
use App\Models\ScheduleCall;
use App\Models\Coupon;
use App\Models\TalkToExpert;
use App\Models\EmailTemplate;

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



        $template = EmailTemplate::whereIn('type',[1,3])->where('form_type','otp')->get();

        foreach ($template as $key => $value) {

            $message = str_replace("{client_name}", $data['name'],$value->description);
            $message = str_replace("{otp}", $data['otp'], $message);

            // if($value->type == 1){

            // // Send OTP to the provided phone number

            //     $phone = '+91'.$data['mobile'];
            //     $this->otpService->sendOtp($phone, $message);


            //     $to = '+91'.$data['mobile']; // Recipient's WhatsApp number
            //     $message = $message; // The message content

            //     try {
            //         $this->whatsAppService->sendMessage($to, $message);
            //         // return response()->json(['status' => 'Message sent successfully!'], 200);
            //     } catch (\Exception $e) {
            //         // return response()->json(['error' => $e->getMessage()], 500);
            //     }

            // }else

            if($value->type == 3){

                $data2 = [
                    'email' => $data['email'],
                    'title' => $value->subject,
                    'message' => $message,
                ];
                  // Dispatch the job
                SendEmailJob::dispatch($data2);

            }
        }
        $insData;
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


    public function calculatePlanForCall(Request $request){

        $data = $request->all();


        // return response()->json(['data'=>$hj]);
        // $QueryType = $data['QueryType'];
        // $queryTypeArr = explode(",",$QueryType);
        // return response()->json(['data'=>$queryTypeArr]);



        $getPlan = getCallPlanAmount($data['plan']);
        $amount =$getPlan['value'];
        $coupon=null;
        $coupon_id = null;
        $lessAmount=0;
        $inputCoupon ='';
        // $data['coupon'] = 'FIRST20%';
        if(isset($data['coupon'])){
        $inputCoupon = $data['coupon'];
        $queryTypeArr =[];

        $CalculateCoupon = CalculateCoupon($data['coupon'],$amount);

            if(isset($CalculateCoupon['finalAmount']) && isset($CalculateCoupon['getCoupon'])){
                $lessAmount = floor(($amount - $CalculateCoupon['finalAmount']) * 100) / 100;
                $amount = floor($CalculateCoupon['finalAmount'] * 100) / 100;
                $coupon = $CalculateCoupon['getCoupon'];
                $coupon_id= $coupon['id'];
            }else{
                $coupon = $CalculateCoupon;
            }
        }

        // return response()->json(['coupon'=>$coupon]);
        if($data['form_type'] == 'talk_to_tax_expert'){
            $QueryType = $data['QueryType'];
            $queryTypeArr = explode(",",$QueryType);

        }else{
            $queryTypeArr = $data['QueryType'];
        }
        // return response()->json(['data'=>$queryTypeArr ]);
        // return response()->json(['data'=>$queryTypeArr ]);

        $getQuery = Call_query_type($queryTypeArr);

        $QueryType = implode(', ', $queryTypeArr);
        $QueryTypeName = implode(', ', $getQuery);
        $setData = [
            'user_id' => $request['id'],
            'call_datetime' =>$request['datetime'],
            'language' =>$request['language'],
            'form_type' => $request['form_type'],
            'plan' => $request['plan'],
            'query_type'=>$QueryType,
            'coupon_id'=>$coupon_id,
            'total_amount'=> (float)$amount,
            'message'=> $request['other_query_message'],
        ];

        $getCall;

        if($data['form_type'] == 'talk_to_tax_expert'){

            $uploadedFiles = $request->file('document'); // Get all uploaded files
            $filePaths = []; // Array to store file paths

            // Define the destination path (within the public folder)
            $destinationPath = public_path('talk_to_TaxExpertFiles');

            // Create the uploads directory if it doesn't exist
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            // Loop through each file and move it
            foreach ($uploadedFiles as $file) {
                // Generate a unique filename
                $fileName = time() . '_' . $file->getClientOriginalName();

                // Move the file to the destination folder
                $file->move($destinationPath, $fileName);

                // Add the file path to the array
                $filePaths[] = asset('talk_to_TaxExpertFiles/' . $fileName);
            }

            if(isset($data['call_id']) && $data['call_id'] !='undefined' && $data['call_id'] > 0){
                $getCall = TalkToExpert::where('id', $data['call_id'])->first();
                $getCall->update($setData);

            }else{
                $getCall = TalkToExpert::create($setData);
            }

            return response()->json(['call_id'=>$getCall->id,'getPlan'=>$getPlan,'regarding'=>$QueryTypeName,'coupon'=>$coupon,'amount'=>$amount,'lessAmount'=>$lessAmount,'inputCoupon'=>$inputCoupon], 200);
        }else{
            if(isset($data['call_id']) && $data['call_id'] > 0){
                $getCall = ScheduleCall::where('id', $data['call_id'])->first();
                $getCall->update($setData);

            }else{
                $getCall = ScheduleCall::create($setData);
            }

            return response()->json(['call_id'=>$getCall->id,'getPlan'=>$getPlan,'regarding'=>$QueryTypeName,'coupon'=>$coupon,'amount'=>$amount,'lessAmount'=>$lessAmount,'inputCoupon'=>$inputCoupon], 200);

        }

        return response()->json(['message'=>'Something went wrong.' ],422);


    }


}






