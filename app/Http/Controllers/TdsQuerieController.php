<?php

namespace App\Http\Controllers;

use App\Models\BusinessRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendEmailJob;
use App\Models\TdsQuerie;
use App\Services\WhatsAppService;
use App\Services\OtpService;

class TdsQuerieController extends Controller
{

    protected $whatsAppService;
    protected $otpService;


    public function __construct(WhatsAppService $whatsAppService,OtpService $otpService)
    {
        $this->whatsAppService = $whatsAppService;
        $this->otpService = $otpService;
    }

    public function getCallPlanAmount($data){

        $typeOfReturn = $data['type_of_return'];

        if($typeOfReturn == 1){
           $noOfEmployees = $data['no_of_employees'];
           $value = $noOfEmployees;

            $callPlan =[
                '1'  => ['value'=>'4000','label' => '1 to 10'],
                '2'  => ['value'=>'15000','label' => '10 to 50'],
                '3'  => ['value'=>'25000','label' => '50 to 100'],
                '4'  => ['value'=>'0','label' => 'More than 100'],
            ];
            return $callPlan[$value];
        }elseif($typeOfReturn == 2){

            $noOfEntries = $data['no_of_entries'];
            $value = $noOfEntries;
 
             $callPlan =[
                 '1'  => ['value'=>'4000','label' => 'Up to 100'],
                 '2'  => ['value'=>'10000','label' => '100 to 250'],
                 '3'  => ['value'=>'15000','label' => '250 to 500'],
                 '4'  => ['value'=>'0','label' => 'More than 500'],
             ];
             return $callPlan[$value];

        }elseif($typeOfReturn == 3){

            $noOfEntries = $data['no_of_entries_27'];
            $value = $noOfEntries;
 
             $callPlan =[
                 '1'  => ['value'=>'4000','label' => 'Up to 50'],
                 '2'  => ['value'=>'10000','label' => '50 to 100'],
                 '3'  => ['value'=>'15000','label' => '100 to 200'],
                 '4'  => ['value'=>'0','label' => 'More than 200'],
             ];
             return $callPlan[$value];


        }else{
            
            return ['value'=>'3000','label' => 'Annual Fee'];

        }
    
    }
    public function Call_query_type($arr){


        $Call_query_type = [
            ['value'=>'1','label' => 'GSTR 1'],
            ['value'=>'2','label' => 'GSTR 3B'],
            ['value'=>'3','label' => '>GSTR 9/9C'],
            ['value'=>'4','label' => 'GSTR 8'],
            ['value'=>'5','label' => 'TCS Return'],
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

    public function tdsQuerieStore(Request $request)
    {   

        $data = $request->all();
        
        $getPlan = $this->getCallPlanAmount($data);
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
        
                $QueryType = null;
                $QueryTypeName = 'Quarterly';


        $setData = [
            'tan_number' => $request['tan_number'],
            'no_of_employees' => $request['no_of_employees'],
            'no_of_entries' => $request['no_of_entries'],
            'tax_planning_of_employees' => $request['tax_planning'],
            'coupon_id'=>$coupon_id,
            'total_amount'=> (float)$amount, 
        ];

        if(isset($data['call_id']) && $data['call_id'] !='undefined' && $data['call_id'] > 0){
            $create = TdsQuerie::where('id', $data['call_id'])->first();
            $create->update($setData);

        }else{
           
            $setData['user_id'] =    $data['user_id'];
            $create = TdsQuerie::create($setData);
        }

        return response()->json(['call_id'=>$create->id,'getPlan'=>$getPlan,'regarding'=>$QueryTypeName,'coupon'=>$coupon,'amount'=>$amount,'lessAmount'=>$lessAmount,'inputCoupon'=>$inputCoupon], 200);
       
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
