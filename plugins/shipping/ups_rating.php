<?php 
require_once('../../vendor/autoload.php');
require_once('Oauth.php');

$dotenv_file_path = __DIR__ . '/../../.env';
if (file_exists($dotenv_file_path)) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname($dotenv_file_path));
    $dotenv->load();
}



$requestoption = "shop";
$version = "v2403";
$accessToken = get_Oauth_token();
$curl = curl_init();

$payload = array(
    "RateRequest" => array(
        "Request" => array(
            "TransactionReference" => array(
                "CustomerContext" => "CustomerContext",
                "TransactionIdentifier" => "TransactionIdentifier"
            )
        ),
        "Shipment" => array(
            "Shipper" => array(
                "Name" => "ShipperName",
                "ShipperNumber" => $_ENV['UPS_ACCOUNT_NUM'],
                "Address" => array(
                    "AddressLine" => array(
                        "3235 Heritage Trail Blvd.",
                        "Apt. 2301"
                    ),
                    "City" => "DENTON",
                    "StateProvinceCode" => "TX",
                    "PostalCode" => "76201",
                    "CountryCode" => "US"
                )
            ),
            "ShipTo" => array(
                "Name" => "ShipToName",
                "Address" => array(
                    "AddressLine" => array("4806 W Discovery Way"),
                    "City" => "KIMBALL JUNCTION",
                    "StateProvinceCode" => "UT",
                    "PostalCode" => "84098",
                    "CountryCode" => "US"
                )
            ),
            "NumOfPieces" => "1",
            "Package" => array(
                "PackagingType" => array(
                    "Code" => "02", // Customer Supplied Packaging
                    "Description" => "Packaging"
                ),
                "Dimensions" => array(
                    "UnitOfMeasurement" => array(
                        "Code" => "IN",
                        "Description" => "Inches"
                    ),
                    "Length" => "5",
                    "Width" => "5",
                    "Height" => "5"
                ),
                "PackageWeight" => array(
                    "UnitOfMeasurement" => array(
                        "Code" => "LBS",
                        "Description" => "Pounds"
                    ),
                    "Weight" => "1"
                )
            )
        )
    )
);



// Send curl request
curl_setopt_array($curl, [
CURLOPT_HTTPHEADER => [
    "Authorization: Bearer " . $accessToken,
    "Content-Type: application/json"
],
CURLOPT_POSTFIELDS => json_encode($payload),
CURLOPT_URL => "https://onlinetools.ups.com/api/rating/" . $version . "/" . $requestoption,
CURLOPT_RETURNTRANSFER => true,
CURLOPT_CUSTOMREQUEST => "POST",
]);

$response = curl_exec($curl);
$error = curl_error($curl);

curl_close($curl);
// echo json_encode($payload, JSON_PRETTY_PRINT);

// if ($error) {
//     echo "cURL Error #:" . $error;
// } else {
//     echo $response;
// }

// Respond with the canceled PaymentIntent details
echo json_encode([
    'success' => true,
    'rateResponse' => $response
]);

?>
