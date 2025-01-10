<?php

function get_Oauth_token() {
    $curl = curl_init();
        
    $payload = "grant_type=client_credentials";

    curl_setopt_array($curl, [
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/x-www-form-urlencoded",
        "Authorization: Basic " . base64_encode($_ENV['UPS_TOKEN'])
    ],
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_URL => "https://wwwcie.ups.com/security/v1/oauth/token",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => "POST",
    ]);

    $response = curl_exec($curl);
    $error = curl_error($curl);

    $responseArray = json_decode($response, true);

    // Extract access token
    $accessToken = $responseArray['access_token'];

    
    curl_close($curl);
    return $accessToken;
}
?>