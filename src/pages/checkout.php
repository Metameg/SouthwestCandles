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
$rprom = createPaymentIntent($subtotal);

try {
    if (!isset($rprom['clientSecret']) || !isset($rprom['paymentIntentId'])) {
        throw new Exception("Missing required keys in the response.");
    }

    $clientSecret = $rprom['clientSecret'];
    $intentId = $rprom['paymentIntentId'];
} catch (Exception $e) {
    // Handle the exception
    echo $e->getMessage();
}
?>

<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Southwest Candle Products</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/cart.css">
    <link rel="stylesheet" href="../css/checkout.css">
    
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

            <div class="checkout-page">
                <!-- <h1>Getting your order</h1> -->
                <input type="email" id="email" placeholder="Enter your email" />
                <div class="checkout-container">
                    <div class="shipping-summary">
                        <h2>Cart Summary</h2>
                        <div class="cart-summary">

                            <?php if (!empty($cart)): ?>
                                <?php foreach ($cart as $item): ?>
                                    <div class="item">
                                        <img src="../<?php echo htmlspecialchars($item['image']); ?>" alt="Placeholder candle product image">
                                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                            
                                            <p>Size<br /><br /><?php echo htmlspecialchars($item['selectedSize']); ?></p>

                                            <p>QTY:<br /><br /><?php echo htmlspecialchars($item['quantity']); ?></p>
                                            
                                            <p><strong>$<?php echo htmlspecialchars(number_format($item['price'], 2)); ?></strong></p>
                                        <!-- </div> -->
                                    </div>
                                <?php endforeach; ?>

                            <?php else: ?>
                                <p>Your cart is empty or invalid.</p>
                            <?php endif; ?>
                        </div>

                        <div  class="shipping-form">

                            <div id="addressElement">
                                <!-- Load Strip Payment Options from stripe.js api -->
                            </div>
                            <div id="paymentOptions">
                                <!-- Load Strip Payment Options from stripe.js api -->
                            </div>
                            
                        </div>
                    </div>
                    <div class="order-summary">
                        <div class="order-details">
                            <h2>Order Summary</h2>
                            <div class="summary-item">
                                <p>Item(s) Subtotal</p>
                                <p>$<?php echo number_format($subtotal, 2) ?></p>
                            </div>
                            <div class="summary-item">
                                <p>Shipping</p>
                                <p class="free-price">FREE</p>
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

                            <div class="success-msg">
                                Payment Success! We will send a receipt to the provided email shortly. 
                                Please keep this confirmation number for your records. 
                                <br />
                                <!-- Confirmation will be created dynamically in script -->
                                
                            </div>
                            <div class="error-msg">
                                <!-- Error will be sent from script -->
                            </div>
                        </div>
                    </div>

            </div>


        </main>
        <script src="/dist/bundle.js"></script>
    </body>
</html>
