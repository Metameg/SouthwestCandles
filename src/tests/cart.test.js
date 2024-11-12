import Cart from '../scripts/cart';
import $ from 'jquery';

// Mock localStorage
const localStorageMock = (() => {
    let store = {};
    return {
        getItem(key) {
            return store[key] || null;
        },
        setItem(key, value) {
            store[key] = value.toString();
        },
        clear() {
            store = {};
        },
        removeItem(key) {
            delete store[key];
        }
    };
})();

Object.defineProperty(window, 'localStorage', { value: localStorageMock });

// Mock jQuery DOM elements
document.body.innerHTML = `
    <div id="cartItems"></div>
    <div id="subtotal"></div>
    <div id="cartOverlay" style="display: none"></div>
    <div id="cartPopover" style="display: none"></div>
`;

describe('Cart', () => {
    let cart;

    beforeEach(() => {
        cart = new Cart();
        localStorage.clear();
        $('#cartItems').empty();
        $('#subtotal').text('');
    });

    test('should add an item to the cart', () => {
        
        const mockProduct = $('<div></div>').addClass('product');

        // Append the necessary elements to mockProduct to match the target DOM structure
        mockProduct.append(`
            <h3>Sample Product</h3>
            <span class="price">$10.00</span>
            <img src="sample.jpg" alt="Sample Product">
            <div class="item-details">
                <span class="order-id">12345</span>
                <span class="color">Dark Blue</span>
            </div>
            <div class="quantity">
                <button class="decrease">-</button>
                <span class="count">1</span>
                <button class="increase">+</button>
            </div>
            <button class="remove-item" data-id="12345">X</button>
        `);

        // Add the mock product to the document body to simulate a DOM environment
        document.body.appendChild(mockProduct[0]);

        cart.addItem(mockProduct.find('button')[0]);
        expect(cart.cart.length).toBe(1);
        expect(cart.cart[0].name).toBe('Sample Product');
        expect(cart.cart[0].price).toBe(10.00);
        expect(localStorage.getItem('cart')).not.toBe(null);
    });

    test('should calculate the subtotal correctly', () => {
        cart.cart = [
            { id: 1, name: 'Item 1', price: 10, quantity: 2 },
            { id: 2, name: 'Item 2', price: 20, quantity: 1 }
        ];
        const subtotal = cart.calculateSubtotal();
        expect(subtotal).toBe(40);
    });

    test('should remove an item from the cart', () => {
        cart.cart = [
            { id: 1, name: 'Item 1', price: 10, quantity: 1 },
            { id: 2, name: 'Item 2', price: 20, quantity: 1 }
        ];
        cart.removeItem(1);
        expect(cart.cart.length).toBe(1);
        expect(cart.cart[0].id).toBe(2);
    });

    test('should clear the cart and update the DOM', () => {
        cart.cart = [
            { id: 1, name: 'Item 1', price: 10, quantity: 1 },
            { id: 2, name: 'Item 2', price: 20, quantity: 1 }
        ];
        cart.clearCart();
        expect(cart.cart.length).toBe(0);
        expect(localStorage.getItem('cart')).toBe(null);
        expect($('#cartItems').html()).toBe('<p>Your cart is empty</p>');
        expect($('#subtotal').text()).toBe('Subtotal: $0.00');
    });

    test('should update the cart DOM correctly', () => {
        cart.cart = [
            { id: 1, name: 'Item 1', price: 10, image: 'sample.jpg', quantity: 1 },
            { id: 2, name: 'Item 2', price: 20, image: 'sample2.jpg', quantity: 2 }
        ];
        cart.updateCart();
        
        expect($('#cartItems').children().length).toBe(2);
        expect($('#subtotal').text()).toBe('Subtotal: $50.00');
    });

    test('should open the cart modal and set display styles', () => {
        cart.openCartModal();
        expect($('#cartOverlay').css('display')).toBe('flex');
        expect($('#cartPopover').css('display')).toBe('block');
    });

    test('should close the cart modal and reset display styles', () => {
        $('#cartPopover').css('display', 'block');
        $('#cartOverlay').css('display', 'flex');
        
        cart.closeCart();
        
        expect($('#cartOverlay').css('display')).toBe('none');
        expect($('#cartPopover').css('display')).toBe('none');
    });
});
