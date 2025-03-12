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
use App\Models\Documents;
use Illuminate\Support\Facades\File;

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


        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
            'form_type' => 'required|string',
            'tan_number' => 'required|string',
         ]);

         // Check if validation fails
         if($validator->fails()) {
             return response()->json([
                 'message' => 'Validation failed',
                 'errors' => $validator->errors(), // Get the error messages
             ], 422);
         }


        $data = $request->all();

        $typeOfReturnArr=[
            '1' =>['label'=>'24Q','url'=>'1' ,'computation_url'=> '112121'],
            '2' => ['label'=>'26Q','url'=>'1','computation_url'=> '112121'],
            '3' =>['label'=>'27Q','url'=>'1','computation_url'=> '112121'] ,
            '4' => ['label'=>'26QB','url'=>'1','computation_url'=> '112121'],
        ];
        $typeOfReturn = $data['type_of_return'];
        if($typeOfReturn == 3){
            $data['no_of_entries'] = $data['no_of_entries_27'];
        }
        $computation =0;
        $getPlan = getTSDPlanAmount($data);
        // dd($getPlan);
        $amount =$getPlan['value'];
        $defaultOfferAmount = 0;
        $computation = isset($getPlan['computation']) ? $getPlan['computation'] :0;
        $amount += $computation;
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
                $returnTypeLabel = $typeOfReturnArr[$returnType]['label'];
                $getPlan['url']= $typeOfReturnArr[$returnType]['url'];
                $getPlan['computation_url']= $typeOfReturnArr[$returnType]['computation_url'];

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


                $r_value = roundOffAmount($amount);
                $roundOff = $r_value['difference'];
                $amount = $r_value['roundedValue'];


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

        if($request['no_of_employees'] ==  4 || $data['no_of_entries'] == 4){
            $formattedDate = $request['datetime'];
            if($request['datetime']){
                $formattedDate = date("Y-m-d H:i:s", strtotime($request['datetime']));
            }else{
                $formattedDate = null; //date("Y-m-d H:i:s", strtotime($request['datetime']));
            }


            $setData['call_when'] =  $request['selectTime'];
            $setData['call_datetime'] = $formattedDate; // $request['datetime'];
            $setData['language'] =  $request['language'];
        }


        if(isset($data['call_id']) && $data['call_id'] !='undefined' && $data['call_id'] > 0){
            $create = TdsQuerie::where('id', $data['call_id'])->first();
            $create->update($setData);

        }else{

            $setData['user_id'] =    $data['user_id'];
            $create = TdsQuerie::create($setData);
        }

        Documents::where('query_id',$create->id)->where('form_type','tds_queries')->delete();
        if($request->uploadedFile && count($request->uploadedFile) > 0){
            foreach ($request->uploadedFile as $file) {
                $filePath = public_path('tmp_uploads/'. $file); // Set the correct file path
                $newPath = public_path('uploads/' . $file);
                if (File::exists($filePath) || File::exists($newPath)) {
                    if(File::exists($filePath)){
                        File::move($filePath, $newPath);
                    }

                    Documents::create([
                        'query_id' => $create->id,
                        'file_url' => $file,
                        'form_type' => 'tds_queries',
                    ]);
                }

            }
        }


        $QueryTypeName =  ' Annually charge for '.$QueryTypeName.'- Number of employee '.$getPlan['label'];
        $computationQuery ='Computation & Tax Planning Service Fee for '.$getPlan['label'].' Employees';

        if($request['type_of_return'] != 1 && ($request['no_of_employees'] ==  4 || $data['no_of_entries'] == 4)){
            commonSendMeassage($create['user_id'],'tds_queries',$create['id']);
            // dd("ddd");
            // return redirect(config('app.CALL_BACK_URL'));
            $redirect_url = config('app.CALL_BACK_URL');
           return response()->json(['redirect_url'=>$redirect_url], 200);

        }else{
            return response()->json(['call_id'=>$create->id,'getPlan'=>$getPlan,'regarding'=>$QueryTypeName,'coupon'=>$coupon,'amount'=>number_format($amount,2),'lessAmount'=>number_format($lessAmount,2),'inputCoupon'=>$inputCoupon,'subtotal'=>number_format($subtotal,2),'gstCharge'=>number_format($gstCharge,2),'defaultOfferAmount'=>number_format($defaultOfferAmount,2),'return_type'=>$returnTypeLabel,'roundOff'=>$roundOff,'computation'=>$computation,'computationQuery'=>$computationQuery], 200);

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
