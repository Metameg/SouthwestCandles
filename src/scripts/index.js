import { handleCheckout } from './checkout';
import { addToCart, closeCart } from './cart';
import $ from 'jquery';

$(() => {
    // Set up the checkout button functionality
    $(".add-to-cart").on("click", addToCart);
    $("#close-cart").on("click", closeCart);
});