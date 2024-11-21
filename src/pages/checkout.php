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
?>

<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Southwest Candle Products</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/cart.css">
    <link rel="stylesheet" href="../css/checkout.css">
    <script src="/dist/bundle.js"></script>
    </head>
    <body>

        <!-- Navbar -->
        <?php $basePath = '../';?>
        <?php include $basePath . '/components/navbar.php';?>


        <!-- Cart -->
        <?php include $basePath . '/components/cart.php';?>
        
        <main>
            <div class="checkout-page">
                <h1>Getting your order</h1>
                <div class="checkout-container">
                    <div class="shipping-details">
                        <h2>Shipping Details</h2>
                        <div class="shipping-summary">

                            <?php foreach ($cart as $item): ?>
                                <div class="item">
                                    <img src="../<?php echo htmlspecialchars($item['image']); ?>" alt="Placeholder candle product image">
                                    <div class="details">
                                        <p><?php echo htmlspecialchars($item['name']); ?></p>
                                        <p><strong>Tomorrow, November 22</strong> <span class="free">FREE</span></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <form class="shipping-form">
                            <div class="form-row">
                                <input type="text" placeholder="First Name" required>
                                <input type="text" placeholder="Last Name" required>
                            </div>
                            <input type="text" placeholder="Address" required>
                            <p class="helper-text">Military (APO/FPO) | International Customers</p>
                            <div class="form-row">
                                <input type="text" placeholder="City" required>
                                <select>
                                    <option value="">State</option>
                                    <!-- Add state options -->
                                </select>
                                <input type="text" placeholder="ZIP Code" required>
                            </div>
                            <div class="checkbox-row">
                                <input type="checkbox" id="billing-address" checked>
                                <label for="billing-address">Use as billing address</label>
                            </div>
                            <button class="apply-button">Apply</button>
                        </form>
                    </div>
                    <div class="order-summary">
                        <h2>Order Summary</h2>
                        <div class="summary-item">
                            <p>Item Subtotal</p>
                            <p>$69.99</p>
                        </div>
                        <div class="summary-item">
                            <p>Shipping</p>
                            <p>FREE</p>
                        </div>
                        <div class="summary-item">
                            <p>Estimated Sales Tax</p>
                            <p>$5.77</p>
                        </div>
                        <div class="total">
                            <h3>Total</h3>
                            <h3>$75.76</h3>
                        </div>
                        <p class="savings">You're saving $60 on your order today!</p>
                    </div>
                </div>
            </div>

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
