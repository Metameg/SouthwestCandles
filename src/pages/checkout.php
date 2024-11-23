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
include '../plugins/payments/payment_intent.php';

$totalPrice = calcTotal($cart);
echo $totalPrice;
$rprom = createPaymentIntent($totalPrice);
if (isset($rprom['error'])) {
    // Handle the error case
    echo "Error creating PaymentIntent: " . $rprom['error'];
} elseif (isset($rprom['clientSecret'])) {
    // Access the client secret
    $clientSecret = $rprom['clientSecret'];
    echo "Client Secret: " . $clientSecret; // For demonstration (don't echo in production)
} else {
    // Handle unexpected cases
    echo "Unexpected response from createPaymentIntent.";
}
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
    
    <script>
        // Pass the client secret to the frontend
        const stripeClientSecret = <?php echo json_encode($rprom['clientSecret']); ?>;
    </script>
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
                    <div class="shipping-summary">
                        <h2>Shipping Details</h2>
                        <div class="cart-summary">

                            <?php if (!empty($cart)): ?>
                                <?php foreach ($cart as $item): ?>
                                    <div class="item">
                                        <img src="../<?php echo htmlspecialchars($item['image']); ?>" alt="Placeholder candle product image">
                                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                            <div class="size-dropdown">
                                                <label for="sizeDropdown">Size</label>
                                        
                                                <select id="sizeDropdown">
                                                    <option value="4oz" <?php echo ($item['selectedSize'] == '4oz') ? 'selected' : ''; ?>>
                                                        4oz
                                                    </option>
                                                    <option value="8oz" <?php echo ($item['selectedSize'] == '8oz') ? 'selected' :  ''; ?>>
                                                        8oz
                                                    </option>
                                                    <option value="16oz" <?php echo ($item['selectedSize'] == '16oz') ? 'selected' : ''; ?>>
                                                        16oz
                                                    </option>
                                                </select>
                                            </div>
                                            <p><strong>$<?php echo htmlspecialchars(number_format($item['price'], 2)); ?></strong></p>
                                        <!-- </div> -->
                                    </div>
                                <?php endforeach; ?>

                            <?php else: ?>
                                <p>Your cart is empty or invalid.</p>
                            <?php endif; ?>
                        </div>

                        <form action="" class="shipping-form">

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

                            <div id="paymentOptions">
                                <!-- Load Strip Payment Options from stripe.js api -->
                            </div>
                            <button id="payNow" class="apply-button">Apply</button>
                        </form>
                    </div>
                    <div class="order-summary">
                        <h2>Order Summary</h2>
                        <div class="summary-item">
                            <p>Item(s) Subtotal</p>
                            <p>$<?php echo number_format($totalPrice, 2) ?></p>
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


        </main>
        <script src="/dist/bundle.js"></script>
    </body>
</html>
