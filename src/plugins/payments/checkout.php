<!DOCTYPE html>

<?php
// Get the cart data from the POST request
$cartJson = $_POST['cart'] ?? '[]'; // Use an empty JSON array as default
$cart = json_decode($cartJson, true); // Decode the JSON into a PHP array

// Validate the cart
if (!is_array($cart)) {
    $cart = [];
}

// Example of processing the cart (calculate prices, etc.)
// For this example, assume you fetch product data by ID
function getProductPrice($product_id) {
    $products = [
        1 => 10.99,
        2 => 5.49,
        3 => 20.00
    ];
    return $products[$product_id] ?? 0;
}

$totalPrice = 0;
foreach ($cart as $item) {
    if (isset($item['product_id'], $item['quantity']) && $item['quantity'] > 0) {
        $totalPrice += getProductPrice($item['product_id']) * $item['quantity'];
    }
}
?>

<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Southwest Candle Products</title>
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="../../css/navbar.css">
    <link rel="stylesheet" href="../../css/cart.css">
    <script src="/dist/bundle.js"></script>
    </head>
    <body>

        <!-- Navbar -->
        <?php $basePath = '../..';?>
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
        </main>
    </body>
</html>
