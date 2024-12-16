<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PayUMoneyController extends Controller
{

    public function initiatePayment(Request $request)
    {



        $validator = Validator::make($request->all(), [
            // 'amount' => 'required|numeric',
            'name' => 'required|string',
            'email' => 'required|email',
            'mobile' => 'required|string',
        ]);
        // Check if validation fails
        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(), // Get the error messages
            ], 422);
        }


        $data = [
            'key' => env('PAYU_MERCHANT_KEY'),
            'txnid' => uniqid(),
            'amount' => 1,
            'productinfo' => 'API Product', // Example: Replace with actual product info
            'firstname' => $request->name,
            'email' => $request->email,
            'phone' => $request->mobile,
            'surl' => route('payu.callback'), // Success URL
            'furl' => route('payu.callback'), // Failure URL
        ];

        $data['hash'] = $this->generateHash($data);

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

    // public function handleCallback(Request $request)
    // {
    //     $postedHash = $request->hash;
    //     $status = $request->status;

    //     $hashString = env('PAYU_MERCHANT_SALT') . '|' . $status . '|' . $request->email . '|' .
    //         $request->firstname . '|' . $request->productinfo . '|' . $request->amount . '|' .
    //         $request->txnid . '|' . env('PAYU_MERCHANT_KEY');

    //     $generatedHash = strtolower(hash('sha512', $hashString));

    //     if ($postedHash === $generatedHash) {
    //         return response()->json(['message' => 'Payment Success', 'status' => 'success'], 200);
    //     }

    //     return response()->json(['message' => 'Payment Failed', 'status' => 'failed'], 400);
    // }

    public function handleCallback(Request $request)
{
    $postedHash = $request->hash;
    $status = $request->status;
    $hashString = env('PAYU_MERCHANT_SALT') . '|' . $status . '|' . $request->email . '|' .
        $request->firstname . '|' . $request->productinfo . '|' . $request->amount . '|' .
        $request->txnid . '|' . env('PAYU_MERCHANT_KEY');

    $generatedHash = strtolower(hash('sha512', $hashString));

    if ($postedHash === $generatedHash) {
        // Redirect to success page
        return redirect(env('CALL_BACK_URL'));
    }

    // Redirect to failure page
    return redirect(env('CALL_BACK_URL'));
}




}
