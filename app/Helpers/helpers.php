<?php
use App\Models\Coupon;
use Carbon\Carbon;
use App\Models\ScheduleCall;
use App\Jobs\SendEmailJob;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Models\TdsQuerie;
use App\Models\UserInquiry;
use Illuminate\Support\Str;



function numberToWords($number)
{
    $hyphen = '-';
    $conjunction = ' and ';
    $separator = ', ';
    $negative = 'negative ';
    $decimal = ' point ';
    $dictionary = [
        0 => 'zero',
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve',
        13 => 'thirteen',
        14 => 'fourteen',
        15 => 'fifteen',
        16 => 'sixteen',
        17 => 'seventeen',
        18 => 'eighteen',
        19 => 'nineteen',
        20 => 'twenty',
        30 => 'thirty',
        40 => 'forty',
        50 => 'fifty',
        60 => 'sixty',
        70 => 'seventy',
        80 => 'eighty',
        90 => 'ninety',
        100 => 'hundred',
        1000 => 'thousand',
        1000000 => 'million',
        1000000000 => 'billion',
        1000000000000 => 'trillion',
        1000000000000000 => 'quadrillion',
        1000000000000000000 => 'quintillion'
    ];

    if (!is_numeric($number)) {
        return false;
    }

    if ($number < 0) {
        return $negative . numberToWords(abs($number));
    }

    $string = $fraction = null;

    if (strpos((string)$number, '.') !== false) {
        [$number, $fraction] = explode('.', (string)$number, 2);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens = ((int) ($number / 10)) * 10;
            $units = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[(int) $hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . numberToWords($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = numberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= numberToWords($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        foreach (str_split((string)$fraction) as $digit) {
            $string .= $dictionary[$digit] . ' ';
        }
    }

    $string = ucwords(strtolower($string));
    return $string;
}



function sumAmountOfPlan(array $plans, float $taxRate = 9): array
{
    // Calculate the sum of the amounts
    $totalAmount = array_reduce($plans, function ($carry, $plan) {
        return $carry + (float) $plan['amount'];
    }, 0);

    // Calculate CGST and SGST
    $cgst = ($totalAmount * $taxRate) / 100;
    $sgst = ($totalAmount * $taxRate) / 100;

    // Return the results as an array
    return [
        'totalAmount' => $totalAmount,
        'cgst' => $cgst,
        'sgst' => $sgst,
        'totalTax' => $cgst + $sgst
    ];
}
function calculateTaxes($totalAmount,float $taxRate = 9){
    $cgst = ($totalAmount * $taxRate) / 100;
    $sgst = ($totalAmount * $taxRate) / 100;
    return [
        'totalAmount' => $totalAmount + $cgst + $sgst,
        'cgst' => $cgst,
        'sgst' => $sgst,
        'totalTax' => $cgst + $sgst
    ];
}


function generateInvoiceId()
{
    $currentMonth = now()->month;

    // Determine the fiscal year
    if ($currentMonth >= 4) {
        $startYear = now()->year;
        $endYear = now()->year + 1;
    } else {
        $startYear = now()->year - 1;
        $endYear = now()->year;
    }

    $fiscalYear = substr($startYear,2) . '-' . substr($endYear, 2); // e.g., "2024-25"

    // Create the invoice ID with the fiscal year
    $invoiceId = 'TD/' . $fiscalYear . '/';// . Str::random(6); // Use Str::random() instead of str_random()

    return $invoiceId;
}

function commonSendMeassage($userId,$formType,$id, $filePaths = []){

    $userData = UserInquiry::where('id',$userId)->first();

    $template = EmailTemplate::whereIn('type',[1,2,3])->where('form_type',$formType)->get();
    if($formType == 'tds_queries'){
        $template = EmailTemplate::whereIn('type',[1,2,3])->where('form_type','schedule_call')->get();
    }
    // $message = str_replace("{client_name}",$userData->name,$value->description);
    // dd($template[0]['description']);

    foreach ($template as $key => $value) {

        $message = str_replace("{client_name}",$userData->name,$value->description);
        $message = str_replace("{mobile_number}",$userData->mobile,$message);
        if($formType == 'schedule_call'){
            $getCall = ScheduleCall::where('id',$id)->first();
            if($getCall && $getCall['call_when'] == 2){
                $formattedDate = date("Y-m-d h:i A", strtotime($getCall['call_datetime']));
                $message = str_replace("{date_time}",$formattedDate,$message);
            }else{
                $message = str_replace("{date_time}",'We will call you within the next hour.',$message);
            }

        }

        if($formType == 'tds_queries'){
            $getCall = TdsQuerie::where('id',$id)->first();
            if($getCall && $getCall['call_when'] == 2){
                $message = str_replace("{date_time}",$getCall['call_datetime'],$message);
            }else{
                $message = str_replace("{date_time}",'We will call you within the next hour.',$message);
            }
        }

        // dd($message);
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

        if($value->type == 3){

            $data = [
                'email' => $userData->email,
                'title' => $value->subject,
                'message' => $message,
                'filePaths'=>$filePaths,
            ];
              // Dispatch the job
            SendEmailJob::dispatch($data);

        }
    }

    // dd('done');
    return 1;

}


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


function CalculateCoupon($code, $value,$id = 0) {
    // Retrieve the coupon based on the code
    $getCoupon;
    if($id > 0){
        $getCoupon = Coupon::where('id', $id)->where('status','active')->first();
    }else{
        $getCoupon = Coupon::where('code', $code)->where('status','active')->first();
    }


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
        $summary[] = ['plan'=>'Do you want us to prepare profit & loss and balance sheet?','amount'=>1000,'answer'=>'Yes'];
    }else if($data['profit_loss'] == 2){
        $summary[] = ['plan'=>'Do you want us to prepare profit & loss and balance sheet?','amount'=>0,'answer'=>'No'];
    }

    if($data['income_tax_forms'] == 1){
        $amount = $amount + 500;
        $summary[] = ['plan'=>'Do you want us to file any income tax forms?','amount'=>500,'answer'=>'Yes'];
    }else{
        $summary[] = ['plan'=>'Do you want us to file any income tax forms?','amount'=>0,'answer'=>'No'];
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
            '1_1_1'  => ['value'=>'2499','label' => 'Regular Quarterly Prepare_only','url'=>'https://www.taxdunia.com/blogs/'],
            '1_1_2'  => ['value'=>'3499','label' => 'Regular Quarterly File only','url'=>'https://www.taxdunia.com/blogs/'],
            '1_1_3'  => ['value'=>'4999','label' => 'Regular Quarterly Both Prepare and file','url'=>'https://www.taxdunia.com/blogs/'],

            '1_2_1'  => ['value'=>'999','label' => 'Regular Monthly Prepare only','url'=>'https://www.taxdunia.com/blogs/'],
            '1_2_2'  => ['value'=>'1499','label' => 'Regular Monthly File only','url'=>'https://www.taxdunia.com/blogs/'],
            '1_2_3'  => ['value'=>'1999','label' => 'Regular Monthly Both Prepare and file','url'=>'https://www.taxdunia.com/blogs/'],

            '1_3_1'  => ['value'=>'7999','label' => 'Regular Annually Prepare only','url'=>'https://www.taxdunia.com/blogs/'],
            '1_3_2'  => ['value'=>'11999','label' => 'Regular Annually File only','url'=>'https://www.taxdunia.com/blogs/'],
            '1_3_3'  => ['value'=>'14999','label' => 'Regular Annually Both Prepare and file','url'=>'https://www.taxdunia.com/blogs/'],
        ];

        return $callPlan[$value];
    }else{
        $fillingFrequency = $value['plan_name'];
        $value = $taxpayer.'_'.$fillingFrequency;

        $callPlan =[
            '2_1'  => ['value'=>'1499','label' => 'composition Quarterly','url'=>'https://www.taxdunia.com/blogs/'],
            '2_2'  => ['value'=>'4999','label' => 'composition Anually','url'=>'https://www.taxdunia.com/blogs/'],
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
            '2'  => ['value'=>'9999','label' => '10 to 50'],
            '3'  => ['value'=>'16999','label' => '50 to 100'],
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
