<!DOCTYPE html>

<?php
// Get the cart data from the POST request
$cartJson = $_POST['cart'] ?? '[]'; // Use an empty JSON array as default
$cart = json_decode($cartJson, true); // Decode the JSON into a PHP array

// Validate the cart
if (!is_array($cart)) {
    $cart = [];
}

include '../../plugins/payments/utilities.php';
include '../../plugins/payments/payment_intent.php';

$cart = consolidateCart($cart);
$subtotal = calcSubtotal($cart);

// Build Line Item info from cart
foreach ($cart as $item) {
    // Add the line item to the array
    $line_items[] = [
        'name' => $item['name'],
        'wickType' => $item['wickType'],
        'selectedSize' => $item['selectedSize'],
        'quantity' => $item['quantity']
    ];
}

if (is_string($subtotal)) {
    // Pass the error message to the HTML
    $errorMessage = $subtotal;
    echo $errorMessage;
} else {
    // Proceed with the checkout process
    $errorMessage = '';
}
$rprom = createPaymentIntent($subtotal, $line_items);

try {
    if (!isset($rprom['clientSecret']) || !isset($rprom['paymentIntentId'])) {
        throw new Exception("Missing required keys in the response.");
    }

    $clientSecret = $rprom['clientSecret'];
    $intentId = $rprom['paymentIntentId'];
} catch (Exception $e) {
    // Handle the exception (for testing purposes)
    // echo $e->getMessage();
}
?>

<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Southwest Candle Products</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/cart.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/checkout.css">
    <script src="/dist/bundle.js"></script>
    
    <script>
        // Pass the client secret to the frontend
        const stripeClientSecret = <?php echo json_encode($clientSecret); ?>;
        const paymentIntentId = <?php echo json_encode($intentId); ?>;
    </script>
    </head>
    <body>

        <!-- Loading Spinner -->
        <div id="loadingOverlay" class="loading-overlay">
            <div class="spinner"></div>
        </div>

        
        
        
        <!-- Navbar -->
        <?php $basePath = '../';?>
        <?php include $basePath . '/components/navbar.php';?> 
        
        
        <!-- Cart -->
        <?php include $basePath . '/components/cart.php';?>
        
        <main>
            <?php if (empty($errorMessage)): ?>
            

            <div class="checkout-page">
                <div class="checkout-container">

                    <!-- CART SUMMARY + CHECKOUT FORMS -->
                    <section class="shipping-summary">
                        <h2>Cart Summary</h2>
                        <div class="cart-summary">

                            <?php if (!empty($cart)): ?>
                                <?php foreach ($cart as $item): ?>
                                    <div class="item">
                                        <div class="item-details">
                                            <div class="item-header">
                                                <h3><?php echo htmlspecialchars($item['name']); ?> - <i><?php echo htmlspecialchars($item['description']); ?> </i></h3>
                                            </div>
                                            
                                            <div class="item-pricing-info">
                                                <p>Wick: <?php echo htmlspecialchars($item['wickType']); ?></p>
                                                <p>Size: <?php echo htmlspecialchars($item['selectedSize']); ?></p>
                                                <p>Qty: <?php echo htmlspecialchars($item['quantity']); ?></p>                                                
                                                <p><strong>$<?php echo htmlspecialchars(number_format($item['price'], 2)); ?></strong></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                            <?php else: ?>
                                <p>Your cart is empty or invalid.</p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="personal-info">
                            <h2>Personal Info</h2>
                            <input type="email" id="email" placeholder="Enter your email" />
                            <p id="emailErrorMsg" class="error-msg">Invalid Email</p>
                        </div>

                        <div  class="shipping-form">
                            
                            <h2>Shipping Address</h2>
                            <div id="addressElement">
                                <!-- Load Strip Payment Options from stripe.js api -->
                            </div>
                            
                            <h2>Payment Info</h2>
                            <div id="paymentOptions">
                                <!-- Load Strip Payment Options from stripe.js api -->
                            </div>
                            
                        </div>
                    </section>

                    <!-- SHIPPING OPTIONS -->
                    <section class="shipping-options">
                        <!-- Loading Spinner -->
                        <div id="shippingLoadingOverlay" class="shipping-loading-overlay">
                            <div class="spinner"></div>
                        </div>
                        <h2>Select Shipping</h2>
                        <div id="shipping-options-container" class="shipping-options-container">
                            <!-- Dynamically added in stripe JS -->
                        </div>
                    </section>

                    <section class="order-summary">
                        <!-- Loading Spinner -->
                        <div id="orderLoadingOverlay" class="order-loading-overlay">
                            <div class="spinner"></div>
                        </div>

                        <div class="order-details">
                            <h2>Order Summary</h2>
                            <div class="summary-item">
                                <p>Item(s) Subtotal</p>
                                <p>$<?php echo number_format($subtotal, 2) ?></p>
                            </div>
                            <div class="summary-item">
                                <p>Shipping</p>
                                <p id="shippingAmount">$-.--</p>
                            </div>
                            <div class="summary-item">
                                <p>Estimated Tax</p>
                                <p id="taxAmount">$0.00 
                                    <!-- Updated Dynamically in Stripe JS -->
                                </p>
                            </div>
                            <div class="total">
                                <h3>Total</h3>
                                <h3 id="totalAmount">$<?php echo number_format($subtotal, 2) ?>
                                    <!-- Updated Dynamically in Stripe JS -->
                                </h3>
                            </div>

                            <button id="payNow" class="paynow-button">Pay Now</button>
                            <div id="payErrorMsg" class="error-msg">
                                <!-- Error will be sent from script -->
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <?php endif; ?>
        </main>
        <?php include $basePath . '/components/footer.php';?>
        
    </body>
</html>
