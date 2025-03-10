<?php

namespace App\Http\Controllers;

use App\Models\BusinessRegistration;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Coupon;
use App\Models\Documents;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class BusinessRegistrationController extends Controller
{





    public function businessStore(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
             'form_type' => 'required|string',

         ]);

         // Check if validation fails
         if($validator->fails()) {
             return response()->json([
                 'message' => 'Validation failed',
                 'errors' => $validator->errors(), // Get the error messages
             ], 422);
         }

        $data = $request->all();

        $plan = explode(",",$data['plan']);
        $amount = 0;
        $defaultOfferAmount = 0;
        $subtotal = 0;
        $gstCharge = 0;
        $roundOff ='';
        $defaultOffer_id = null;
        $getPlan = [];
        foreach($plan as $value){

            $planData = getBusinessrRegPlanAmount($value);
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
        $subtotal = $amount;

        $getDefaulOffer = Coupon::where(['form_type'=>'business_registration','status'=>'active'])->where('expires_at', '>=', Carbon::now())->first();
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
        $amount = $r_value['roundedValue']; // number_format($r_value['roundedValue'], 2);

        $QueryType = $data['plan'];
        $queryTypeArr = explode(",",$QueryType);


        $getQuery = businessrReg_query_type($queryTypeArr);


        $QueryType = implode(', ', $queryTypeArr);
        $QueryTypeName = implode(', ', $getQuery);
        $destinationPath = public_path('business_documents');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        // Handle file uploads
        $uploadedFiles = [];
        if ($request->hasFile('files')) {

            foreach ($request->file('files') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move($destinationPath, $fileName);
                $uploadedFiles[] = asset('talk_to_TaxExpertFiles/' . $fileName);
            }
        }


        $setData = [
            'coupon_id'=>$coupon_id,
            'total_amount'=> (float)$amount,
            'plan' =>   $QueryType,
            'default_discount'=>$defaultOffer_id,
        ];

        if(isset($data['call_id']) && $data['call_id'] !='undefined' && $data['call_id'] > 0){
            $create = BusinessRegistration::where('id', $data['call_id'])->first();
            $create->update($setData);
        }else{
            $setData['user_id'] =    $request['user_id'];
            $create = BusinessRegistration::create($setData);
        }


        Documents::where('query_id',$create->id)->where('form_type','business_registration')->delete();
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
                        'form_type' => 'business_registration',
                    ]);
                }

            }
        }

        return response()->json(['call_id'=>$create->id,'getPlan'=>$getPlan,'regarding'=>$QueryTypeName,'coupon'=>$coupon,'amount'=>number_format($amount,2),'lessAmount'=>number_format($lessAmount,2),'inputCoupon'=>$inputCoupon,'subtotal'=>number_format($subtotal,2),'gstCharge'=>number_format($gstCharge,2),'defaultOfferAmount'=>number_format($defaultOfferAmount,2),"roundOff"=>$roundOff], 200);

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
