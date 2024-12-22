<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJob;
use App\Models\BusinessRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ScheduleCall;
use App\Models\Coupon;
use App\Models\EmailTemplate;
use App\Models\GstQuerie;
use App\Models\UserInquiry;
use App\Models\Transactions;
use App\Models\TalkToExpert;
use App\Models\TdsQuerie;
use App\Services\WhatsAppService;
use App\Services\OtpService;

class PayUMoneyController extends Controller
{
    protected $whatsAppService;
    protected $otpService;


    public function __construct(WhatsAppService $whatsAppService,OtpService $otpService)
    {
        $this->whatsAppService = $whatsAppService;
        $this->otpService = $otpService;
    }

    function formatNumber($number) {
        return number_format($number, 2, '.', '');
    }

    public function initiatePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'amount' => 'required|numeric',
            'id' => 'required',
            'form_type' => 'required',
            'user_id' => 'required',
        ]);
        // Check if validation fails
        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(), // Get the error messages
            ], 422);
        }



        $planDetails = '';
        $userDetails = UserInquiry::where('id',$request->user_id)->first();
        if($request->form_type == 'schedule_call'){
            $planDetails = ScheduleCall::where('id',$request->id)->first();

        }else if($request->form_type == 'talk_to_tax_expert'){
            $planDetails = TalkToExpert::where('id',$request->id)->first();
        }else if($request->form_type == 'business_registration'){
            $planDetails = BusinessRegistration::where('id',$request->id)->first();
        }else if($request->form_type == 'gst_queries'){
            $planDetails = GstQuerie::where('id',$request->id)->first();
        }else if($request->form_type == 'tds_queries'){
            $planDetails = TdsQuerie::where('id',$request->id)->first();
        }

        if($userDetails && $planDetails){


            $data = [
                'key' => env('PAYU_MERCHANT_KEY'),
                'txnid' =>  uniqid(),
                'amount' => '1.00',//$planDetails->total_amount,
                'productinfo' => 'API Product', // Example: Replace with actual product info
                'firstname' => $userDetails->name,
                'email' => $userDetails->email,
                'phone' => $userDetails->mobile,
                'surl' => route('payu.callback_success'), // Success URL
                'furl' => route('payu.callback_failed'), // Failure URL
            ];


        }else{
            return response()->json([
                'message' => 'User & Plane not Found',
            ], 500);
        }

        $data['hash'] = $this->generateHash($data);
        $setData = [
            "txnid"=>$data['txnid'],
            "hash"=>$data['hash'],
            "amount"=>$data['amount'],
            "form_type"=>$request->form_type,
            "order_id"=>$request->id,
            "user_id"=>$request->user_id
        ];

        Transactions::create($setData);

        // Return the payment data as a JSON response
        return response()->json([
            'url' => env('PAYU_URL'),
            'data' => $data,
        ]);
    }

    public function generateHash($data)
    {
        $hashString = $data['key'] . '|' . $data['txnid'] . '|' . $data['amount'] . '|' .
            $data['productinfo'] . '|' . $data['firstname'] . '|' . $data['email'] . '|||||||||||' . env('PAYU_MERCHANT_SALT');

        return strtolower(hash('sha512', $hashString));

    }



    public function handleCallbackSuccess(Request $request)
    {
        $postedHash = $request->hash;
        $status = $request->status;

        $generatedHash  = $this->generateHash($request);

        if ($status == 'success') {
            $updateData  =  Transactions::where('txnid',$request['txnid'])->first();
            $this->sendMeassage($updateData->user_id, $updateData->form_type);
            $updateData->update(["status"=>'completed']);
            return redirect(env('CALL_BACK_URL'));
        }

        return redirect(env('CALL_BACK_ERROR_URL'));
    }

    public function sendMeassage($userId,$formType){

        $userData = UserInquiry::where('id',$userId)->first();

        $template = EmailTemplate::whereIn('type',[1,2,3])->get();

        foreach ($template as $key => $value) {

            $message = str_replace("{client_name}","Sumit poonia",$value->description);

            if($value->type == 1){

            // Send OTP to the provided phone number
        
                $phone = '+91'.$userData->mobile;
                $this->otpService->sendOtp($phone, $message);

            }elseif($value->type == 2){

                $to = '+91'.$userData->mobile; // Recipient's WhatsApp number
                $message = $message; // The message content
        
                try {
                    $this->whatsAppService->sendMessage($to, $message);
                    // return response()->json(['status' => 'Message sent successfully!'], 200);
                } catch (\Exception $e) {
                    // return response()->json(['error' => $e->getMessage()], 500);
                }
                
            }elseif($value->type == 3){

                $data = [
                    'email' => $userData->email,
                    'title' => $value->subject,
                    'message' => $message,
                ];
                  // Dispatch the job
                SendEmailJob::dispatch($data);

            }
        }
        

        return '';
 
    }

    public function handleCallbackFailed(Request $request)
    {


        $postedHash = $request->hash;
        $status = $request->status;

        $generatedHash  = $this->generateHash($request);
        $updateData  =  Transactions::where('txnid',$request['txnid'])->first();
        $updateData->update(["status"=>'failed']);

        return redirect(env('CALL_BACK_ERROR_URL'));
    }




}
