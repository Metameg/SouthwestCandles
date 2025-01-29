<div id="cartOverlay" class="cart-overlay">
    <div id="cartPopover" class="cart-popover">
        <!-- Loading Spinner -->
        <div id="cartLoadingOverlay" class="loading-overlay">
            <div class="spinner"></div>
        </div>

        <button id="closePopover" class="close-btn">x</button>
        <h2>Your Shopping Cart</h2>
        <div id="cartItems" class="cart-items">
            <!-- Cart Items added dynamically in JS -->
        </div> 

        <div id="cartSummary" class="cart-summary">
            <span id="subtotal" class="subtotal-price">
                <!-- Subtotal calculated dynamically in JS -->
            </span>
        </div>
        
        <div class="cart-actions">
            <button id="closeCart" class="back-to-shop">← Add More</button>
            <button id="clearCart" class="clear-btn">Clear Cart</button>
            <button id="checkoutCart" class="checkout">Checkout →</button>
        </div>
        
    </div>
</div>