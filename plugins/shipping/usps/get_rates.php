<?php 
require_once(__DIR__ . '/../../../vendor/autoload.php');
require_once(__DIR__ . '/Oauth.php');

$dotenv_file_path = __DIR__ . '/../../../.env';
if (file_exists($dotenv_file_path)) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname($dotenv_file_path));
    $dotenv->load();
}

function getUSPSRates() {
    $curl = curl_init();
    $access_token = get_Oauth_token();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://apis.usps.com/prices/v3/base-rates-list/search",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode(array(
            "originZIPCode"=> "76201",
            "destinationZIPCode"=> "84098",
            "weight"=> 1,
            "length"=> 5,
            "width"=> 5,
            "height"=> 5,
            "mailClasses"=> [
            "PRIORITY_MAIL_EXPRESS",
            "PRIORITY_MAIL",
            "USPS_GROUND_ADVANTAGE",
            ],
            "priceType"=> "RETAIL",
            "mailingDate"=> date("Y-m-d"),
        
        )),
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer " . $access_token
        ),
    ));

    $response = curl_exec($curl);
    $responseArray = json_decode($response, true);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($response === false || $http_code !== 200) {
        $error = curl_error($curl);
        echo "HTTP Code: $http_code\n";
        echo "cURL Error: $error\n";
        echo "Response: $response\n";
    } else {
        return json_encode([
            'success' => true,
            'rateResponse' => $response
        ]);
    }

    curl_close($curl);
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $rates = getUSPSRates();
    echo $rates;
}

?>