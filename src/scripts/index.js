import { handleCheckout } from './checkout';
// import './stripePaymentElements';
import cart  from './cart';
import $ from 'jquery';

// const cart = new Cart();
$(() => {
    // const hamburgerMenu = document.querySelector('.hamburger-menu');
    // const navMenu = document.querySelector('nav ul');

    $('#hamburgerMenu').on("click", () => {
        document.getElementById('hamburgerMenu').classList.toggle('open');
        document.querySelector('nav ul').classList.toggle('open');
    });
    // hamburgerMenu.addEventListener('click', () => {
    //     navMenu.classList.toggle('open');
    // });

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