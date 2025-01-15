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
    echo $cart;
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
            $consolidated[$key]['quantity'] = $item['quantity'];
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
            if (!in_array($price, [9.00, 12.00, 23.00])) {
                return "There was an error checking you out. Please try again.";
            }
            // Calculate the total price for this item (price * quantity)
            $totalPrice += $price * $item['quantity'];
        }
    }

    return $totalPrice;
}

function extractUSPSOptions($rates) {
    // Define the list of SKUs to match
    $skus = [
        "DPXX0XXXXR05010",
        "DEXX0XXXXR05010",
        "DUXP0XXXXR05010"
    ];

    $validOptions = [];

    // Iterate through the rate options
    foreach ($rates as $opt) {
        if (isset($opt['rates'][0]['SKU']) && in_array($opt['rates'][0]['SKU'], $skus)) {
            $validOptions[] = $opt;
        }
    }

    return $validOptions;
}

function build_sku_to_price_map($shippingData) {
    // Build the SKU-to-price map
    $skuToPriceMap = [];

    foreach ($shippingData as $item) {
        if (isset($item['rates'][0]['SKU']) && isset($item['rates'][0]['price'])) {
            $sku = $item['rates'][0]['SKU'];
            $price = $item['rates'][0]['price'];
            $skuToPriceMap[$sku] = $price;
        }
    }

    // add default price for when no option is selected
    $skuToPriceMap['default'] = 0.00;
    return $skuToPriceMap;
}
?>