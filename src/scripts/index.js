import cart  from './cart';
import $ from 'jquery';

$(() => {
    $('#hamburgerMenu').on("click", (event) => {
        event.stopPropagation();
        document.getElementById('hamburgerMenu').classList.toggle('open');
        document.querySelector('nav ul').classList.toggle('open');
        document.getElementById('dropdown').classList.remove('open');
    });
    
    $("#productsLink").on("click", (event) => {
        event.stopPropagation();
        document.getElementById('dropdown').classList.toggle('open');
    });

    // Close dropdown if clicking outside of it
    $(document).on("click", () => {
        const dropdown = document.getElementById('dropdown');
        const hamburger = document.getElementById('hamburgerMenu');
        if (dropdown.classList.contains('open')) {
            dropdown.classList.remove('open');
        }
        if (hamburger.classList.contains('open')) {
            hamburger.classList.remove('open');
            document.querySelector('nav ul').classList.toggle('open');
        }
    });
    
    // Set up the checkout button functionality
    $("#cartNavBtn").on("click", () => cart.openCartModal());
    $(".add-to-cart").on("click", function () {
        cart.addItem(this);
    });
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
    if (window.location.pathname.endsWith('thank-you.php')) {
        import('./thankYou').then(() => {
            console.log("Thank you module loaded successfully!");
        })
        .catch((err) => {
            console.error("Error loading thank you module:", err);
        });
    }
});