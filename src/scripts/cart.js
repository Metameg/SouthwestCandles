import $ from 'jquery'
import { getRelativeRootPath } from './utils';

class Cart {
    constructor() {
        this.cart = this.getCart(); // Initialize cart from storage
        this.prices = {
            "4oz": 9.00,
            "8oz": 12.00,
            "16oz": 23.00,
            "default": 12.00
        }
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
                    <div class="order-details">
                        <div class="quantity">
                            <button class="decrease">-</button>
                            <span class="count">${item.quantity}</span>
                            <button class="increase">+</button>
                        </div>

                        <div class="size-btns">
                            <div class="size-option">
                                <input type="radio" id="sz4_${item.data_id}" name="${item.data_id}Radios" class="size-btn" value="4"  ${item.selectedSize === "4oz" ? "checked" : ""}>
                                <label for="sz4">4oz</label>
                                <div class="sz-price">$9</div>
                            </div>
                            <div class="size-option">
                                <input type="radio" id="sz8_${item.data_id}" name="${item.data_id}Radios" class="size-btn" value="8"  ${item.selectedSize === "8oz" ? "checked" : ""}>
                                <label for="sz8">8oz</label>
                                <div class="sz-price">$12</div>
                            </div>
                            <div class="size-option">
                                <input type="radio" id="sz16_${item.data_id}" name="${item.data_id}Radios" class="size-btn" value="16"  ${item.selectedSize === "16oz" ? "checked" : ""}>
                                <label for="sz16">16oz</label>
                                <div class="sz-price">$23</div>
                            </div> 
                        </div>
                    </div>
                    <span class="price"> $${(item.price * item.quantity).toFixed(2)}</span>
                    <button class="remove-item" data-id="${item.data_id}">X</button>
                `);
                cartItemsList.append(cartItem);

                // Add change event listener for size radio buttons
                cartItem.find(".size-btn").on("change", (e) => {
                    const selectedValue = $(e.target).val();
                    item.selectedSize = `${selectedValue}oz`;
                    item.price = this.prices[`${selectedValue}oz`];
                    this.saveCart(); 
                    this.updateCart();
                });

                // Add click event listener for increase button
                cartItem.find(".increase").on("click", () => {
                    item.quantity += 1; 
                    this.saveCart(); 
                    this.updateCart(); 
                });

                // Add click event listener for decrease button
                cartItem.find(".decrease").on("click", () => {
                    if (item.quantity > 1) { 
                        item.quantity -= 1; 
                        this.saveCart(); 
                        this.updateCart(); 
                    }
                });
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
        const productPrice = this.prices["default"]; 
        const productImage = productElement.find("img").attr("src");
        const productId = productElement.find('.item-id').text();
        const productTag = productElement.find('.tag').text();

        this.cart.push({
            id: productId,
            data_id: Date.now(),  // Generate a unique ID based on current time
            name: productName,
            price: productPrice,
            image: productImage,
            quantity: 1,
            selectedSize: "8oz",
            tag: productTag
        });
    
        // Save the updated cart to storage
        this.saveCart(this.cart);
        this.updateCart();

        // Open the cart modal or popover for user feedback
        this.openCartModal();
    }

    
    removeItem(productId) {
        this.cart = this.cart.filter(item => item.data_id !== productId);
        this.saveCart();
        this.updateCart();
    }

    openCartModal() {
        this.updateCart();
        $("#cartOverlay").css("display", "flex").fadeIn();
        $("#cartPopover").fadeIn();
    }

    async clearCart() {
        localStorage.removeItem('cart');
        this.cart = [];
        this.saveCart();
        this.updateCart();
    }

    closeCart() {
        $("#cartPopover").fadeOut();
        $("#cartOverlay").fadeOut(() => {
            $("#cartOverlay").css("display", "none");
        })
    }

    checkout() {
        const cart = this.getCart();

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = getRelativeRootPath('') + 'src/pages/checkout.php';
    
        // Create an input to hold the cart data as JSON
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'cart'; // This will be the key on the PHP side
        input.value = JSON.stringify(cart);
    
        // Append input to the form and submit
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

// Export the class to use in other modules
const cartInstance = new Cart();
export default cartInstance;
