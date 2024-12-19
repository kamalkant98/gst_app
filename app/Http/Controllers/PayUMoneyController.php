<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ScheduleCall;
use App\Models\Coupon;
use App\Models\UserInquiry;
use App\Models\Transactions;

class PayUMoneyController extends Controller
{

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

        $planDetails;
        $userDetails;
        if($request->form_type == 'schedule_call'){
            $planDetails = ScheduleCall::where('id',$request->id)->first();
            $userDetails = UserInquiry::where('id',$request->user_id)->first();
        }


        if($userDetails && $planDetails){


            $data = [
                'key' => env('PAYU_MERCHANT_KEY'),
                'txnid' => uniqid(),
                'amount' => 1,//$planDetails->total_amount,
                'productinfo' => 'API Product', // Example: Replace with actual product info
                'firstname' => $userDetails->name,
                'email' => $userDetails->email,
                'phone' => $userDetails->mobile,
                'surl' => route('payu.callback'), // Success URL
                'furl' => route('payu.callback'), // Failure URL
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
            "user_id"=>$request->user_id

        ];

        Transactions::create($setData);

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



    public function handleCallback(Request $request)
    {
        $postedHash = $request->hash;
        $status = $request->status;
        $hashString = env('PAYU_MERCHANT_SALT') . '|' . $status . '|' . $request->email . '|' .
            $request->firstname . '|' . $request->productinfo . '|' . $request->amount . '|' .
            $request->txnid . '|' . env('PAYU_MERCHANT_KEY');

        $generatedHash = strtolower(hash('sha512', $hashString));

        if ($postedHash === $generatedHash && $status == 200) {
            // Redirect to success page
            $updateData  =  Transactions::where('hash',$postedHash)->first();
            $updateData->update(["status"=>'completed']);
            return redirect(env('CALL_BACK_URL'));
        }

        // Redirect to failure page
        return redirect(env('CALL_BACK_URL'));
    }




}
