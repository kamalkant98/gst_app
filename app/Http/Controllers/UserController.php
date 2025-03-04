<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Jobs\SendEmailJob;
use App\Services\WhatsAppService;
use App\Services\OtpService;
use App\Models\ScheduleCall;
use App\Models\Coupon;
use App\Models\TalkToExpert;
use App\Models\EmailTemplate;
use Carbon\Carbon;
use App\Models\Documents;


class UserController extends Controller
{
    protected $whatsAppService;
    protected $otpService;


    public function __construct(WhatsAppService $whatsAppService,OtpService $otpService)
    {
        // $this->whatsAppService = $whatsAppService;
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
            'name' => ucwords($request['name']),
            'mobile' =>$request['mobile'],
            'form_type' => $request['form_type'],
            'otp' => $otp,
            'otp_expires_at'=> now()->addMinutes(5),
        ];



        $template = EmailTemplate::whereIn('type',[1,3])->where('form_type','otp')->get();

        foreach ($template as $key => $value) {

            $message = str_replace("{client_name}", $data['name'],$value->description);
            $message = str_replace("{otp}", $data['otp'], $message);

            // if($value->type == 1){

            // Send OTP to the provided phone number
            $mg = "##var## is your one-time password for verification at TaxDunia. Valid for 5 minutes.";
            $mg = str_replace("##var##", $data['otp'], $mg);

            $phone = $data['mobile'];
            // $this->otpService->sendOtp($phone, $mg);


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
                    'filePaths'=>[]
                ];
                  // Dispatch the job
                SendEmailJob::dispatch($data2);

            }
        }
        $insData;
        if($request->id > 0){
            $inquiry = UserInquiry::where('id', $request->id)->first();
            $inquiry->update($data);

            return response()->json(['message' => 'OTP generated successfully!','data'=> $inquiry->id]); //'insertData'=>$data
        }else{
            $insertData = UserInquiry::create($data);
            return response()->json(['message' => 'OTP generated successfully!','data'=> $insertData->id]); //"insertData" => $insertData
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
            return response()->json(['errors' => $validator->errors()], 200);
        }

        // Find the user inquiry record by email
        $userInquiry = UserInquiry::where('id', $request->id)->first();

        if (!$userInquiry) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        // Check if OTP has expired
        // if ($userInquiry->otp_expires_at && now()->gt($userInquiry->otp_expires_at)) {
        if (now()->gt(Carbon::parse($userInquiry->otp_expires_at))) {

            return response()->json(['error' => 'OTP has expired.'], 404);
        }
        dd(now()->gt($userInquiry->otp_expires_at));
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


        if($data['form_type'] == 'talk_to_tax_expert'){
            $getPlan = getCallPlanAmount($data['plan']);
            $amount =$getPlan['value'];
            $defaultOfferAmount =0;
            $subtotal = 0;
            $gstCharge = 0;
            $coupon=null;
            $coupon_id = null;
            $defaultOffer_id = null;
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
            $subtotal = $amount;

            $getDefaulOffer = Coupon::where(['form_type'=>'talk_to_tax_expert','status'=>'active'])->where('expires_at', '>=', Carbon::now())->first();

            if($getDefaulOffer){
                $CalculateCoupon = CalculateCoupon($getDefaulOffer['code'],$amount);
                // dd($getDefaulOffer['code']);
                if(isset($CalculateCoupon['finalAmount']) && isset($CalculateCoupon['getCoupon'])){
                    $defaultOfferAmount = $subtotal; // floor(($amount - $CalculateCoupon['finalAmount']) * 100) / 100;
                    $subtotal = floor($CalculateCoupon['finalAmount'] * 100) / 100;
                    // $coupon = $CalculateCoupon['getCoupon'];
                    // $coupon_id= $CalculateCoupon['getCoupon']['id'];
                    $defaultOffer_id =$CalculateCoupon['getCoupon']['id'];
                }
            }
            $gstCharge = ($subtotal * 18) / 100;
            $gstCharge = number_format((float)$gstCharge, 2, '.', '');
            $amount = $subtotal + $gstCharge;
            $amount = number_format((float)$amount, 2, '.', '');

            $r_value = roundOffAmount($amount);
            $roundOff = $r_value['difference'];
            $amount = $r_value['roundedValue'];
            // dd($getDefaulOffer);

            // return response()->json(['coupon'=>$coupon]);
            if($data['form_type'] == 'talk_to_tax_expert'){
                // $QueryType = $data['QueryType'];
                $queryTypeArr[] =  $data['queryType'];

            }else{
                $queryTypeArr[] = $data['queryType'];
            }
            // return response()->json(['data'=>$queryTypeArr ]);
            // return response()->json(['data'=>$queryTypeArr ]);

            $getQuery = Call_query_type($queryTypeArr);
            // return response()->json(['data'=>$getQuery]);
            $QueryType = $data['queryType']; //implode(', ', $queryTypeArr);
            $QueryTypeName = implode(', ', $getQuery);
            $setData = [
                'user_id' => $request['id'],
                'call_datetime' =>$request['datetime'],
                'language' =>$request['language'],
                'form_type' => $request['form_type'],
                'plan' => $request['plan'],
                'query_type'=>$QueryType,
                'coupon_id'=>$coupon_id,
                'default_discount'=>$defaultOffer_id,
                'total_amount'=> (float)$amount,
                'message'=> $request['other_query_message'],
            ];


            $formattedDate = $request['datetime'];
            if($request['datetime']){
                $formattedDate = date("Y-m-d H:i:s", strtotime($request['datetime']));
            }else{
                $formattedDate = null; //date("Y-m-d H:i:s", strtotime($request['datetime']));
            }

            $setData['call_datetime'] = $formattedDate;

            $getCall;



            // $uploadedFiles = $request->file('document'); // Get all uploaded files
            // $filePaths = []; // Array to store file paths

            // // Define the destination path (within the public folder)
            // $destinationPath = public_path('talk_to_TaxExpertFiles');

            // // Create the uploads directory if it doesn't exist
            // if (!file_exists($destinationPath)) {
            //     mkdir($destinationPath, 0777, true);
            // }

            // // Loop through each file and move it
            // if($request->file('document')){
            //     foreach ($uploadedFiles as $file) {
            //         // Generate a unique filename
            //         $fileName = time() . '_' . $file->getClientOriginalName();

            //         // Move the file to the destination folder
            //         $file->move($destinationPath, $fileName);

            //         // Add the file path to the array
            //         $filePaths[] = asset('talk_to_TaxExpertFiles/' . $fileName);
            //     }
            //     $setData['documents'] = $filePaths && count($filePaths) > 0 ? json_encode($filePaths) : '';
            // }

            // $setData['documents'] = $filePaths;

            // return response()->json(['data'=>$setData]);

            if(isset($data['call_id']) && $data['call_id'] !='undefined' && $data['call_id'] > 0){
                $getCall = TalkToExpert::where('id', $data['call_id'])->first();
                $getCall->update($setData);

            }else{
                $getCall = TalkToExpert::create($setData);
            }
            // $this->sendMeassage($data['id'],'talk_to_tax_expert',$getCall['id']);

            Documents::where('query_id',$getCall->id)->where('form_type','talk_to_tax_expert')->delete();
            if($request->uploadedFile && count($request->uploadedFile) > 0){
                foreach ($request->uploadedFile as $file) {
                    $filePath = public_path('tmp_uploads/'. $file); // Set the correct file path
                    $newPath = public_path('uploads/' . $file);
                    if (File::exists($filePath) || File::exists($newPath)) {
                        if(File::exists($filePath)){
                            File::move($filePath, $newPath);
                        }

                        Documents::create([
                            'query_id' => $getCall->id,
                            'file_url' => $file,
                            'form_type' => 'talk_to_tax_expert',
                        ]);
                    }

                }
            }

            return response()->json(['call_id'=>$getCall->id,'getPlan'=>$getPlan,'regarding'=>$QueryTypeName,'coupon'=>$coupon,'amount'=>number_format($amount,2),'lessAmount'=>number_format($lessAmount,2),'inputCoupon'=>$inputCoupon,'subtotal'=>number_format($subtotal,2),'gstCharge'=>number_format($gstCharge,2),'defaultOfferAmount'=>number_format($defaultOfferAmount,2),'roundOff'=>$roundOff], 200);
        }else{

            $setData = [
                'user_id' => $request['id'],
                'call_datetime'=>$request['datetime'],
                'language' =>$request['language'],
                'form_type' => $request['form_type'],
                'call_when'=>$request['selectTime']
            ];


            $formattedDate = $request['datetime'];
            if($request['datetime']){
                $formattedDate = date("Y-m-d H:i:s", strtotime($request['datetime']));
            }else{
                $formattedDate = null; //date("Y-m-d H:i:s", strtotime($request['datetime']));
            }

            $setData['call_datetime'] = $formattedDate;

            // dd($setData);
            $getCall = ScheduleCall::where('user_id', $data['id'])->first();
            if(isset($data['call_id']) && $data['call_id'] > 0 && $data['call_id'] != 'undefined'){
                $getCall = ScheduleCall::where('id', $data['call_id'])->first();
                $getCall->update($setData);

            }else if(isset($data['id']) && $data['id'] > 0 && $data['id'] != 'undefined' && $getCall){
                $getCall->update($setData);
            }else{
                $getCall = ScheduleCall::create($setData);
            }

            commonSendMeassage($data['id'],'schedule_call',$getCall['id']);
            $redirect_url = env('CALL_BACK_URL');
            return response()->json(['redirect_url'=>$redirect_url], 200);

            // return response()->json(['call_id'=>$getCall->id,'getPlan'=>$getPlan,'regarding'=>$QueryTypeName,'coupon'=>$coupon,'amount'=>$amount,'lessAmount'=>$lessAmount,'inputCoupon'=>$inputCoupon,'subtotal'=>$subtotal,'gstCharge'=>$gstCharge], 200);

        }

        return response()->json(['message'=>'Something went wrong.' ],422);


    }



    public function commonUploadFile(Request $request)
    {
        // Validate the files input
        // $request->validate([
        //     'files.*' => 'required|file|mimes:jpg,png,pdf|max:2048', // Adjust rules as needed
        // ]);

        $validator = Validator::make($request->all(), [
            'files.*' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,rtf,csv,odt,ods,odp,jpg,jpeg,png|max:5120',
        ]);

        // Check if validation fails
        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(), // Get the error messages
            ], 422);
        }

        $uploadedFiles = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // Define the destination path
                $destinationPath = public_path('tmp_uploads');

                // Ensure the uploads directory exists
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }

                // Generate a unique file name
                $fileName = time() . '_' . str_replace(' ', '-', $file->getClientOriginalName());
                $originalFileNames = $file->getClientOriginalName();
                // Move the file to the destination folder
                $file->move($destinationPath, $fileName);

                $fileArr = ['originalName'=>$originalFileNames,'uploadedFile'=>$fileName];
                // Store the file path for later use
                $uploadedFiles[] = $fileArr; //url('tmp_uploads/' . $fileName);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Files uploaded successfully!',
                'files' => $uploadedFiles, // Return an array of file paths
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No files uploaded.',
        ], 400);
    }

    public function deleteFile(Request $request){

        $uploadedFile = $request->input('uploadedFile'); // Get the original name or filename

        $filePath = public_path('tmp_uploads/'. $uploadedFile); // Set the correct file path

        if (File::exists($filePath)) {
            // If file exists, delete it
            File::delete($filePath);

            return response()->json([
                'status' => 'success',
                'message' => 'File deleted successfully'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'File not found'
        ]);
    }

}






