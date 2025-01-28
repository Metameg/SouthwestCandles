import cart  from './cart';
import $ from 'jquery';

$(() => {
    cart.updateCartItemTicker();
    $('#hamburgerMenu').on("click", (event) => {
        event.stopPropagation();
        document.getElementById('hamburgerMenu').classList.toggle('open');
        document.querySelector('nav ul').classList.toggle('open');
    });

    // Close dropdown if clicking outside of it
    $(document).on("click", () => {
        
        const hamburger = document.getElementById('hamburgerMenu');
        if (hamburger.classList.contains('open')) {
            hamburger.classList.remove('open');
            document.querySelector('nav ul').classList.toggle('open');
        }
    });
    
    // Set up the checkout button functionality
    $("#cartNavBtn").on("click", () => cart.openCartModal());

    $("#cartItems").on("click", ".remove-item", function() {
        const productId = $(this).data('id');
        cart.removeItem(productId); // You can call the removeItem function or handle logic here
    });
    $("#checkoutCart").on("click", () => cart.checkout());
    $("#closeCart").on("click", () => cart.closeCart());
    $("#closePopover").on("click", () => cart.closeCart());
    $("#clearCart").on("click", () => cart.clearCart());

    // Only load correct modules
    if (window.location.pathname.endsWith('checkout.php')) {
        import('./stripePaymentElements')
    }
    if (window.location.pathname.endsWith('build-candle.php')) {
        import('./build-candle')
    }
    if (window.location.pathname.endsWith('thank-you.php')) {
        import('./thankYou')
    }
});