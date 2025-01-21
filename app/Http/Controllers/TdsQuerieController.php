<?php

namespace App\Http\Controllers;

use App\Models\BusinessRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendEmailJob;
use App\Models\TdsQuerie;
use App\Services\WhatsAppService;
use App\Services\OtpService;
use Carbon\Carbon;
use App\Models\Coupon;

class TdsQuerieController extends Controller
{

    protected $whatsAppService;
    protected $otpService;


    public function __construct(WhatsAppService $whatsAppService,OtpService $otpService)
    {
        $this->whatsAppService = $whatsAppService;
        $this->otpService = $otpService;
    }


    public function tdsQuerieStore(Request $request)
    {

        $data = $request->all();

        $typeOfReturnArr=[
            '1' =>['label'=>'24Q','url'=>'1'],
            '2' => ['label'=>'26Q','url'=>'1'],
            '3' =>['label'=>'27Q','url'=>'1'] ,
            '4' => ['label'=>'26QB','url'=>'1'],
        ];
        $typeOfReturn = $data['type_of_return'];
        if($typeOfReturn == 3){
            $data['no_of_entries'] = $data['no_of_entries_27'];
        }

        $getPlan = getTSDPlanAmount($data);
        $amount =$getPlan['value'];
        $defaultOfferAmount = 0;
        $coupon=null;
        $coupon_id = null;
        $defaultOffer_id= null;
        $lessAmount=0;
        $inputCoupon ='';
        $subtotal = 0;
        $gstCharge = 0;
        // dd($getPlan);
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
                $returnType = $data['type_of_return'];

                $QueryTypeName = $typeOfReturnArr[$returnType]['label'];
                $getPlan['url']= $typeOfReturnArr[$returnType]['url'];

                $subtotal = $amount;

                $getDefaulOffer = Coupon::where(['form_type'=>'tds_queries','status'=>'active'])->where('expires_at', '>=', Carbon::now())->first();
                if($getDefaulOffer){
                    $CalculateCoupon = CalculateCoupon($getDefaulOffer['code'],$amount);
                    // dd($getDefaulOffer['code']);
                    if(isset($CalculateCoupon['finalAmount']) && isset($CalculateCoupon['getCoupon'])){
                        $defaultOfferAmount = $subtotal; // floor(($amount - $CalculateCoupon['finalAmount']) * 100) / 100;
                        $subtotal = floor($CalculateCoupon['finalAmount'] * 100) / 100;
                        // $coupon = $CalculateCoupon['getCoupon'];
                        $defaultOffer_id= $CalculateCoupon['getCoupon']['id'];
                    }else{
                        $coupon = $CalculateCoupon;
                    }
                }
                $gstCharge = ($subtotal * 18) / 100;
                $gstCharge = number_format((float)$gstCharge, 2, '.', '');
                $amount = $subtotal + $gstCharge;
                $amount = number_format((float)$amount, 2, '.', '');



        $setData = [
            'tan_number' => $request['tan_number'],
            'no_of_employees' => $request['no_of_employees'],
            'no_of_entries' => $data['no_of_entries'],
            'tax_planning_of_employees' => $request['tax_planning'],
            'type_of_return' => $request['type_of_return'],
            'coupon_id'=>$coupon_id,
            'total_amount'=> (float)$amount,
            'default_discount'=>$defaultOffer_id,
        ];

        if($request['no_of_employees'] ==  4){
            $setData['call_when'] =  $request['selectTime'];
            $setData['call_datetime'] =  $request['datetime'];
            $setData['language'] =  $request['language'];
        }


        if(isset($data['call_id']) && $data['call_id'] !='undefined' && $data['call_id'] > 0){
            $create = TdsQuerie::where('id', $data['call_id'])->first();
            $create->update($setData);

        }else{

            $setData['user_id'] =    $data['user_id'];
            $create = TdsQuerie::create($setData);
        }

        $QueryTypeName =  $QueryTypeName.'- Number of employee '.$getPlan['label'];

        if($request['no_of_employees'] ==  4){
            commonSendMeassage($create['user_id'],'tds_queries',$create['id']);
            // dd("ddd");
            // return redirect(env('CALL_BACK_URL'));
            $redirect_url = env('CALL_BACK_URL');
            return response()->json(['redirect_url'=>$redirect_url], 200);

        }else{
            return response()->json(['call_id'=>$create->id,'getPlan'=>$getPlan,'regarding'=>$QueryTypeName,'coupon'=>$coupon,'amount'=>$amount,'lessAmount'=>$lessAmount,'inputCoupon'=>$inputCoupon,'subtotal'=>$subtotal,'gstCharge'=>$gstCharge,'defaultOfferAmount'=>$defaultOfferAmount], 200);

        }

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
