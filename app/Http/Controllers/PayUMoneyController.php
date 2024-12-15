<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PayUMoneyController extends Controller
{




    public function initiatePayment()
    {

        // return response()->json([
        //     'url' => env('PAYU_URL'),
        //     'data' => '$data',
        // ]);


        $data = [
            'key' => env('PAYU_MERCHANT_KEY'),
            'txnid' => uniqid(),
            'amount' => 1, // Amount in INR
            'productinfo' => 'Sample Product',
            'firstname' => 'John',
            'email' => 'john@example.com',
            'phone' => '9876543210',
            'surl' => route('payu.callback'), // Success URL
            'furl' => route('payu.callback'), // Failure URL
        ];

        $data['hash'] = $this->generateHash($data);


         return response()->json([
            'url' => env('PAYU_URL'),
            'data' => $data,
        ]);
        // return view('payment', ['data' => $data, 'url' => env('PAYU_URL')]);
    }

    // public function generateHash($data)
    // {
    //     $hashString = $data['key'] . '|' . $data['txnid'] . '|' . $data['amount'] . '|' .
    //         $data['productinfo'] . '|' . $data['firstname'] . '|' . $data['email'] . '|||||||||||' . env('PAYU_MERCHANT_SALT');

    //     return strtolower(hash('sha512', $hashString));
    // }

    // public function handleCallback(Request $request)
    // {
    //     $postedHash = $request->hash;
    //     $status = $request->status;

    //     $hashString = env('PAYU_MERCHANT_SALT') . '|' . $status . '|' . $request->email . '|' .
    //         $request->firstname . '|' . $request->productinfo . '|' . $request->amount . '|' .
    //         $request->txnid . '|' . env('PAYU_MERCHANT_KEY');

    //     $generatedHash = strtolower(hash('sha512', $hashString));

    //     if ($postedHash === $generatedHash) {
    //         return response('Payment Success', 200);
    //     }

    //     return response('Payment Failed', 400);
    // }


    ////



    // public function initiatePayment(Request $request)
    // {



    //     $validator = Validator::make($request->all(), [
    //         'amount' => 'required|numeric',
    //         'firstname' => 'required|string',
    //         'email' => 'required|email',
    //         'phone' => 'required|string',
    //     ]);
    //     // Check if validation fails
    //     if($validator->fails()) {
    //         return response()->json([
    //             'message' => 'Validation failed',
    //             'errors' => $validator->errors(), // Get the error messages
    //         ], 422);
    //     }


    //     // // Validate the incoming request
    //     // $request->validate([
    //     //     'amount' => 'required|numeric',
    //     //     'firstname' => 'required|string',
    //     //     'email' => 'required|email',
    //     //     'phone' => 'required|string',
    //     // ]);

    //     // return response()->json([
    //     //     'url' => env('PAYU_URL'),
    //     //     'data' =>"45234234",
    //     // ]);

    //     $data = [
    //         'key' => env('PAYU_MERCHANT_KEY'),
    //         'txnid' => uniqid(),
    //         'amount' => $request->amount,
    //         'productinfo' => 'API Product', // Example: Replace with actual product info
    //         'firstname' => $request->firstname,
    //         'email' => $request->email,
    //         'phone' => $request->phone,
    //         'surl' => route('payu.callback'), // Success URL
    //         'furl' => route('payu.callback'), // Failure URL
    //     ];

    //     $data['hash'] = $this->generateHash($data);

    //     // Return the payment data as a JSON response
    //     return response()->json([
    //         'url' => env('PAYU_URL'),
    //         'data' => $data,
    //     ]);
    // }

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

        if ($postedHash === $generatedHash) {
            return response()->json(['message' => 'Payment Success', 'status' => 'success'], 200);
        }

        return response()->json(['message' => 'Payment Failed', 'status' => 'failed'], 400);
    }



}
