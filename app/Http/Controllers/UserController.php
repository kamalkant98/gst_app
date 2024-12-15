<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function index(){

        $userData  = User::get();

        $twilioIds     = "";
        $twilioToken  = "";
        $twilioWhatsappNumber = "";
        $recipientNumber = "";
        $message = "Sumit Hello";

        $twilio = new Client($twilioIds, $twilioToken);


        // try{
        //     $twilio->messages->create($recipientNumber,
        //     [
        //         "from" => $twilioWhatsappNumber,
        //         "body" => $message
    
        //     ]);

        //     // return response()->json(['message' => 'Data fetched successfully!']);

        // }catch(\Exception $e){
        //     return response()->json(['message' => $e->getMessage()],500);

        // }
     
        return response()->json(['message' => 'Data fetched successfully!','data'=> $userData]);


    }

    public function store(Request $request ){

        $request = $request->all();

        $data = [
            'email' => $request['email'],
            'name' => 'Sumit',
            'password' =>  Hash::make( $request['password']),
        ];

        User::create($data);

        return response()->json(['message' => 'Data fetched successfully!']);

    }
}






