import { handleCheckout } from './checkout';
import { addToCart, removeItem, closeCart } from './cart';
import $ from 'jquery';

$(() => {
    // Set up the checkout button functionality
    $(".add-to-cart").on("click", addToCart);
    $(".remove-item").on("click", removeItem);
    $("#closeCart").on("click", closeCart);
});