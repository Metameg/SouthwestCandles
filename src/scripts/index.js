import { handleCheckout } from './checkout';
import Cart  from './cart';
// import { addToCart, removeItem, openCartModal, closeCart, clearCart } from './cart';
import $ from 'jquery';

const cart = new Cart();
$(() => {
    // Set up the checkout button functionality
    $("#cartNavBtn").on("click", () => cart.openCartModal());
    $(".add-to-cart").on("click", function () {
        cart.addItem(this);
    });
    $("#cartItems").on("click", ".remove-item", function() {
        const productId = $(this).data('id');
        cart.removeItem(productId); // You can call the removeItem function or handle logic here
    });
    $("#closeCart").on("click", () => cart.closeCart());
    $("#clearCart").on("click", () => cart.clearCart());
});