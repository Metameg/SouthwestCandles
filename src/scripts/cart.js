import { loadStripe } from '@stripe/stripe-js';
import $ from 'jquery'

// Initialize cart if it doesn't exist
if (!localStorage.getItem('cart')) {
    localStorage.setItem('cart', JSON.stringify([])); // Empty cart array
}

export const stripePromise = loadStripe('pk_test_51QHonuBzPFeYbIr4JLEEpJQbGR7P0WFb9dJH4HhjdXyq2DJqIh6I3TaWZLQ49ffz0VYtDupbBgByT6SVsYZpo7Rw00jwB23EOF');

function getCart() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    return cart;
}

// Function to save the cart to localStorage
function saveCart(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
}

export function addToCart() {
    const productElement = $(this).closest(".product"); // Find the closest product element
    const productName = productElement.find("h3").text();
    const productPrice = parseFloat(productElement.find(".price").text().slice(1));
    const productImage = productElement.find("img").attr("src");

    const cart = getCart();
    
    // Check if the item already exists in the cart
    const existingItemIndex = cart.findIndex(item => item.name === productName);
    
    if (existingItemIndex >= 0) {
        // If item exists, increase the quantity or adjust the quantity if needed
        cart[existingItemIndex].quantity += 1;
    } else {
        // If it's a new item, add it to the cart
        cart.push({
            id: Date.now(),  // Using the current time as a unique ID
            name: productName,
            price: productPrice,
            image: productImage,
            quantity: 1
        });
    }

    saveCart(cart);
    updateCart();
    
    // Handle Display of popover
    $("#cartOverlay").css("display", "flex").fadeIn();
    $("#cartPopover").fadeIn();
};


export function updateCart() {
    const cart = getCart();
    const cartItemsList = $('#cartItems');
    cartItemsList.empty(); 

    if (cart.length === 0) {
        cartItemsList.innerHTML = '<li>Your cart is empty</li>';
    } else {
        cart.forEach(item => {
            const cartItem = $('<div></div>').addClass('cart-item');
            cartItem.html( `
                <img src="${item.image}" alt="${item.name}">
                <div class="item-details">
                    <h3>${item.name}</h3>
                    <span class="order-id">${item.id}</span>
                    <span class="color">Dark Blue</span>
                </div>
                <div class="quantity">
                    <button class="decrease">-</button>
                    <span class="count">${item.quantity}</span>
                    <button class="increase">+</button>
                </div>
                <span class="price">${item.price}</span>
                <button class="remove-item" data-id="${item.id}">x</button>
            `);
            cartItemsList.append(cartItem);
        });
    }
}


export function removeItem(productId) {
    let cart = getCart();
    
    // Remove the item from the cart array
    cart = cart.filter(item => item.id !== productId);
    
    // Save the updated cart back to localStorage
    saveCart(cart);
    
    // Update the cart display
    updateCart();  
}

export function clearCart() {
    localStorage.removeItem('cart');
    updateCart();
}

export function closeCart() {
    $("#cartPopover").fadeOut();
    $("#cartOverlay").fadeOut(() => {
        $("#cartOverlay").css("display", "none");
    })
};
