<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ItrQuerie;
use Carbon\Carbon;
use App\Models\Coupon;
use App\Models\Documents;
use Illuminate\Support\Facades\File;

class ItrQueriesController extends Controller
{

    public function ItrQuerieStore(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
             'form_type' => 'required|string',
             'income_type' => 'required|array',
         ]);

         // Check if validation fails
         if($validator->fails()) {
             return response()->json([
                 'message' => 'Validation failed',
                 'errors' => $validator->errors(), // Get the error messages
             ], 422);
         }

         $data = $request->all();

        $amount = 0;
        $defaultOfferAmount = 0;
        $subtotal = 0;
        $gstCharge = 0;
        $getPlan = [];
        $coupon=null;
        $coupon_id = null;
        $defaultOffer_id = null;
        $lessAmount=0;
        $inputCoupon ='';

        $plan = getItrPlanAmount($data);
        $getPlan = $plan['summary'];
        $amount = $plan['amount'];

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

        $getDefaulOffer = Coupon::where(['form_type'=>'itr_queries','status'=>'active'])->where('expires_at', '>=', Carbon::now())->first();
        if($getDefaulOffer){
            $CalculateCoupon = CalculateCoupon($getDefaulOffer['code'],$amount);
            // dd($getDefaulOffer['code']);
            if(isset($CalculateCoupon['finalAmount']) && isset($CalculateCoupon['getCoupon'])){
                $defaultOfferAmount = $subtotal; // floor(($amount - $CalculateCoupon['finalAmount']) * 100) / 100;
                $subtotal = floor($CalculateCoupon['finalAmount'] * 100) / 100;
                // $coupon = $CalculateCoupon['getCoupon'];
                $defaultOffer_id= $CalculateCoupon['getCoupon']['id'];
            }
        }
        $gstCharge = ($subtotal * 18) / 100;
        $gstCharge = number_format((float)$gstCharge, 2, '.', '');
        $amount = $subtotal + $gstCharge;
        $amount = number_format((float)$amount, 2, '.', '');

        $r_value = roundOffAmount($amount);
        $roundOff = $r_value['difference'];
        $amount = $r_value['roundedValue'];

        $income_type = implode(',', $data['income_type']);

        $setData=[
            'user_id'=>$data['user_id'],
            'income_type' =>$income_type,
            'resident'=> $data['resident'],
            'business_income'=> $data['business_income'],
            'profit_loss'=> $data['profit_loss'],
            'income_tax_forms'=>$data['income_tax_forms'],
            'services'=>$data['services'],
            'coupon_id'=>$coupon_id,
            'amount'=>$amount,
            'default_discount'=>$defaultOffer_id
        ];
        // return $plan;
        if(isset($data['call_id']) && $data['call_id'] !='undefined' && $data['call_id'] > 0){
            $create = ItrQuerie::where('id', $data['call_id'])->first();
            $create->update($setData);
        }else{
            $create = ItrQuerie::create($setData);
        }

        Documents::where('query_id',$create->id)->where('form_type','itr_queries')->delete();
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
                        'form_type' => 'itr_queries',
                    ]);
                }

            }
        }
        return response()->json(['call_id'=>$create->id,'getPlan'=>$getPlan,'coupon'=>$coupon,'amount'=>number_format($amount,2),'lessAmount'=>number_format($lessAmount,2),'inputCoupon'=>$inputCoupon,'subtotal'=>number_format($subtotal,2),'gstCharge'=>number_format($gstCharge,2),'defaultOfferAmount'=>number_format($defaultOfferAmount,2),'roundOff' => $roundOff], 200);
         //dd($plan);
        //  getCallPlanAmount
    }
}
