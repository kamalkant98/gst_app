<?php

// Define the API endpoint and parameters
$url = "http://cloud.smsindiahub.in/api/mt/SendSMS";
$params = [
    'APIKey' => '06qrB8RC40ilkY9XBtCytw', // Replace with your actual API key
    'senderid' => '121212',
    'channel' => 'Promo',
    'DCS' => '0',
    'flashsms' => '0',
    'number' => '918742072032', // Replace with the recipient's number
    'text' => 'test message',
    'route' => 'sdfsdf', // Replace with your route
    'PEId' => 'dsfsdf',
    'user'=>'Taxduniya',
    'password'=>'Taxduniya',   // Replace with your PEId
];

// Create the query string from parameters
$queryString = http_build_query($params);

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url . '?' . $queryString);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the request
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    echo 'Response: ' . $response;
}

// Close the cURL session
curl_close($ch);


?>