import { loadStripe } from '@stripe/stripe-js';
import cart from './cart';

(function () {
    const STRIPE_PUBLIC_KEY = 'pk_test_51QHonuBzPFeYbIr4JLEEpJQbGR7P0WFb9dJH4HhjdXyq2DJqIh6I3TaWZLQ49ffz0VYtDupbBgByT6SVsYZpo7Rw00jwB23EOF'; // Replace with your Stripe public key
    const paymentElement = document.getElementById('paymentOptions');
    const addressElement = document.getElementById('addressElement');
    const email = document.getElementById('email');
    const pay_btn = document.getElementById('payNow');
    const backBtn = document.getElementById('continueShoppingLink');
    let stripe;
    let elements;

    async function load() {
        
        if (!paymentElement || !stripeClientSecret) {
            console.error('Payment options or client secret is missing.');
            return;
        }

        // Load the Stripe library
        stripe = await loadStripe(STRIPE_PUBLIC_KEY);

        // Initialize Stripe Elements with the client secret
        elements = stripe.elements({
            clientSecret: stripeClientSecret,
            loader: 'auto',
        });

        // Payment Element
        const s_payEl = elements.create('payment', {
            layout: 'tabs',
        });
        s_payEl.mount(paymentElement);

        // Address Element
        const s_addressEl = elements.create("address", {
            mode: "shipping",
        });
        s_addressEl.mount(addressElement);

        // When the address changes, calculate tax
        s_addressEl.on('change', (event) => {
            s_addressEl.getValue()
                .then(function(result) {
                    if (result.complete) {
                        // console.log("address complete");
                        const address = event.value.address;
                        triggerTaxCalculation(address);
                    }
                });
        });
    }

    // Handle the payment submission
    pay_btn.addEventListener('click', async () => {
        const loadingOverlay = document.getElementById('loadingOverlay');
        const success = document.querySelector('.success-msg');
        const error = document.querySelector('.error-msg');

        try {
            // Show the overlay
            loadingOverlay.style.display = 'flex';
    
            // Start the payment process
            const sResult = await stripe.confirmPayment({
                elements,
                redirect: 'if_required',
                confirmParams: {
                    return_url: 'http://localhost:3000/src/pages/thank-you.php', 
                },
            });
    
            // Hide the overlay
            loadingOverlay.style.display = 'none';
    
            // Handle payment result error
            if (sResult.error) {
                if (shouldRedirect(sResult.paymentIntent)) {
                    window.location.href = `http://localhost:3000/src/pages/thank-you.php?success=false&error=${encodeURIComponent(sResult.error.message)}`;
                } else {
                    error.textContent = sResult.error.message;
                    error.style.display = "block";
                }
                return;
            }

            if (shouldRedirect(sResult.paymentIntent)) {
                // Redirect for successful payments
                window.location.href = `http://localhost:3000/src/pages/thank-you.php?success=true&paymentIntentId=${sResult.paymentIntent.id}`;
            } else {
                // Redirect for unsuccessful payments
                success.textContent += ` ${sResult.paymentIntent.id}`;
                success.style.display = "block";
                error.style.display = "none";
                cart.clearCart();
            }

            // Update email
            updateRecepientEmail(paymentIntentId, email.value);
            
            // Disable the pay button after payment is confirmed 
            // (button doesn't exist on thank-you page)
            if (pay_btn) {
                pay_btn.disabled = true;
                pay_btn.classList.add('disabled');
            }
            
        } catch (error) {
            // Hide the overlay in case of errors
            loadingOverlay.style.display = 'none';
            console.error('Error during payment:', error);
        }

    });

    // Update email address on the charge (for receipt purposes) 
    email.addEventListener('change', async (e) => {
        const email = e.target.value;
        // Validate email format
        if (!validateEmail(email)) {
            return;
        }
    });

    backBtn.addEventListener('click', async (e) => {
        cancelIntent(paymentIntentId);
    });


    async function updateRecepientEmail(paymentIntentId, email) {
         // Send the updated email to the backend
         try {
            const response = await fetch('../../plugins/payments/update_intent_email.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: email,
                    paymentIntentId: paymentIntentId
                 }),
            });

            const result = await response.json();

        } catch (error) {
            console.error('Error updating email:', error);
        }
    }

    async function triggerTaxCalculation(address) {
        if (!address) return;
    
        // Pass the address to your backend for tax calculation
        const response = await fetch('../../plugins/payments/calc_price.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                cart: cart.getCart(),
                address: address 
            }),
        });

        // Check if the HTTP request was successful
        if (!response.ok) {
            const error = await response.json();
            console.error('Network error calculating price:', error);
            return;
        }

        // Parse the JSON response
        const result = await response.json();

        // Check if the backend logic succeeded
        if (!result.success) {
            console.error('Error calculating price:', result.error);
            return;
        }

        updateCartSummary(result.estimated_tax, result.total_price);
    }

    async function cancelIntent(paymentIntentId) {
        const response = await fetch('../../plugins/payments/cancel_intent.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                paymentIntentId: paymentIntentId
             }),
        });

        if (response.ok) {
            const result = await response.json();
            console.log('Payment Intent canceled:', result);
        } else {
            const error = await response.json();
            console.error('Error canceling intent:', error);
        }
    }

    // Helper Functions
    function validateEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    function shouldRedirect(paymentIntent) {
        // Example: Redirect only for certain payment methods
        const requiresAction = paymentIntent?.status === 'requires_action';
    
        // Add logic for redirection criteria
        return requiresAction;
    }

    function updateCartSummary(tax, total) {
        document.getElementById('taxAmount').textContent = `$${(tax / 100).toFixed(2)}`;
        document.getElementById('totalAmount').textContent = `$${(total / 100).toFixed(2)}`;
    }


    // Initialize the payment flow
    load();
})();
