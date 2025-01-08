<?php
require_once('../../vendor/autoload.php');

$dotenv_file_path = __DIR__ . '/../../.env';
if (file_exists($dotenv_file_path)) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname($dotenv_file_path));
    $dotenv->load();
}
\Stripe\Stripe::setApiKey($_ENV['STRIPE_SK_TEST']);

function consolidateCart($cart) {
    $consolidated = [];

    // Iterate over the cart items
    foreach ($cart as $item) {
        // Use a unique identifier, e.g., 'data_id', to group items
        $key = $item['selectedSize'] . '|' . $item['wickType'] . '|' . $item['name'];

        // Check if the item already exists in the consolidated array
        if (isset($consolidated[$key])) {
            // Update the quantity if the item already exists
            $consolidated[$key]['quantity'] += 1;
        } else {
            // Add the item to the consolidated array with an initial quantity
            $consolidated[$key] = $item;
            $consolidated[$key]['quantity'] = 1;
        }
    }

    // Re-index the consolidated array to return a list
    return array_values($consolidated);
}

function getProductPrice($size) {
    // This could be a database query or a predefined list of products with prices
    $prices = [
        '4oz' => 9.00, 
        '8oz' => 12.00,
        '16oz' => 23.00
    ];

    return isset($prices[$size]) ? $prices[$size] : 0.00;
}


function calcSubtotal($cart) {
    $totalPrice = 0.00;
    foreach ($cart as $item) {
        if (isset($item['selectedSize'], $item['quantity']) && $item['quantity'] > 0) {
            // Fetch product price using product_id
            $price = getProductPrice($item['selectedSize']);
            // Calculate the total price for this item (price * quantity)
            $totalPrice += $price * $item['quantity'];
        }
    }

    return $totalPrice;
}
?>