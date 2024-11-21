<!DOCTYPE html>

<?php
    // Include the database connection
    include '../../db.php';

    function getProductPrice($product_id) {
        // This could be a database query or a predefined list of products with prices
        $products = [
            1 => 10.99, // Product ID => Price
            2 => 5.49,
            3 => 20.00
        ];
    
        return isset($products[$product_id]) ? $products[$product_id] : 0;
    }
    
    // Get cart data from the query string
    $cartJson = $_GET['cart'] ?? '[]';
    $cart = json_decode($cartJson, true);
    
    // Validate and process cart data
    if (!is_array($cart)) {
        $cart = [];
    }
    
    $totalPrice = 0;
    foreach ($cart as $item) {
        if (isset($item['product_id'], $item['quantity']) && $item['quantity'] > 0) {
            // Fetch product price using product_id
            $price = getProductPrice($item['product_id']);
            // Calculate the total price for this item (price * quantity)
            $totalPrice += $price * $item['quantity'];
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
