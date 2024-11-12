import { loadStripe } from '@stripe/stripe-js';
import $ from 'jquery'


// export const stripePromise = loadStripe('pk_test_51QHonuBzPFeYbIr4JLEEpJQbGR7P0WFb9dJH4HhjdXyq2DJqIh6I3TaWZLQ49ffz0VYtDupbBgByT6SVsYZpo7Rw00jwB23EOF');




class Cart {
    constructor() {
        this.cart = this.getCart(); // Initialize cart from storage
    }

    // Method to get the cart from localStorage or initialize it if empty
    getCart() {
        return JSON.parse(localStorage.getItem('cart')) || [];
    }

    // Method to save the cart back to localStorage
    saveCart() {
        localStorage.setItem('cart', JSON.stringify(this.cart));
    }

    // Method to update the cart display in the DOM
    updateCart() {
        const cartItemsList = $('#cartItems');
        cartItemsList.empty(); 

        if (this.cart.length === 0) {
            cartItemsList.html('<p>Your cart is empty</p>');
        } else {
            this.cart.forEach(item => {
                const cartItem = $('<div></div>').addClass('cart-item');
                cartItem.html(`
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
                    <button class="remove-item" data-id="${item.id}">X</button>
                `);
                cartItemsList.append(cartItem);
            });

        }
        // Calculate and display the subtotal
        const subtotal = this.calculateSubtotal();
        $('#subtotal').text(`Subtotal: $${subtotal.toFixed(2)}`);
    }

    // Method to calculate the subtotal of items in the cart
    calculateSubtotal() {
        return this.cart.reduce((subtotal, item) => subtotal + (item.price * item.quantity), 0);
    }

    // Method to add an item to the cart
    addItem(buttonElement) {
        // Find the closest product element
        const productElement = $(buttonElement).closest(".product");
        
        // Extract product details
        const productName = productElement.find("h3").text();
        const productPrice = parseFloat(productElement.find(".price").text().slice(1));
        const productImage = productElement.find("img").attr("src");

        // Check if the item already exists in the cart
        const existingItemIndex = this.cart.findIndex(item => item.name === productName);

        if (existingItemIndex >= 0) {
            // If item exists, increase the quantity
            this.cart[existingItemIndex].quantity += 1;
        } else {
            // If it's a new item, add it to the cart
            this.cart.push({
                id: Date.now(),  // Generate a unique ID based on current time
                name: productName,
                price: productPrice,
                image: productImage,
                quantity: 1
            });
        }

        // Save the updated cart to storage
        this.saveCart(this.cart);
        this.updateCart();

        // Open the cart modal or popover for user feedback
        this.openCartModal();
    }

    // Method to remove an item from the cart
    removeItem(productId) {
        this.cart = this.cart.filter(item => item.id !== productId);
        this.saveCart();
        this.updateCart();
    }

    openCartModal() {
        this.updateCart();
        $("#cartOverlay").css("display", "flex").fadeIn();
        $("#cartPopover").fadeIn();
    }

    clearCart() {
        localStorage.removeItem('cart');
        this.cart = [];
        this.updateCart();
    }

    closeCart() {
        $("#cartPopover").fadeOut();
        $("#cartOverlay").fadeOut(() => {
            $("#cartOverlay").css("display", "none");
        })
    }
}

// Export the class to use in other modules
export default Cart;
