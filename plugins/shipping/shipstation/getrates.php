<?php
    require_once('../../vendor/autoload.php');
    
    $dotenv_file_path = __DIR__ . '/../../.env';
    if (file_exists($dotenv_file_path)) {
        $dotenv = Dotenv\Dotenv::createImmutable(dirname($dotenv_file_path));
        $dotenv->load();
    }

    $curl = curl_init();
    $username = $_ENV['SHIPSTATION_USERNAME'];
    $secret = $_ENV['SHIPSTATION_SECRET'];
    $api_key = $username . ':' . $secret;
    $base64 = base64_encode($api_key);

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://ssapi.shipstation.com/shipments/getrates",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode(array(
            "carrierCode" => "fedex_walleted",
            "serviceCode" => null,
            "packageCode" => null,
            "fromPostalCode" => "76201",
            "toState" => "UT",
            "toCountry" => "US",
            "toPostalCode" => "84098",
            "toCity" => "Park City",
            "weight" => array(
                "value" => 1,
                "units" => "pounds"
            ),
            "dimensions" => array(
                "units" => "inches",
                "length" => 5,
                "width" => 5,
                "height" => 5
            ),
            "confirmation" => "delivery",
            "residential" => false
        )),
        CURLOPT_HTTPHEADER => array(
            "Host: ssapi.shipstation.com",
            "Authorization: Basic " . $base64,
            "Content-Type: application/json"
        ),
    ));
    
    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    if ($response === false || $http_code !== 200) {
        $error = curl_error($curl);
        echo "HTTP Code: $http_code\n";
        echo "cURL Error: $error\n";
        echo "Response: $response\n";
    } else {
        echo "Response: $response\n";
    }
    
    curl_close($curl);
?>