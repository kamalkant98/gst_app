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
use App\Models\ItrQuerie;
use Carbon\Carbon;


class PayUMoneyController extends Controller
{
    protected $whatsAppService;
    protected $otpService;


    // public function __construct(WhatsAppService $whatsAppService,OtpService $otpService)
    // {
    //     $this->whatsAppService = $whatsAppService;
    //     $this->otpService = $otpService;
    // }

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
        $amount = 0;
        $userDetails = UserInquiry::where('id',$request->user_id)->first();
        if($request->form_type == 'schedule_call'){
            $planDetails = ScheduleCall::where('id',$request->id)->first();
            $amount = $planDetails->total_amount;
        }else if($request->form_type == 'talk_to_tax_expert'){
            $planDetails = TalkToExpert::where('id',$request->id)->first();
            $amount = $planDetails->total_amount;
        }else if($request->form_type == 'business_registration'){
            $planDetails = BusinessRegistration::where('id',$request->id)->first();
            $amount = $planDetails->total_amount;
        }else if($request->form_type == 'gst_queries'){
            $planDetails = GstQuerie::where('id',$request->id)->first();
            $amount = $planDetails->total_amount;
        }else if($request->form_type == 'tds_queries'){
            $planDetails = TdsQuerie::where('id',$request->id)->first();
            $amount = $planDetails->total_amount;
        }else if($request->form_type == 'itr_queries'){
            $planDetails = ItrQuerie::where('id',$request->id)->first();
            $amount = $planDetails->amount;
        }

        if($userDetails && $planDetails){


            $data = [
                'key' => env('PAYU_MERCHANT_KEY'),
                'txnid' =>  uniqid(),
                'amount' => $amount,
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
            $updateData->update(["status"=>'completed']);
            $setData = $updateData;
            if($updateData->form_type == 'talk_to_tax_expert'){
                $getCall = TalkToExpert::where('id', $updateData['order_id'])->first();
                $queryTypeArr[] = $getCall->query_type;
                $getQuery = Call_query_type($queryTypeArr);
                $QueryTypeName = implode(', ', $getQuery);
                $getPlan = getCallPlanAmount($getCall->plan);
                $duration = '10 mins';
                $date = $getCall->call_datetime;

                if($getCall->plan == 2){
                    $duration = '20 mins';
                }else if($getCall->plan == 3){
                    $duration = '30 mins';
                }
                $setData['other_details'] = [
                    'service_name' => $QueryTypeName,
                    'duration' => $duration,
                    'amount' => $updateData['amount'],
                    'date' => $date,
                    'invoice_url' => 'https://www.stackhawk.com/blog/laravel-cors/'
                ];
            }else if($updateData->form_type == 'itr_queries'){
                $getplanDetails = ItrQuerie::where('id',$updateData['order_id'])->first();
                $getplanDetails->income_type =  explode(',',$getplanDetails->income_type);
                $data = getItrPlanAmount($getplanDetails);
                $labels = []; // Array to store labels

                foreach ($data['plan'] as $item) {
                    if (isset($item['label'])) {
                        $labels[] = $item['label']; // Add label to the labels array
                    }
                }
                $commaSeparatedLabels = implode(", ", $labels);

                $setData['other_details'] = [
                    'service_name' => $commaSeparatedLabels,
                    'amount' => $updateData['amount'],
                    'invoice_url' => 'https://www.stackhawk.com/blog/laravel-cors/'
                ];


            }else if ($updateData->form_type == 'gst_queries'){
                $getplanDetails = GstQuerie::where('id',$updateData['order_id'])->first();
                $service_name = getGstPlanAmount($getplanDetails);
                $setData['other_details'] = [
                    'service_name' => $service_name['label'],
                    'amount' => $updateData['amount'],
                    'invoice_url' => 'https://www.stackhawk.com/blog/laravel-cors/'
                ];
            }else if($updateData->form_type == 'business_registration'){
                $getplanDetails = BusinessRegistration::where('id',$updateData['order_id'])->first();

                $QueryType = $getplanDetails['plan'];
                $queryTypeArr = explode(",",$QueryType);
                $getQuery = businessrReg_query_type($queryTypeArr);
                $QueryTypeName = implode(', ', $getQuery);
                $setData['other_details'] = [
                    'service_name' => $QueryTypeName,
                    'amount' => $updateData['amount'],
                    'invoice_url' => 'https://www.stackhawk.com/blog/laravel-cors/'
                ];
            }else if ($updateData->form_type == 'tds_queries'){
                $getplanDetails = TdsQuerie::where('id',$updateData['order_id'])->first();

                $typeOfReturnArr=[
                    '1' =>['label'=>'24Q','url'=>'1'],
                    '2' => ['label'=>'26Q','url'=>'1'],
                    '3' =>['label'=>'27Q','url'=>'1'] ,
                    '4' => ['label'=>'26QB','url'=>'1'],
                ];
                $QueryTypeName = $typeOfReturnArr[$getplanDetails->type_of_return]['label'];
                $getPlan = getTSDPlanAmount($getplanDetails);
                $QueryTypeName =  $QueryTypeName.'- Number of employee '.$getPlan['label'];

                $setData['other_details'] = [
                    'service_name' => $QueryTypeName,
                    'amount' => $updateData['amount'],
                    'invoice_url' => 'https://www.stackhawk.com/blog/laravel-cors/'
                ];
                // dd($QueryTypeName);
            }

            $this->sendMeassage($setData, $updateData->form_type);

            return redirect(env('CALL_BACK_URL'));
        }

        return redirect(env('CALL_BACK_ERROR_URL'));
    }

    public function sendMeassage($updateData,$formType){

        $userData = UserInquiry::where('id',$updateData->user_id)->first();

        $template = EmailTemplate::whereIn('type',[1,2,3])->where('form_type',$formType)->get();
        // dd($updateData);
        foreach ($template as $key => $value) {

            $message = str_replace("{client_name}",$userData->name,$value->description);
            $message = str_replace("{amount}",$updateData['other_details']['amount'],$message);
            $message = str_replace("{service_name}",$updateData['other_details']['service_name'],$message);
            $message = str_replace("{R_No}",$updateData['txnid'],$message);
            $message = str_replace("{invoice_url}",$updateData['other_details']['invoice_url'],$message);

            if($formType == 'talk_to_tax_expert'){
                $message = str_replace("{mobile_number}",$userData['mobile'],$message);
                $message = str_replace("{date_time}",$updateData['other_details']['date'],$message);
                $message = str_replace("{duration}",$updateData['other_details']['duration'],$message);
            }


            // if($value->type == 1){

            // // Send OTP to the provided phone number

            //     $phone = '+91'.$userData->mobile;
            //     $this->otpService->sendOtp($phone, $message);

            // }elseif($value->type == 2){

            //     $to = '+91'.$userData->mobile; // Recipient's WhatsApp number
            //     $message = $message; // The message content

            //     try {
            //         $this->whatsAppService->sendMessage($to, $message);
            //         // return response()->json(['status' => 'Message sent successfully!'], 200);
            //     } catch (\Exception $e) {
            //         // return response()->json(['error' => $e->getMessage()], 500);
            //     }

            // }else
            // dd($message);

            if($value->type == 3){

                $data = [
                    'email' => $userData->email,
                    'title' => $value->subject,
                    'message' => $message,
                ];
                  // Dispatch the job
                SendEmailJob::dispatch($data);

            }
        }


        return 1;

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
