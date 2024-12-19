<?php
use App\Models\Coupon;
use Carbon\Carbon;

function getCallPlanAmount($value){
    $callPlan =[
        "1"=>['value'=>'499','label' => '10 minutes plan'],
        "2"=>['value'=>'899','label' => '20 minutes plan'],
        "3"=>['value'=>'1299','label' => '30 minutes plan'],
    ];

    return $callPlan[$value];
}
function Call_query_type($arr){
    $Call_query_type = [
        ['value'=>'1','label' => 'Income Tax Returns'],
        ['value'=>'2','label' => 'TDS Returns'],
        ['value'=>'3','label' => 'GST Returns'],
        ['value'=>'4','label' => 'Business Registration And Licenses'],
        ['value'=>'5','label' => 'NRI Taxation'],
        ['value'=>'6','label' => 'Consultancy Services'],
        ['value'=>'7','label' => 'Other Query']
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
?>
