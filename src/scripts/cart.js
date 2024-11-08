import { loadStripe } from '@stripe/stripe-js';
import $ from 'jquery'

export const stripePromise = loadStripe('pk_test_51QHonuBzPFeYbIr4JLEEpJQbGR7P0WFb9dJH4HhjdXyq2DJqIh6I3TaWZLQ49ffz0VYtDupbBgByT6SVsYZpo7Rw00jwB23EOF');

export async function addToCart() {
    const productName = $(this).siblings("h3").text();
    updateCartPopover([productName]);
    // Add the item to the session (via AJAX)
    // $.post("add_to_cart.php", { name: productName}, function(data) {
    //     // Update the cart popover with the new cart contents
    //     updateCartPopover(data);
        
    //     // Show the cart popover
    //     $("#cart-popover").fadeIn();
    // });
    // fetch("plugins/payments/add_to_cart.php", {
    //     method: "POST",
    //     headers: {
    //         "Content-Type": "application/json"
    //     },
    //     body: JSON.stringify({ name: productName })
    // })
    // .then(response => response.json())
    // .then(data => {
    //     // Update the cart popover with the new cart contents
    //     updateCartPopover(data);
        
    //     // Show the cart popover
    //     $("#cart-popover").fadeIn();
    // })
    // .catch(error => console.error("Error:", error));

    $("#cart-popover").fadeIn();
};

export function closeCart() {
    $("#cart-popover").fadeOut();
};

export function updateCartPopover(cartItems) {
    $("#cart-items").empty();
    cartItems.forEach(function(item) {
        $("#cart-items").append(`<li>${item}</li>`);
        // $("#cart-items").append(`<li>${item.name}</li>`);
    });
}