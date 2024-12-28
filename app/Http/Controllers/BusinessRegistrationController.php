<?php

namespace App\Http\Controllers;

use App\Models\BusinessRegistration;
use Illuminate\Http\Request;



class BusinessRegistrationController extends Controller
{


    public function getCallPlanAmount($value){
        $callPlan =[
            '1'  => ['value'=>'500','label' => 'PAN Registration'],
            '2'  => ['value'=>'500','label' => 'TAN Registration'],
            '3'  => ['value'=>'2000','label' => 'GST Registration'],
            '4'  => ['value'=>'1000','label' => 'MSME Registration'],
            '5'  => ['value'=>'5000','label' => 'SHOP ACT Registration'],
            '6'  => ['value'=>'11000','label' => 'LLP Registration'],
            '7'  => ['value'=>'15000','label' => 'PRIVATE LIMITED COMPANY Registration'],
            '8'  => ['value'=>'20000','label' => 'PUBLIC LIMITED COMPANY Registration'],
            '9'  => ['value'=>'20000','label' => 'SECTION 8 COMPANY Registration'],
            '10' => ['value'=>'10000','label' => 'TRADEMARK Registration'],
            '11' => ['value'=>'30000','label' => 'COPYRIGHT Registration'],
            '12' => ['value'=>'15000','label' => 'OPC Registration'],
            '13' => ['value'=>'10000','label' => 'ESI Registration'],
            '14' => ['value'=>'10000','label' => 'PF Registration'],
            '15' => ['value'=>'5000','label' => 'FIRM Registration'],
            '16' => ['value'=>'20000','label' => 'Start up Registration']
        ];

        return $callPlan[$value];
    }
    public function Call_query_type($arr){
        $Call_query_type = [
            ['value'=>'1','label' => 'PAN Registration'],
            ['value'=>'2','label' => 'TAN Registration'],
            ['value'=>'3','label' => 'GST Registration'],
            ['value'=>'4','label' => 'MSME Registration'],
            ['value'=>'5','label' => 'SHOP ACT Registration'],
            ['value'=>'6','label' => 'LLP Registration'],
            ['value'=>'7','label' => 'PRIVATE LIMITED COMPANY Registration'],
            ['value'=>'8','label' => 'PUBLIC LIMITED COMPANY Registration'],
            ['value'=>'9','label' => 'SECTION 8 COMPANY Registration'],
            ['value'=>'10','label' => 'TRADEMARK Registration'],
            ['value'=>'11','label' => 'COPYRIGHT Registration'],
            ['value'=>'12','label' => 'OPC Registration'],
            ['value'=>'13','label' => 'ESI Registration'],
            ['value'=>'14','label' => 'PF Registration'],
            ['value'=>'15','label' => 'FIRM Registration'],
            ['value'=>'16','label' => 'Start up Registration']
        ];


        $labels = [];

        // Check if $values is an array
        if (is_array($arr)) {
            foreach ($arr as $value) {
                // Find the label for each value
                $found = false;
                foreach ($Call_query_type as $item) {
                    if ($item['value'] == $value) {
                        $labels[] = $item['label'];
                        $found = true;
                        break;
                    }
                }

                // If no label found, add a default message
                if (!$found) {
                    $labels[] = 'Unknown value';
                }
            }
        }

        return $labels;
    }


    public function businessStore(Request $request)
    {

        $data = $request->all();

        $plan = explode(",",$data['plan']);
        $amount = 0;
        $getPlan = [];
        foreach($plan as $value){

            $planData = $this->getCallPlanAmount($value);
            $getPlan[] = $planData;
            $amount += $planData['value'];
        }
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

        $QueryType = $data['plan'];
        $queryTypeArr = explode(",",$QueryType);


        $getQuery = $this->Call_query_type($queryTypeArr);


        $QueryType = implode(', ', $queryTypeArr);
        $QueryTypeName = implode(', ', $getQuery);

        // Handle file uploads
        $uploadedFiles = [];
        if ($request->hasFile('files')) {

            foreach ($request->file('files') as $file) {
                // Store the file and get the path
                $filePath = $file->store('business_documents', 'public');
                $uploadedFiles[] = $filePath;
            }
        }

        $setData = [
            'coupon_id'=>$coupon_id,
            'total_amount'=> (float)$amount,
            'plan' =>   $QueryType,
            'documents' =>  json_encode($uploadedFiles)
        ];

        if(isset($data['call_id']) && $data['call_id'] !='undefined' && $data['call_id'] > 0){
            $create = BusinessRegistration::where('id', $data['call_id'])->first();
            $create->update($setData);
        }else{
            $setData['user_id'] =    $request['user_id'];
            $create = BusinessRegistration::create($setData);
        }

        return response()->json(['call_id'=>$create->id,'getPlan'=>$getPlan,'regarding'=>$QueryTypeName,'coupon'=>$coupon,'amount'=>$amount,'lessAmount'=>$lessAmount,'inputCoupon'=>$inputCoupon], 200);

    }

    // public function handlePaySuccess(Request $request)
    // {
    //      // phone send otp start
    //      // Generate a random 6-digit OTP
    //     $otp = rand(100000, 999999);
    //     $phone = '+918890889144';

    //     //  // Send OTP to the provided phone number
    //      $this->otpService->sendOtp($phone, $otp);

    //      return response()->json(['message' => 'OTP sent successfully']);

    //     // phone send otp end


    //     //Send whatsapp message start

    //     $to = '+91'; // Recipient's WhatsApp number
    //     $message = 'Hello Sumit how are you'; // The message content

    //     try {
    //         $this->whatsAppService->sendMessage($to, $message);
    //         return response()->json(['status' => 'Message sent successfully!'], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }

    //     //Send whatsapp message end

    //     //Send email start


    //     $data = [
    //         'email' => $request['email'],
    //         'title' => 'Welcome to our App',
    //         'message' => 'Thank you for registering with us!',
    //     ];

    //     // // Dispatch the job
    //     SendEmailJob::dispatch($data);

    //     //Send email end

    // }


}
