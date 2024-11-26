<?php

function _getProductPrice($size) {
    // This could be a database query or a predefined list of products with prices
    $prices = [
        '4oz' => 9.00, 
        '8oz' => 12.00,
        '16oz' => 23.00
    ];

    return isset($prices[$size]) ? $prices[$size] : 0.00;
}

function calcTotal($cart) {
    $totalPrice = 0.00;
    foreach ($cart as $item) {
        if (isset($item['selectedSize'], $item['quantity']) && $item['quantity'] > 0) {
            // Fetch product price using product_id
            $price = _getProductPrice($item['selectedSize']);
            // Calculate the total price for this item (price * quantity)
            $totalPrice += $price * $item['quantity'];
        }
    }

    return $totalPrice;
}
?>