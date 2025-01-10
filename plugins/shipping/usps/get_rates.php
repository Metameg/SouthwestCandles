<?php 
require_once('../../../vendor/autoload.php');
require_once('Oauth.php');

$dotenv_file_path = __DIR__ . '/../../../.env';
if (file_exists($dotenv_file_path)) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname($dotenv_file_path));
    $dotenv->load();
}


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
        // "PARCEL_SELECT"
        ],
        "priceType"=> "RETAIL",
        "mailingDate"=> "2025-01-11",
        // "accountType"=> "EPS",
        // "accountNumber"=> "1234567890",
        // "itemValue"=> 0,
        
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
    echo "Response: $response\n";
}


curl_close($curl);


// Respond with the canceled PaymentIntent details
echo json_encode([
    'success' => true,
    'rateResponse' => $response
]);

?>
