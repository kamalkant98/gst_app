<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SendEmailJob;
use App\Models\GstQuerie;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Coupon;
use App\Models\Documents;
use Illuminate\Support\Facades\File;

class GstQuerieController extends Controller
{






    public function gstQuerieStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
             'form_type' => 'required|string',
             'gst_number' => 'required|string',
         ]);

         // Check if validation fails
         if($validator->fails()) {
             return response()->json([
                 'message' => 'Validation failed',
                 'errors' => $validator->errors(), // Get the error messages
             ], 422);
         }

        $data = $request->all();

        $getPlan = getGstPlanAmount($data);
        $amount =$getPlan['value'];
        $defaultOfferAmount = 0;
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
        if( $data['type_of_taxpayer'] == 1){

            $QueryType = $data['plan'];
            $queryTypeArr = explode(",",$QueryType);
            $getQuery = gst_query_type($queryTypeArr);
            $QueryType = implode(', ', $queryTypeArr);
            $QueryTypeName = implode(', ', $getQuery);
        }else{
            if($request['plan_name'] == 1){
                $QueryType = null;
                $QueryTypeName = 'Quarterly ssas';

            }else{
                $QueryType = null;
                $QueryTypeName = 'Monthly asd';

            }
        }

        $QueryTypeName = $getPlan['label'];

        $subtotal = $amount;

        $getDefaulOffer = Coupon::where(['form_type'=>'gst_queries','status'=>'active'])->where('expires_at', '>=', Carbon::now())->first();
        if($getDefaulOffer){
            $CalculateCoupon = CalculateCoupon($getDefaulOffer['code'],$amount);
            // dd($getDefaulOffer['code']);
            if(isset($CalculateCoupon['finalAmount']) && isset($CalculateCoupon['getCoupon'])){
                $defaultOfferAmount = $subtotal; // floor(($amount - $CalculateCoupon['finalAmount']) * 100) / 100;
                $subtotal = floor($CalculateCoupon['finalAmount'] * 100) / 100;
                // $coupon = $CalculateCoupon['getCoupon'];
                $defaultOffer_id = $CalculateCoupon['getCoupon']['id'];
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
            'gst_number' => $request['gst_number'],
            'type_of_taxpayer' => $request['type_of_taxpayer'],
            'return_filling_frequency' => $data['type_of_taxpayer'] == 1 ? $request['return_filling_frequency'] : $request['plan_name'],
            'type_of_return' => $QueryType,
            'service_type' =>  $data['type_of_taxpayer'] == 1?$request['service_type']:null,
            'coupon_id'=>$coupon_id,
            'total_amount'=> (float)$amount,
            'default_discount'=>$defaultOffer_id,
        ];

        if(isset($data['call_id']) && $data['call_id'] !='undefined' && $data['call_id'] > 0){
            $create = GstQuerie::where('id', $data['call_id'])->first();
            $create->update($setData);

        }else{

            $setData['user_id'] =$data['user_id'];
            $create = GstQuerie::create($setData);
        }


        Documents::where('query_id',$create->id)->where('form_type','gst_queries')->delete();
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
                        'form_type' => 'gst_queries',
                    ]);
                }

            }
        }

        return response()->json(['call_id'=>$create->id,'getPlan'=>$getPlan,'regarding'=>$QueryTypeName,'coupon'=>$coupon,'amount'=>number_format($amount,2),'lessAmount'=>number_format($lessAmount,2),'inputCoupon'=>$inputCoupon,'subtotal'=>number_format($subtotal,2),'gstCharge'=>number_format($gstCharge,2),'defaultOfferAmount'=>number_format($defaultOfferAmount,2),'roundOff'=>$roundOff], 200);

    }

}
