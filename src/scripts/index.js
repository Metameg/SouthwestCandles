import { handleCheckout } from './checkout';
import { addToCart, removeItem, closeCart } from './cart';
import $ from 'jquery';

$(() => {
    // Set up the checkout button functionality
    $(".add-to-cart").on("click", addToCart);
    $("#cartItems").on("click", ".remove-item", function() {
        const productId = $(this).data('id');
        console.log("Removing item with ID:", productId);
        removeItem(productId); // You can call the removeItem function or handle logic here
    });
    $("#closeCart").on("click", closeCart);
});