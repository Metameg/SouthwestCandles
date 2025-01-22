<?php 
require_once(__DIR__ . '/../../../vendor/autoload.php');
require_once(__DIR__ . '/Oauth.php');

$dotenv_file_path = __DIR__ . '/../../../.env';
if (file_exists($dotenv_file_path)) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname($dotenv_file_path));
    $dotenv->load();
}

function get_USPS_rates() {
    define('ENV_THRESHOLD', 0.5);

    $curl = curl_init();
    $access_token = get_Oauth_token();
    $data = json_decode(file_get_contents('php://input'), true);
    $cart = $data['cart'];
    $address = $data['address'];
    $to_zip = $address['postal_code'];
    error_log(json_encode($address));
    
    // Get package weight from cart
    $weight = calc_package_weight($cart);
    // Determine whether or not an envelope will be used
    $is_envelope = $weight <= ENV_THRESHOLD;
    // Get dimensions of parcel
    $dims = calc_package_dims($weight, $is_envelope);
    $response = fetch_package_rates($access_token, $dims, $to_zip);
    

    curl_close($curl);

    return $response;
}

function fetch_package_rates($access_token, $dims, $to_zip) {
    $curl = curl_init();

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
            "originZIPCode"=> "77401",
            "destinationZIPCode"=> $to_zip,
            "weight"=> $dims['weight'],
            "length"=> $dims['length'],
            "width"=> $dims['width'],
            "height"=> $dims['height'],
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
    // $responseArray = json_decode($response, true);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($response === false || $http_code !== 200) {
        $error = curl_error($curl);
        return json_encode([
            'success' => false,
            'rateResponse' => $response
        ]);
    } else {
        error_log("NOT ERRROR" . json_encode($response));
        return json_encode([
            'success' => true,
            'rateResponse' => $response
        ]);
    }

}



// Helper functions

// Calculate package weight
function calc_package_dims($weight, $is_envelope) {
    // Package dims
    define('EMPTY_PKG_WEIGHT', 11.0 / 16.0);
    define('PKG_L', 6);
    define('PKG_W', 4);
    define('PKG_H', 3);
    // Envelope dims
    define('EMPTY_ENV_WEIGHT', 2.0 / 16.0);
    define('ENV_L', 15);
    define('ENV_W', 11.625);
    define('ENV_H', 0.75);


    if ($is_envelope) {
        $dims = ['length' => ENV_L, 'width' => ENV_W, 'height' => ENV_H, 'weight' => $weight + EMPTY_ENV_WEIGHT];
    } else {
        $dims = ['length' => PKG_L, 'width' => PKG_W, 'height' => PKG_H, 'weight' => $weight + EMPTY_PKG_WEIGHT];
    }
    return $dims;
}

// Calculate weight of package from cart array
function calc_package_weight($cart) {
    $total_oz = 0.0;

    foreach ($cart as $item) {
        // Extract the numeric value from the selectedSize
        if (isset($item['selectedSize'])) {
            $ounces = floatval(str_replace('oz', '', $item['selectedSize']));
            $total_oz += $ounces * $item['quantity'];
        }
    }
    error_log("This is the total_oz: " . $total_oz);
    return $total_oz / 16.0;
}




// execute the function if called as an endpoint (from js)
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $rates = get_USPS_rates();
    echo $rates;
}

?>