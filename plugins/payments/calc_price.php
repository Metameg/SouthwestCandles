<?php
require_once('../../vendor/autoload.php');
require_once('utilities.php');

$dotenv_file_path = __DIR__ . '/../../.env';
if (file_exists($dotenv_file_path)) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname($dotenv_file_path));
    $dotenv->load();
}
\Stripe\Stripe::setApiKey($_ENV['STRIPE_SK_TEST']);


$data = json_decode(file_get_contents('php://input'), true);
$cart = consolidateCart($data['cart']);
$address = $data['address'];
$line_items = [];

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
        'amount' => 500, // Shipping cost in cents
    ]
]);

echo json_encode([
    'success' => true,
    'estimated_tax' => $calculation['tax_amount_exclusive'],
    'total_price' => $calculation['amount_total'],
    'calculation' => $calculation
]);
?>