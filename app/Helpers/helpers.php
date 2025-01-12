<?php
use App\Models\Coupon;
use Carbon\Carbon;

function getCallPlanAmount($value){
    $callPlan =[
        "1"=>['value'=>'499','label' => '10 minutes plan','url'=>'https://www.taxdunia.com/contact-us/'],
        "2"=>['value'=>'899','label' => '20 minutes plan','url'=>'https://www.taxdunia.com/contact-us/'],
        "3"=>['value'=>'1299','label' => '30 minutes plan','url'=>'https://www.taxdunia.com/contact-us/'],
    ];

    return $callPlan[$value];
}
function Call_query_type($arr){
    $Call_query_type = [
        ['value'=>'1','label' => 'Income Tax Returns','url'=>'https://www.taxdunia.com/contact-us/'],
        ['value'=>'2','label' => 'TDS Returns','url'=>'https://www.taxdunia.com/contact-us/'],
        ['value'=>'3','label' => 'GST Returns','url'=>'https://www.taxdunia.com/contact-us/'],
        ['value'=>'4','label' => 'Business Registration And Licenses','url'=>'https://www.taxdunia.com/contact-us/'],
        ['value'=>'5','label' => 'NRI Taxation','url'=>'https://www.taxdunia.com/contact-us/'],
        ['value'=>'6','label' => 'Consultancy Services','url'=>'https://www.taxdunia.com/contact-us/'],
        ['value'=>'7','label' => 'Other Query','url'=>'https://www.taxdunia.com/contact-us/']
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


function CalculateCoupon($code, $value) {
    // Retrieve the coupon based on the code
    $getCoupon = Coupon::where('code', $code)->where('status','active')->first();

    // Check if the coupon exists
    if (!$getCoupon) {
        return "Invalid Coupon.";
    }

    // Check if the coupon has expired
    if ($getCoupon->expires_at && Carbon::parse($getCoupon->expires_at)->isBefore(Carbon::now())) {
        return "This coupon has expired.";
    }

    // Get the type and value of the coupon
    $couponType = $getCoupon->type;  // 'flat' or 'percentage'
    $couponValue = $getCoupon->value;  // Coupon value (flat amount or percentage)

    // Check the type of coupon and calculate the discount accordingly
    if ($couponType == 'flat') {
        // Apply flat discount (subtract coupon value from the total amount)
        $finalAmount = $value - $couponValue;
    } elseif ($couponType == 'percentage') {
        // Apply percentage discount (calculate the percentage of the total value)
        $finalAmount = $value - ($value * ($couponValue / 100));
    } else {
        return "Invalid coupon type.";
    }

    // Ensure the final amount is not negative

    $finalAmount = max(0, $finalAmount);
    $finalAmount = floor($finalAmount * 100) / 100;
    // $finalAmount = number_format($finalAmount, 2, '.', '');
    return ["finalAmount"=>$finalAmount,"getCoupon"=>$getCoupon,'message'=>'Coupon applied successfully!'];
}

function getItrPlanAmount($data){
    $amount = 0;
    $summary = [];
    $planList = [
        '1'  => ['label'=>'Income form salary','url'=>'https://www.taxdunia.com/contact-us/'],
        '2'  => ['label'=>'Income from house property','url'=>'https://www.taxdunia.com/contact-us/'],
        '3'  => ['label'=>'Income from business and profession','url'=>'https://www.taxdunia.com/contact-us/'],
        '4'  => ['label'=>'Income from capital gains','url'=>'https://www.taxdunia.com/contact-us/'],
        '5'  => ['label'=>'Income from other sources','url'=>'https://www.taxdunia.com/contact-us/'],
        '6'  => ['label'=>'Income from crypto','url'=>'https://www.taxdunia.com/contact-us/'],
        '7'  => ['label'=>'Income form other sources','url'=>'https://www.taxdunia.com/contact-us/']
    ];

    $selectedKeys = $data['income_type']; // Get the selected keys
    $filteredSources = array_intersect_key($planList, array_flip($selectedKeys));

    $result = [];
    foreach ($filteredSources as $key => $value) {
        $result[$key] = $value; // Push the values into the result array
    }

    // Keys associated with the 999 plan
    $specialKeys = ['1', '2', '7'];

    // Check if any special plan keys are selected
    $hasSpecialKeys = !empty(array_intersect($selectedKeys, $specialKeys));

    // Check if any non-special keys are selected
    $hasOtherKeys = !empty(array_diff($selectedKeys, $specialKeys));

    // Determine the amount
    if ($hasSpecialKeys && !$hasOtherKeys) {
        $amount = 999;
    } else {
        $amount = 1499;
    }

    // dd( $result);
    $summary[] = ['plan'=>$result,'amount'=>$amount,'answer'=>'','type'=>'income_type'];

    if($data['resident'] == 2){
        $amount = $amount + 500;
        $summary[] = ['plan'=>'Are you a resident?','amount'=>500,'answer'=>'No'];
    }else{
        $summary[] = ['plan'=>'Are you a resident?','amount'=>0,'answer'=>'Yes'];
    }

    if($data['business_income'] == 1){
        $summary[] = ['plan'=>'Do you have business / stock market income?','amount'=>0,'answer'=>'Yes'];
    }else{
        $summary[] = ['plan'=>'Do you have business / stock market income?','amount'=>0,'answer'=>'No'];
    }

    if($data['profit_loss'] == 1){
        $amount = $amount + 1000;
        $summary[] = ['plan'=>'Do you want us tp prepare profit & loss and balance sheet?','amount'=>1000,'answer'=>'Yes'];
    }else if($data['profit_loss'] == 2){
        $summary[] = ['plan'=>'Do you want us tp prepare profit & loss and balance sheet?','amount'=>0,'answer'=>'No'];
    }

    if($data['income_tax_forms'] == 1){
        $amount = $amount + 500;
        $summary[] = ['plan'=>'Do you want us tp file any income tax forms?','amount'=>500,'answer'=>'Yes'];
    }else{
        $summary[] = ['plan'=>'Do you want us tp file any income tax forms?','amount'=>0,'answer'=>'No'];
    }

    if($data['services'] == 1){
        $summary[] = ['plan'=>'Do you need value added services?','amount'=>0,'answer'=>'Yes'];
    }else{
        $summary[] = ['plan'=>'Do you need value added services?','amount'=>0,'answer'=>'No'];
    }



    return [
        'amount' => $amount,
        'plan' => $result,
        'summary'=> $summary
    ];
}

function getGstPlanAmount($value){

    $taxpayer = $value['type_of_taxpayer'];

    if($taxpayer == 1){
       $fillingFrequency = $value['return_filling_frequency'];
       $serviceType = $value['service_type'];

       $value = $taxpayer.'_'.$fillingFrequency.'_'.$serviceType;

        $callPlan =[
            '1_1_1'  => ['value'=>'3499','label' => 'Regular Quarterly Prepare_only','url'=>'https://www.taxdunia.com/blogs/'],
            '1_1_2'  => ['value'=>'900','label' => 'Regular Quarterly File only','url'=>'https://www.taxdunia.com/blogs/'],
            '1_1_3'  => ['value'=>'1999','label' => 'Regular Quarterly Both Prepare and file','url'=>'https://www.taxdunia.com/blogs/'],

            '1_2_1'  => ['value'=>'1499','label' => 'Regular Monthly Prepare only','url'=>'https://www.taxdunia.com/blogs/'],
            '1_2_2'  => ['value'=>'2499','label' => 'Regular Monthly File only','url'=>'https://www.taxdunia.com/blogs/'],
            '1_2_3'  => ['value'=>'4999','label' => 'Regular Monthly Both Prepare and file','url'=>'https://www.taxdunia.com/blogs/'],

            '1_3_1'  => ['value'=>'11999','label' => 'Regular Annually Prepare only','url'=>'https://www.taxdunia.com/blogs/'],
            '1_3_2'  => ['value'=>'7999','label' => 'Regular Annually File only','url'=>'https://www.taxdunia.com/blogs/'],
            '1_3_3'  => ['value'=>'14999','label' => 'Regular Annually Both Prepare and file','url'=>'https://www.taxdunia.com/blogs/'],
        ];

        return $callPlan[$value];
    }else{
        $fillingFrequency = $value['plan_name'];
        $value = $taxpayer.'_'.$fillingFrequency;

        $callPlan =[
            '2_1'  => ['value'=>'1499','label' => 'composition Quarterly'],
            '2_2'  => ['value'=>'4999','label' => 'composition Anually'],
        ];
        return $callPlan[$value];

    }

}

function gst_query_type($arr){


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


function getBusinessrRegPlanAmount($value){
    $callPlan =[
        '1'  => ['value'=>'500','label' => 'PAN Registration','url'=>"https://www.taxdunia.com/contact-us/"],
        '2'  => ['value'=>'500','label' => 'TAN Registration','url'=>"https://claude.ai/chat/46cbb18e-8515-4f27-83f8-38eacad69526"],
        '3'  => ['value'=>'2000','label' => 'GST Registration','url'=>"https://www.taxdunia.com/contact-us/"],
        '4'  => ['value'=>'1000','label' => 'MSME Registration','url'=>"https://www.taxdunia.com/contact-us/"],
        '5'  => ['value'=>'5000','label' => 'SHOP ACT Registration','url'=>"https://www.taxdunia.com/contact-us/"],
        '6'  => ['value'=>'11000','label' => 'LLP Registration','url'=>"https://www.taxdunia.com/contact-us/"],
        '7'  => ['value'=>'15000','label' => 'PRIVATE LIMITED COMPANY Registration','url'=>"https://www.taxdunia.com/contact-us/"],
        '8'  => ['value'=>'20000','label' => 'PUBLIC LIMITED COMPANY Registration','url'=>"https://www.taxdunia.com/contact-us/"],
        '9'  => ['value'=>'20000','label' => 'SECTION 8 COMPANY Registration','url'=>"https://www.taxdunia.com/contact-us/"],
        '10' => ['value'=>'10000','label' => 'TRADEMARK Registration','url'=>"https://www.taxdunia.com/contact-us/"],
        '11' => ['value'=>'30000','label' => 'COPYRIGHT Registration','url'=>"https://www.taxdunia.com/contact-us/"],
        '12' => ['value'=>'15000','label' => 'OPC Registration','url'=>"https://www.taxdunia.com/contact-us/"],
        '13' => ['value'=>'10000','label' => 'ESI Registration','url'=>"https://www.taxdunia.com/contact-us/"],
        '14' => ['value'=>'10000','label' => 'PF Registration','url'=>"https://www.taxdunia.com/contact-us/"],
        '15' => ['value'=>'5000','label' => 'FIRM Registration','url'=>"https://www.taxdunia.com/contact-us/"],
        '16' => ['value'=>'20000','label' => 'Start up Registration','url'=>"https://www.taxdunia.com/contact-us/"]
    ];

    return $callPlan[$value];
}

function businessrReg_query_type($arr){
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


function getTSDPlanAmount($data){

    $typeOfReturn = $data['type_of_return'];
    // dd($typeOfReturn );
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

        $noOfEntries = $data['no_of_entries'];
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

function tds_query_type($arr){


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


?>
