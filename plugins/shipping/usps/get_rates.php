<?php 
require_once(__DIR__ . '/../../../vendor/autoload.php');
require_once(__DIR__ . '/Oauth.php');
require_once(__DIR__ . '/../../payments/utilities.php');

$dotenv_file_path = __DIR__ . '/../../../.env';
if (file_exists($dotenv_file_path)) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname($dotenv_file_path));
    $dotenv->load();
}

// Package dimensions
const EMPTY_PKG_WEIGHT = 11.0 / 16.0;
const PKG_L = 6;
const PKG_W = 4;
const PKG_H = 3;

// Envelope dimensions
const EMPTY_ENV_WEIGHT = 2.0 / 16.0;
const ENV_L = 15;
const ENV_W = 11.625;
const ENV_H = 0.75;


function get_USPS_rates() {
    define('ENV_WEIGHT_LIMIT', 0.5);
    define('PKG_WEIGHT_LIMIT', 2);

    $curl = curl_init();
    $access_token = get_Oauth_token();
    $data = json_decode(file_get_contents('php://input'), true);
    $cart = $data['cart'];
    $subtotal = calcSubtotal($cart);
    if (is_string($subtotal)) {
        return json_encode(['error' => 'Something went wrong. Please refresh the page and try again.']);
    }
    $address = $data['address'];
    $to_zip = $address['postal_code'];
    
    // Get package weight from cart
    $weight = calc_package_weight_lbs($cart);
    $partial_weight = fmod($weight, PKG_WEIGHT_LIMIT);  // floating point modular
    $has_partial = $partial_weight != 0;
    
    // For full package
    $num_full_packages = intdiv((int)$weight, PKG_WEIGHT_LIMIT);
    $full_pkg_response = null;
    if ($num_full_packages >= 1) {
        $full_pkg_dims = calc_package_dims(PKG_WEIGHT_LIMIT, false);
        $full_pkg_response = fetch_package_rates($access_token, $full_pkg_dims, $to_zip);    
    }

    // For partial package
    $is_envelope = $partial_weight <= ENV_WEIGHT_LIMIT;
    $partial_pkg_dims = calc_package_dims($partial_weight, $is_envelope);
    $partial_pkg_response = fetch_package_rates($access_token, $partial_pkg_dims, $to_zip);

    $aggregate_response = aggregate_responses($partial_pkg_response, $full_pkg_response, $num_full_packages, $has_partial);

    curl_close($curl);

    return $aggregate_response;
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
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($response === false || $http_code !== 200) {
        $error = curl_error($curl);
        return json_encode([
            'success' => false,
            'rateResponse' => $response
        ]);
    } else {
        return json_encode([
            'success' => true,
            'rateResponse' => json_decode($response)
        ]);
    }

}



// Helper functions

// Calculate package weight
function calc_package_dims($weight, $is_envelope) {
    
    if ($is_envelope) {
        $dims = ['length' => ENV_L, 'width' => ENV_W, 'height' => ENV_H, 'weight' => $weight + EMPTY_ENV_WEIGHT];
    } else {
        $dims = ['length' => PKG_L, 'width' => PKG_W, 'height' => PKG_H, 'weight' => $weight + EMPTY_PKG_WEIGHT];
    }
    return $dims;
}

// Calculate weight of package from cart array
function calc_package_weight_lbs($cart) {
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

function aggregate_responses($partial_package_response, $full_package_response, $num_packages, $has_partial) {

    if (isset($full_package_response)) {
        $responseArrayFull = json_decode($full_package_response, true);
        foreach ($responseArrayFull['rateResponse']['rateOptions'] as &$rateOption) {         
            $rateOption['rates'][0]['price'] *= $num_packages;
        }
        
        $responseArrayPartial = json_decode($partial_package_response, true);

        // Extract rate options
        $rateOptionsFull = $responseArrayFull['rateResponse']['rateOptions'];
        $rateOptionsPartial = $responseArrayPartial['rateResponse']['rateOptions'];

        $agg_options = $has_partial  ? aggregate_prices($rateOptionsPartial, $rateOptionsFull) : $rateOptionsFull;
        error_log(print_r($agg_options, true) . $has_partial);
        $aggregated_response = [
            'success' => true,
            'rateResponse' => [
                'rateOptions' => $agg_options
            ]
        ];

        return json_encode($aggregated_response);
    } 

    return $partial_package_response;
}


function aggregate_prices($rateOptions1, $rateOptions2) {

    // Aggregate rate options by matching SKU
    $aggregatedRateOptions = [];
    foreach ($rateOptions1 as $option1) {
        foreach ($rateOptions2 as $option2) {
            // Check if SKUs match
            $sku1 = substr($option1['rates'][0]['SKU'], 0, -4);
            $sku2 = substr($option2['rates'][0]['SKU'], 0, -4);

            if ( $option1['rates'][0]['productName'] === $option2['rates'][0]['productName']) {
                $updatedRates = $option1;
                $updatedRates['rates'][0]['price'] += $option2['rates'][0]['price'];
    
                // Aggregate rates for matching SKUs
                $aggregatedRateOptions[] = $updatedRates;
                break; // Stop searching once a match is found
            }
        }
    }
    
    return $aggregatedRateOptions;
}


// execute the function if called as an endpoint (from js)
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $rates = get_USPS_rates();
    echo $rates;
}

?>