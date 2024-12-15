<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PayUMoneyController extends Controller
{

    public function initiatePayment(Request $request)
    {
        $merchantKey = env('PAYU_MERCHANT_KEY');
        $merchantSalt = env('PAYU_MERCHANT_SALT');

        // Validate the request data
        $request->validate([
            'amount' => 'required|numeric',
            'firstname' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
        ]);

        $data = [
            'key' => $merchantKey,
            'txnid' => uniqid(), // Unique Transaction ID
            'amount' => $request->amount,
            'productinfo' => $request->productinfo,
            'firstname' => $request->firstname,
            'email' => $request->email,
            'phone' => $request->phone,
            'surl' => route('payumoney.success'), // Success URL
            'furl' => route('payumoney.failure'), // Failure URL
        ];

        // Generate a hash for security
        $hashString = $data['key'] . '|' . $data['txnid'] . '|' . $data['amount'] . '|' . $data['productinfo'] . '|' . $data['firstname'] . '|' . $data['email'] . '|||||||||||' . $merchantSalt;
        $data['hash'] = strtolower(hash('sha512', $hashString));

        // PayUMoney form action URL (Sandbox mode)
        $paymentUrl = env('PAYU_URL');

        // Redirect to PayUMoney
        return view('pay.redirect', compact('data', 'paymentUrl'));
    }

    public function paymentSuccess(Request $request)
    {
        // Handle successful payment
        return response()->json(['status' => 'success', 'data' => $request->all()]);
    }

    public function paymentFailure(Request $request)
    {
        // Handle failed payment
        return response()->json(['status' => 'failure', 'data' => $request->all()]);
    }

    ////////////

    public function pay()
    {
        $data = [
            'key' => env('PAYU_MERCHANT_KEY'),
            'txnid' => uniqid(),
            'amount' => 1, // Amount in INR
            'productinfo' => 'Sample Product',
            'firstname' => 'John',
            'email' => 'john@example.com',
            'phone' => '9876543210',
            'surl' => route('pay.success'), // Success URL
            'furl' => route('pay.failure'), // Failure URL
        ];

        $data['hash'] = $this->generateHash($data);
        return view('pay.pay',['payuForm' => $data, 'url' => env('PAYU_URL')]);
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

        if ($postedHash === $generatedHash) {
            return response('Payment Success', 200);
        }

        return response('Payment Failed', 400);
    }

    // public function pay()
    // {
    //     $data = [
    //         'txnid' => uniqid(),   // Unique transaction ID
    //         'amount' => 100,       // Payment amount
    //         'productinfo' => 'Product description',
    //         'firstname' => 'John Doe',
    //         'email' => 'john@example.com',
    //         'phone' => '9876543210',
    //         'surl' => route('pay.success'),  // Success URL
    //         'furl' => route('pay.failure'),  // Failure URL
    //     ];

    //     $payuForm = $this->generatePayUMoneyForm($data);

    //     return view('pay.pay', compact('payuForm'));
    // }

    // public function generatePayUMoneyForm($data)
    // {
    //     // Generate the PayUMoney hash
    //     // $key = env('PAYUMONEY_KEY');
    //     // $salt = env('PAYUMONEY_SALT');
    //     $key = 'Lr7qwE';
    //     $salt = 'GWBu66KrEB3DAbhBPo75fk7rYtMboY96';
    //     $data['key'] = $key;
    //     $data['salt'] = $salt;

    //     // Generate hash for security (used for transaction verification)
    //     $hashString = $key . '|' . $data['txnid'] . '|' . $data['amount'] . '|' . $data['productinfo'] . '|' . $data['firstname'] . '|' . $data['email'] . '|' . $data['surl'] . '|' . $data['furl'] . '|||||' . $salt;
    
    //     // Generate hash
    //     $hash = strtolower(hash('sha512', $hashString));
        
    //     $data['hash'] = $hash;

    //     return $data;
    // }

    // public function success(Request $request)
    // {
    //     // Handle successful payment (can be used to update order status)
    //     return view('pay.success');
    // }

    // public function failure(Request $request)
    // {
    //     // Handle failed payment
    //     return view('pay.failure');
    // }

    // public function notify(Request $request)
    // {
    //     // Handle PayUMoney notification
    //     $response = $request->all();

    //     if ($response['status'] == 'success') {
    //         // Update payment status in your system
    //         return redirect()->route('pay.success');
    //     } else {
    //         return redirect()->route('pay.failure');
    //     }
    // }

}
