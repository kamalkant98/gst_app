<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJob;
use App\Models\BusinessRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ScheduleCall;
use App\Models\Coupon;
use App\Models\EmailTemplate;
use App\Models\GstQuerie;
use App\Models\UserInquiry;
use App\Models\Transactions;
use App\Models\TalkToExpert;
use App\Models\TdsQuerie;
use App\Services\WhatsAppService;
use App\Services\OtpService;
use App\Models\ItrQuerie;
use Carbon\Carbon;
use App\Models\Documents;
// use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PayUMoneyController extends Controller
{
    protected $whatsAppService;
    protected $otpService;


    // public function __construct(WhatsAppService $whatsAppService,OtpService $otpService)
    // {
    //     $this->whatsAppService = $whatsAppService;
    //     $this->otpService = $otpService;
    // }

    function generatePdf($data){
        $userData = UserInquiry::where('id',$data['user_id'])->first();
        $formattedDate = Carbon::now()->format('j-M-y');
        $data['date'] = $formattedDate;
        $data['buyer_name'] = $userData['name'];
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);
        $pdfContent = view('invoice_template',['data' => $data])->render();
        $dompdf->loadHtml($pdfContent);
        $dompdf->setPaper('a4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        // $destinationPath = public_path('invoice');
        $destinationPath = public_path('invoice');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $filename = str_replace('/', '-', $data['invoice_id']);
        $fileName =   $filename.'-invoice.pdf';

        // Storage::put('public/' . $fileName,$output);
        file_put_contents($destinationPath.'/'.$fileName, $output);
        // $file->move($destinationPath, $fileName);
        // return response()->json([
        //     'success' => true,
        //     'message' => 'PDF saved successfully!',
        //     'file_path' => url('storage/invoice/'.$fileName),
        // ]);
        return $destinationPath.'/'.$fileName;
    }


    function formatNumber($number) {
        return number_format($number, 2, '.', '');
    }

    public function initiatePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'amount' => 'required|numeric',
            'id' => 'required',
            'form_type' => 'required',
            'user_id' => 'required',
        ]);
        // Check if validation fails
        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(), // Get the error messages
            ], 422);
        }



        $planDetails = '';
        $amount = 0;
        $userDetails = UserInquiry::where('id',$request->user_id)->first();
        if($request->form_type == 'schedule_call'){
            $planDetails = ScheduleCall::where('id',$request->id)->first();
            $amount = $planDetails->total_amount;
        }else if($request->form_type == 'talk_to_tax_expert'){
            $planDetails = TalkToExpert::where('id',$request->id)->first();
            $amount = $planDetails->total_amount;
        }else if($request->form_type == 'business_registration'){
            $planDetails = BusinessRegistration::where('id',$request->id)->first();
            $amount = $planDetails->total_amount;
        }else if($request->form_type == 'gst_queries'){
            $planDetails = GstQuerie::where('id',$request->id)->first();
            $amount = $planDetails->total_amount;
        }else if($request->form_type == 'tds_queries'){
            $planDetails = TdsQuerie::where('id',$request->id)->first();
            $amount = $planDetails->total_amount;
        }else if($request->form_type == 'itr_queries'){
            $planDetails = ItrQuerie::where('id',$request->id)->first();
            $amount = $planDetails->amount;
        }

        if($userDetails && $planDetails){


            $data = [
                'key' => env('PAYU_MERCHANT_KEY'),
                'txnid' =>  uniqid(),
                'amount' => $amount,
                'productinfo' => 'API Product', // Example: Replace with actual product info
                'firstname' => $userDetails->name,
                'email' => $userDetails->email,
                'phone' => $userDetails->mobile,
                'surl' => route('payu.callback_success'), // Success URL
                'furl' => route('payu.callback_failed'), // Failure URL
            ];


        }else{
            return response()->json([
                'message' => 'User & Plane not Found',
            ], 500);
        }

        $data['hash'] = $this->generateHash($data);
        $setData = [
            "txnid"=>$data['txnid'],
            "hash"=>$data['hash'],
            "amount"=>$data['amount'],
            "form_type"=>$request->form_type,
            "order_id"=>$request->id,
            "user_id"=>$request->user_id,
            "coupon_code"=>$planDetails['coupon_id'],
            "default_discount" =>$planDetails['default_discount']
        ];

        Transactions::create($setData);
        // dd("as");
        // Return the payment data as a JSON response
        return response()->json([
            'url' => env('PAYU_URL'),
            'data' => $data,
        ]);
    }

    public function generateHash($data)
    {
        $hashString = $data['key'] . '|' . $data['txnid'] . '|' . $data['amount'] . '|' .
            $data['productinfo'] . '|' . $data['firstname'] . '|' . $data['email'] . '|||||||||||' . env('PAYU_MERCHANT_SALT');

        return strtolower(hash('sha512', $hashString));

    }





    public function handleCallbackSuccess(Request $request)
    {
        $postedHash = $request->hash;
        $status = $request->status;
        // dd("sd");
        $invoice_id = generateInvoiceId();

        $generatedHash  = $this->generateHash($request);
        $pdfData = [];
        if ($status == 'success') {
            $updateData  =  Transactions::where('txnid',$request['txnid'])->first();
            $invoice_id = $invoice_id.$updateData['id'];
            $updateData->update(["status"=>'completed',"invoice_id"=> $invoice_id]);
            // $updateData  =  Transactions::where('txnid',$request['txnid'])->first();
            $setData = $updateData;
            $fileArr= [];
            if($updateData->form_type == 'talk_to_tax_expert'){

                $getCall = TalkToExpert::where('id', $updateData['order_id'])->first();
                $queryTypeArr[] = $getCall->query_type;
                $getQuery = Call_query_type($queryTypeArr);
                $QueryTypeName = implode(', ', $getQuery);
                $getPlan = getCallPlanAmount($getCall->plan);
                $duration = '10 mins';
                $date = $getCall->call_datetime;

                if($getCall->plan == 2){
                    $duration = '20 mins';
                }else if($getCall->plan == 3){
                    $duration = '30 mins';
                }


                $pdfData['plans']=[];
                array_push( $pdfData['plans'], ["name"=>$QueryTypeName.'-'.$getPlan['label'],"amount" =>$getPlan['value']]);
                $pdfData['paid_amount'] = $updateData['amount'];
                $pdfData['coupon'] =  $updateData['coupon_code'];
                $pdfData['default_discount'] =  $updateData['default_discount'];
                $pdfData['invoice_id'] =$invoice_id;
                $pdfData['user_id'] = $updateData['user_id'];
                $getTotal = sumAmountOfPlan($pdfData['plans']);

                $amount =  $getTotal['totalAmount'];
                $lessAmount = 0;
                $couponDiscount ;
                $defaultDiscount;
                if($pdfData['coupon'] != ""){
                    $couponDiscount = CalculateCoupon(null,$amount, $pdfData['coupon']);
                    if(isset($couponDiscount['finalAmount']) && isset($couponDiscount['getCoupon'])){
                        $lessAmount =$lessAmount + floor(($amount - $couponDiscount['finalAmount']) * 100) / 100;
                        $amount = floor($couponDiscount['finalAmount'] * 100) / 100;
                    }
                }
                // dd($amount);
                if($pdfData['default_discount'] > 0){
                    $couponDiscount = CalculateCoupon(null,$amount,  $pdfData['default_discount']);
                    if(isset($couponDiscount['finalAmount']) && isset($couponDiscount['getCoupon'])){
                        $lessAmount = $lessAmount + floor(($amount - $couponDiscount['finalAmount']) * 100) / 100;
                        $amount = floor($couponDiscount['finalAmount'] * 100) / 100;
                    }
                }

                $calculateTaxes = calculateTaxes($amount);
                $pdfData['totalAmount'] = $calculateTaxes['totalAmount'];
                $pdfData['subtotal'] =  $amount ;
                $pdfData['lessAmount'] =  $lessAmount;
                $pdfData['cgst'] =  $calculateTaxes['cgst'];
                $pdfData['sgst'] =  $calculateTaxes['sgst'];
                $pdfData['totalTax'] =  $calculateTaxes['totalTax'];


                // $pdfData['date'] = '1222-20-20';
                // $pdfData['buyer_name'] = 'kamla kaskh';
                // return view('invoice_template',['data' => $pdfData]);
                $pdf = $this->generatePdf($pdfData);
                //dd('ss');
                // $fileArr= [];
                array_push($fileArr,$pdf);
                $setData['other_details'] = [
                    'service_name' => $QueryTypeName,
                    'duration' => $duration,
                    'amount' => $calculateTaxes['totalAmount'],
                    'description' => $getCall['message'],
                    'date' => $date,
                ];



            }else if($updateData->form_type == 'itr_queries'){
                $getplanDetails = ItrQuerie::where('id',$updateData['order_id'])->first();
                $getplanDetails->income_type =  explode(',',$getplanDetails->income_type);
                $data = getItrPlanAmount($getplanDetails);
                $labels = []; // Array to store labels

                foreach ($data['plan'] as $item) {
                    if (isset($item['label'])) {
                        $labels[] = $item['label']; // Add label to the labels array
                    }
                }
                $commaSeparatedLabels = implode(", ", $labels);


                $pdfData['plans']=[];
                array_push( $pdfData['plans'], ["name"=>'ITR FEES AS PER FORM ATTACHED',"amount" =>$data['amount']]);

                $pdfData['paid_amount'] = $updateData['amount'];
                $pdfData['coupon'] =  $updateData['coupon_code'];
                $pdfData['default_discount'] =  $updateData['default_discount'];
                $pdfData['invoice_id'] =$invoice_id;
                $pdfData['user_id'] = $updateData['user_id'];
                $getTotal = sumAmountOfPlan($pdfData['plans']);


                $amount =  $getTotal['totalAmount'];
                $lessAmount = 0;
                $couponDiscount ;
                $defaultDiscount;
                if($pdfData['coupon'] != ""){
                    $couponDiscount = CalculateCoupon(null,$amount, $pdfData['coupon']);
                    if(isset($couponDiscount['finalAmount']) && isset($couponDiscount['getCoupon'])){
                        $lessAmount =$lessAmount + floor(($amount - $couponDiscount['finalAmount']) * 100) / 100;
                        $amount = floor($couponDiscount['finalAmount'] * 100) / 100;
                    }
                }
                // dd($amount);
                if($pdfData['default_discount'] > 0){
                    $couponDiscount = CalculateCoupon(null,$amount,  $pdfData['default_discount']);
                    if(isset($couponDiscount['finalAmount']) && isset($couponDiscount['getCoupon'])){
                        $lessAmount = $lessAmount + floor(($amount - $couponDiscount['finalAmount']) * 100) / 100;
                        $amount = floor($couponDiscount['finalAmount'] * 100) / 100;
                    }
                }

                $calculateTaxes = calculateTaxes($amount);
                $pdfData['totalAmount'] = $calculateTaxes['totalAmount'];
                $pdfData['subtotal'] =  $amount ;
                $pdfData['lessAmount'] =  $lessAmount;
                $pdfData['cgst'] =  $calculateTaxes['cgst'];
                $pdfData['sgst'] =  $calculateTaxes['sgst'];
                $pdfData['totalTax'] =  $calculateTaxes['totalTax'];

                $pdf = $this->generatePdf($pdfData);
                // $fileArr= [];
                array_push($fileArr,$pdf);

                // dd($pdfData);

                $setData['other_details'] = [
                    'service_name' => $commaSeparatedLabels,
                    'amount' => $updateData['amount']
                ];


            }else if ($updateData->form_type == 'gst_queries'){
                $getplanDetails = GstQuerie::where('id',$updateData['order_id'])->first();
                $service_name = getGstPlanAmount($getplanDetails);
                $pdfData['plans']=[];

                // $service_name['label']
                array_push( $pdfData['plans'], ["name"=>'GST FEES AS PER FORM ATTACHED',"amount" =>$service_name['value']]);


                $pdfData['paid_amount'] = $updateData['amount'];
                $pdfData['coupon'] =  $updateData['coupon_code'];
                $pdfData['default_discount'] =  $updateData['default_discount'];
                $pdfData['invoice_id'] =$invoice_id;
                $pdfData['user_id'] = $updateData['user_id'];
                $getTotal = sumAmountOfPlan($pdfData['plans']);


                $amount =  $getTotal['totalAmount'];
                $lessAmount = 0;
                $couponDiscount ;
                $defaultDiscount;
                if($pdfData['coupon'] != ""){
                    $couponDiscount = CalculateCoupon(null,$amount, $pdfData['coupon']);
                    if(isset($couponDiscount['finalAmount']) && isset($couponDiscount['getCoupon'])){
                        $lessAmount =$lessAmount + floor(($amount - $couponDiscount['finalAmount']) * 100) / 100;
                        $amount = floor($couponDiscount['finalAmount'] * 100) / 100;
                    }
                }
                // dd($amount);
                if($pdfData['default_discount'] > 0){
                    $couponDiscount = CalculateCoupon(null,$amount,  $pdfData['default_discount']);
                    if(isset($couponDiscount['finalAmount']) && isset($couponDiscount['getCoupon'])){
                        $lessAmount = $lessAmount + floor(($amount - $couponDiscount['finalAmount']) * 100) / 100;
                        $amount = floor($couponDiscount['finalAmount'] * 100) / 100;
                    }
                }

                $calculateTaxes = calculateTaxes($amount);
                $pdfData['totalAmount'] = $calculateTaxes['totalAmount'];
                $pdfData['subtotal'] =  $amount ;
                $pdfData['lessAmount'] =  $lessAmount;
                $pdfData['cgst'] =  $calculateTaxes['cgst'];
                $pdfData['sgst'] =  $calculateTaxes['sgst'];
                $pdfData['totalTax'] =  $calculateTaxes['totalTax'];

                $pdf = $this->generatePdf($pdfData);
                // $fileArr= [];
                array_push($fileArr,$pdf);

                $setData['other_details'] = [
                    'service_name' => $service_name['label'],
                    'amount' => $updateData['amount']
                ];


            }else if($updateData->form_type == 'business_registration'){
                $getplanDetails = BusinessRegistration::where('id',$updateData['order_id'])->first();

                $QueryType = $getplanDetails['plan'];
                $queryTypeArr = explode(", ",$QueryType);
                $getQuery = businessrReg_query_type($queryTypeArr);

                $getPlan = [];
                foreach($queryTypeArr as $value){

                    $planData = getBusinessrRegPlanAmount($value);
                    $nestendArr = ['name'=>$planData['label'],'amount'=>$planData['value']];
                    $getPlan[] = $nestendArr;
                    // $amount += $planData['value'];
                }


                $pdfData['plans']=$getPlan;
                // array_push( $pdfData['plans'], ["name"=>$service_name['label'],"amount" =>$service_name['value']]);


                $pdfData['paid_amount'] = $updateData['amount'];
                $pdfData['coupon'] =  $updateData['coupon_code'];
                $pdfData['default_discount'] =  $updateData['default_discount'];
                $pdfData['invoice_id'] =$invoice_id;
                $pdfData['user_id'] = $updateData['user_id'];
                $getTotal = sumAmountOfPlan($pdfData['plans']);

                $amount =  $getTotal['totalAmount'];
                $lessAmount = 0;
                $couponDiscount ;
                $defaultDiscount;
                if($pdfData['coupon'] != ""){
                    $couponDiscount = CalculateCoupon(null,$amount, $pdfData['coupon']);
                    if(isset($couponDiscount['finalAmount']) && isset($couponDiscount['getCoupon'])){
                        $lessAmount =$lessAmount + floor(($amount - $couponDiscount['finalAmount']) * 100) / 100;
                        $amount = floor($couponDiscount['finalAmount'] * 100) / 100;
                    }
                }
                // dd($amount);
                if($pdfData['default_discount'] > 0){
                    $couponDiscount = CalculateCoupon(null,$amount,  $pdfData['default_discount']);
                    if(isset($couponDiscount['finalAmount']) && isset($couponDiscount['getCoupon'])){
                        $lessAmount = $lessAmount + floor(($amount - $couponDiscount['finalAmount']) * 100) / 100;
                        $amount = floor($couponDiscount['finalAmount'] * 100) / 100;
                    }
                }

                $calculateTaxes = calculateTaxes($amount);
                $pdfData['totalAmount'] = $calculateTaxes['totalAmount'];
                $pdfData['subtotal'] =  $amount ;
                $pdfData['lessAmount'] =  $lessAmount;
                $pdfData['cgst'] =  $calculateTaxes['cgst'];
                $pdfData['sgst'] =  $calculateTaxes['sgst'];
                $pdfData['totalTax'] =  $calculateTaxes['totalTax'];

                $pdf = $this->generatePdf($pdfData);
                // $fileArr= [];
                array_push($fileArr,$pdf);



                $QueryTypeName = implode(', ', $getQuery);
                $setData['other_details'] = [
                    'service_name' => $QueryTypeName,
                    'amount' => $updateData['amount']
                ];
            }else if ($updateData->form_type == 'tds_queries'){
                $getplanDetails = TdsQuerie::where('id',$updateData['order_id'])->first();

                $typeOfReturnArr=[
                    '1' =>['label'=>'24Q','url'=>'1'],
                    '2' => ['label'=>'26Q','url'=>'1'],
                    '3' =>['label'=>'27Q','url'=>'1'] ,
                    '4' => ['label'=>'26QB','url'=>'1'],
                ];
                $QueryTypeName = $typeOfReturnArr[$getplanDetails->type_of_return]['label'];
                $getplanDetails['tax_planning'] =  $getplanDetails['tax_planning_of_employees'];
                $getPlan = getTSDPlanAmount($getplanDetails);

                // dd($getPlan);

                $QueryTypeName = 'TDS FEES AS PER FORM ATTACHED'; //$QueryTypeName.'- Number of employee '.$getPlan['label'];

                $pdfData['plans']=[];
                $computationQuery ='Computation & Tax Planning Service Fee for '.$getplanDetails['no_of_employees'].' Employees';
                array_push( $pdfData['plans'], ["name"=>$QueryTypeName,"amount" =>$getPlan['value']]);
                if($getplanDetails['type_of_return'] ==1 ){
                    array_push( $pdfData['plans'], ["name"=>$computationQuery,"amount" =>$getPlan['computation']]);
                }

                $pdfData['paid_amount'] = $updateData['amount'];
                $pdfData['coupon'] =  $updateData['coupon_code'];
                $pdfData['default_discount'] =  $updateData['default_discount'];
                $pdfData['invoice_id'] =$invoice_id;
                $pdfData['user_id'] = $updateData['user_id'];
                $getTotal = sumAmountOfPlan($pdfData['plans']);


                $amount =  $getTotal['totalAmount'];
                $lessAmount = 0;
                $couponDiscount ;
                $defaultDiscount;
                if($pdfData['coupon'] != ""){
                    $couponDiscount = CalculateCoupon(null,$amount, $pdfData['coupon']);
                    if(isset($couponDiscount['finalAmount']) && isset($couponDiscount['getCoupon'])){
                        $lessAmount =$lessAmount + floor(($amount - $couponDiscount['finalAmount']) * 100) / 100;
                        $amount = floor($couponDiscount['finalAmount'] * 100) / 100;
                    }
                }
                // dd($amount);
                if($pdfData['default_discount'] > 0){
                    $couponDiscount = CalculateCoupon(null,$amount,  $pdfData['default_discount']);
                    if(isset($couponDiscount['finalAmount']) && isset($couponDiscount['getCoupon'])){
                        $lessAmount = $lessAmount + floor(($amount - $couponDiscount['finalAmount']) * 100) / 100;
                        $amount = floor($couponDiscount['finalAmount'] * 100) / 100;
                    }
                }


                $calculateTaxes = calculateTaxes($amount);
                $pdfData['totalAmount'] = $calculateTaxes['totalAmount'];
                $pdfData['subtotal'] =  $amount ;
                $pdfData['lessAmount'] =  $lessAmount;
                $pdfData['cgst'] =  $calculateTaxes['cgst'];
                $pdfData['sgst'] =  $calculateTaxes['sgst'];
                $pdfData['totalTax'] =  $calculateTaxes['totalTax'];

                $pdf = $this->generatePdf($pdfData);

                array_push($fileArr,$pdf);

                $setData['other_details'] = [
                    'service_name' => $QueryTypeName,
                    'amount' => $updateData['amount']
                ];

            }
            $getOtherFiles = Documents::where('query_id',$updateData['order_id'])->where('form_type',$updateData['form_type'])->get();

            foreach ($getOtherFiles as $file) {
                $newPath = public_path('uploads/' . $file['file_url']);
                if (File::exists($newPath)) {
                    array_push($fileArr,$newPath);
                }
            }

            $this->sendMeassage($setData, $updateData->form_type,$fileArr);

            return redirect(env('CALL_BACK_URL'));
        }

        return redirect(env('CALL_BACK_ERROR_URL'));
    }

    public function sendMeassage($updateData,$formType,$filePaths = []){

        $userData = UserInquiry::where('id',$updateData->user_id)->first();

        $template = EmailTemplate::whereIn('type',[1,2,3])->where('form_type',$formType)->get();
        // dd($updateData);
        foreach ($template as $key => $value) {

            $message = str_replace("{client_name}",$userData->name,$value->description);
            $message = str_replace("{amount}",$updateData['other_details']['amount'],$message);
            $message = str_replace("{service_name}",$updateData['other_details']['service_name'],$message);
            $message = str_replace("{R_No}",$updateData['invoice_id'],$message);
            // $message = str_replace("{invoice_url}",$updateData['other_details']['invoice_url'],$message);

            if($formType == 'talk_to_tax_expert'){
                $message = str_replace("{mobile_number}",$userData['mobile'],$message);
                $formattedDate = date("Y-m-d h:i A", strtotime($updateData['other_details']['date']));
                $message = str_replace("{date_time}",$formattedDate,$message);
                $message = str_replace("{duration}",$updateData['other_details']['duration'],$message);
                $message = str_replace("{description}",$updateData['other_details']['description'],$message);
                // {description}
            }


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
            // dd($message);
            // $filePaths = $updateData['other_details']['files'];
            // dd($filePaths);
            if($value->type == 3){

                $data = [
                    'email' => $userData->email,
                    'title' => $value->subject,
                    'message' => $message,
                    'filePaths' => $filePaths
                ];
                SendEmailJob::dispatch($data);

                $data = [
                    'email' => env('ADMIN_EMAIL'),
                    'title' => $value->subject.'-'.$userData->name.'('.$userData['mobile'].')',
                    'message' => $message,
                    'filePaths' => $filePaths
                ];
                SendEmailJob::dispatch($data);

            }
        }


        return 1;

    }

    public function handleCallbackFailed(Request $request)
    {


        $postedHash = $request->hash;
        $status = $request->status;

        $generatedHash  = $this->generateHash($request);
        $updateData  =  Transactions::where('txnid',$request['txnid'])->first();
        $updateData->update(["status"=>'failed']);

        return redirect(env('CALL_BACK_ERROR_URL'));
    }




}
