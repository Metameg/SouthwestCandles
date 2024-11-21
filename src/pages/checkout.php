<!DOCTYPE html>

<?php
// Get the cart data from the POST request
$cartJson = $_POST['cart'] ?? '[]'; // Use an empty JSON array as default
$cart = json_decode($cartJson, true); // Decode the JSON into a PHP array

// Validate the cart
if (!is_array($cart)) {
    $cart = [];
}

include '../plugins/payments/price_calculator.php';

$totalPrice = calcTotal($cart);

// function getProductPrice($size) {
//     // This could be a database query or a predefined list of products with prices
//     $prices = [
//         '4oz' => 9.00, 
//         '8oz' => 12.00,
//         '16oz' => 23.00
//     ];

//     return isset($prices[$size]) ? $prices[$size] : 0.00;
// }

// $totalPrice = 0;
// foreach ($cart as $item) {
//     if (isset($item['selectedSize'], $item['quantity']) && $item['quantity'] > 0) {
//         // Fetch product price using product_id
//         $price = getProductPrice($item['selectedSize']);
//         // Calculate the total price for this item (price * quantity)
//         $totalPrice += $price * $item['quantity'];
//     }
// }
?>

<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Southwest Candle Products</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/cart.css">
    <script src="/dist/bundle.js"></script>
    </head>
    <body>

        <!-- Navbar -->
        <?php $basePath = '../';?>
        <?php include $basePath . '/components/navbar.php';?>


        <!-- Cart -->
        <?php include $basePath . '/components/cart.php';?>
        
        <main>
            <h1>Your Cart</h1>
            <?php if (!empty($cart)): ?>
                <ul>
                    <?php foreach ($cart as $item): ?>
                        <li class="cart-item">
                            <strong><?php echo htmlspecialchars($item['name']); ?></strong> - 
                            Quantity: <?php echo htmlspecialchars($item['quantity']); ?> - 
                            Price: $<?php echo htmlspecialchars(number_format($item['price'], 2)); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Your cart is empty or invalid.</p>
            <?php endif; ?>
            <p>Total: $<?php echo number_format($totalPrice, 2) ?></p>
        </main>
    </body>
</html>
