<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SendEmailJob;
use App\Models\GstQuerie;


class GstQuerieController extends Controller
{


    public function getCallPlanAmount($value){

        $taxpayer = $value['type_of_taxpayer'];

        if($taxpayer == 1){
           $fillingFrequency = $value['return_filling_frequency'];
           $serviceType = $value['service_type'];

           $value = $taxpayer.'_'.$fillingFrequency.'_'.$serviceType;

            $callPlan =[
                '1_1_1'  => ['value'=>'3499','label' => 'regular_Quarterly_Prepare_only'],
                '1_1_2'  => ['value'=>'900','label' => 'regular_Quarterly_File_only'],
                '1_1_3'  => ['value'=>'1999','label' => 'regular_Quarterly_Both_Prepare_and _file'],
                '1_2_1'  => ['value'=>'1499','label' => 'regular_Monthly_Prepare_only'],
                '1_2_2'  => ['value'=>'2499','label' => 'regular_Monthly_File_only'],
                '1_2_3'  => ['value'=>'4999','label' => 'regular_Monthly_Both_Prepare_and _file'],
            ];
            return $callPlan[$value];
        }else{
            $fillingFrequency = $value['plan_name'];
            $value = $taxpayer.'_'.$fillingFrequency;

            $callPlan =[
                '2_1'  => ['value'=>'1499','label' => 'composition_Quarterly'],
                '2_2'  => ['value'=>'4999','label' => 'composition_Monthly'],
            ];
            return $callPlan[$value];

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

    public function gstQuerieStore(Request $request)
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
        if( $data['type_of_taxpayer'] == 1){

            $QueryType = $data['plan'];
            $queryTypeArr = explode(",",$QueryType);
            $getQuery = $this->Call_query_type($queryTypeArr);
            $QueryType = implode(', ', $queryTypeArr);
            $QueryTypeName = implode(', ', $getQuery);
        }else{
            if($request['plan_name'] == 1){
                $QueryType = null;
                $QueryTypeName = 'Quarterly';

            }else{
                $QueryType = null;
                $QueryTypeName = 'Monthly';

            }
        }

        $setData = [
            'gst_number' => $request['gst_number'],
            'type_of_taxpayer' => $request['type_of_taxpayer'],
            'return_filling_frequency' => $data['type_of_taxpayer'] == 1 ? $request['return_filling_frequency'] : $request['plan_name'],
            'type_of_return' => $QueryType,
            'service_type' =>  $data['type_of_taxpayer'] == 1?$request['service_type']:null,
            'coupon_id'=>$coupon_id,
            'total_amount'=> (float)$amount, 
        ];

        if(isset($data['call_id']) && $data['call_id'] !='undefined' && $data['call_id'] > 0){
            $create = GstQuerie::where('id', $data['call_id'])->first();
            $create->update($setData);

        }else{
           
            $setData['user_id'] =    $data['user_id'];
            $create = GstQuerie::create($setData);
        }

        return response()->json(['call_id'=>$create->id,'getPlan'=>$getPlan,'regarding'=>$QueryTypeName,'coupon'=>$coupon,'amount'=>$amount,'lessAmount'=>$lessAmount,'inputCoupon'=>$inputCoupon], 200);
       
    }

}
