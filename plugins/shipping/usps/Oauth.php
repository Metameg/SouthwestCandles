<?php

function get_Oauth_token() {
    $curl = curl_init();

       
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://apis.usps.com/oauth2/v3/token",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode(array(
            
            "grant_type" => "client_credentials",
            "client_id" => $_ENV['USPS_CLIENTID'],
            "client_secret" => $_ENV['USPS_SECRET'],
        )),
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json"
        ),
    ));
    
    $response = curl_exec($curl);
    $responseArray = json_decode($response, true);

    // Extract access token
    $access_token = $responseArray['access_token'];
    
    return $access_token;
}
?>