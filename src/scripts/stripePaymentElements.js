import { loadStripe } from '@stripe/stripe-js';

(function () {
    const STRIPE_PUBLIC_KEY = 'pk_test_51QHonuBzPFeYbIr4JLEEpJQbGR7P0WFb9dJH4HhjdXyq2DJqIh6I3TaWZLQ49ffz0VYtDupbBgByT6SVsYZpo7Rw00jwB23EOF'; // Replace with your Stripe public key
    const paymentElement = document.getElementById('paymentOptions');
    const addressElement = document.getElementById('addressElement');
    const email = document.getElementById('email');
    const pay_btn = document.getElementById('payNow');
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

        // FIXME - address will be stored here. send it to order processor
        s_addressEl.on('change', (event) => {
            if (event.complete){
              // Extract potentially complete address
              const address = event.value.address;
            }
        })
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
                    return_url: 'http://localhost:3000/src/index.php', // Replace with your return URL
                },
            });
    
            // Hide the overlay
            loadingOverlay.style.display = 'none';
    
            // Handle payment result
            if (sResult.error) {
                error.textContent = sResult.error.message;
                error.style.display = "block";
                return;
            }
    
            // Update success message and email
            
            success.textContent += ` ${sResult.paymentIntent.id}`;
            success.style.display = "block";
            error.style.display = "none";
            updateRecepientEmail(paymentIntentId, email.value);
            
            // Disable the pay button after payment is confirmed
            pay_btn.disabled = true;
            pay_btn.classList.add('disabled');
            
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
            alert('Please enter a valid email address.');
            return;
        }
    });

    function validateEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    async function updateRecepientEmail(paymentIntentId, email) {
         // Send the updated email to the backend
         try {
            const response = await fetch('../../plugins/payments/updateIntent.php', {
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
    // Initialize the payment flow
    load();
})();
