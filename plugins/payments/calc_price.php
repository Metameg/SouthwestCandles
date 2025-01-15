<?php
require_once('../../vendor/autoload.php');
require_once('payment_intent.php');
require_once('../shipping/usps/get_rates.php');
require_once('utilities.php');

$dotenv_file_path = __DIR__ . '/../../.env';
if (file_exists($dotenv_file_path)) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname($dotenv_file_path));
    $dotenv->load();
}
\Stripe\Stripe::setApiKey($_ENV['STRIPE_SK_TEST']);

const shipping_options = [
    'DUXP0XXXXR05010' => 'USPS Ground Advantage', 
    'DEXX0XXXXR05010' => 'Priority Mail Express', 
    'DPXX0XXXXR05010' => 'Priority Mail', 
];

session_start();


$data = json_decode(file_get_contents('php://input'), true);
$cart = consolidateCart($data['cart']);
$payment_intent_id = $data['payment_intent_id'];
$address = $data['address'];
$sku = $data['sku'] ?? null;
$line_items = [];



// Handle initial load (no SKU selected yet)
if (is_null($sku)) {
    // If the user has not selected a shipping option, reset the session flag to false
    $_SESSION['shippingSelected'] = false;
    if (!isset($_SESSION['shippingSelected']) || $_SESSION['shippingSelected'] === false) {
        $sku = 'default';
    } else {
        // Invalid state: SKU cannot be null after a shpping option is selected
        http_response_code(400);
        echo json_encode(['error' => 'Shipping option must be selected.']);
        exit;
    }
}

// Get rates for validating incoming data's shipping price
$rates = getUSPSRates();
$result = json_decode($rates, true); // true to return an associative array

// Check if decoding was successful
if ($result && isset($result['rateResponse'])) {
    // Decode the nested "rateResponse" field
    $rateResponse = json_decode($result['rateResponse'], true);

    if ($rateResponse && isset($rateResponse['rateOptions'])) {
        // Extract "rateOptions"
        $rateOptions = $rateResponse['rateOptions'];
    }
}

$validOptions = extractUSPSOptions($rateOptions);

// Get the price based on sku
$skuToPriceMap = build_sku_to_price_map($validOptions);
// Get the shipping option name from sku unless sku is 'default'
if (array_key_exists($sku, shipping_options)) {
    $shipping_option = shipping_options[$sku];
} else {
    $shipping_option = null;
}

if (!array_key_exists($sku, $skuToPriceMap)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid shipping option selected.']);
    exit;
}

// Mark the user as having selected a shipping option
$_SESSION['shippingSelected'] = true;
// Set the correct price for the SKU
$shipping_cost = $skuToPriceMap[$sku];


foreach ($cart as $item) {
    // Get the item price from the product details

    try {
        $itemPrice = getProductPrice($item['selectedSize']); 
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
        exit; // Stop further execution
    } 
    
    // Add the line item to the array
    $line_items[] = [
        'amount' => $itemPrice * 100 * $item['quantity'], 
        'quantity' => $item['quantity'],
        'tax_code' => 'txcd_33110005',
        'reference' => $item['name'] . ' - ' . $item['selectedSize']
    ];
}
    
$calculation = \Stripe\Tax\Calculation::create([
    'currency' => 'usd',
    'line_items' => $line_items,
    'customer_details' => [
        'address' => [
        'line1' => $address['line1'],
        'city' => $address['city'],
        'state' => $address['state'],
        'postal_code' => $address['postal_code'],
        'country' => $address['country'],
        ],
        'address_source' => 'shipping',
    ],
    'shipping_cost' => [
        'amount' => $shipping_cost * 100, // Shipping cost in cents
    ]
]);

$amount_total = $calculation['amount_total'];
$tax_calculation_id = $calculation['id'];
updatePaymentIntent($payment_intent_id, $amount_total,  $tax_calculation_id, $shipping_option);

echo json_encode([
    'success' => true,
    'shipping_price' => $calculation['shipping_cost']['amount'],
    'estimated_tax' => $calculation['tax_amount_exclusive'],
    'total_price' => $amount_total
]);
?>