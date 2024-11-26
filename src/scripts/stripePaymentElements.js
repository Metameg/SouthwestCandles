import { loadStripe } from '@stripe/stripe-js';

(function () {
    const STRIPE_PUBLIC_KEY = 'pk_test_51QHonuBzPFeYbIr4JLEEpJQbGR7P0WFb9dJH4HhjdXyq2DJqIh6I3TaWZLQ49ffz0VYtDupbBgByT6SVsYZpo7Rw00jwB23EOF'; // Replace with your Stripe public key
    const paymentElement = document.getElementById('paymentOptions');
    const addressElement = document.getElementById('addressElement');
    const email = document.getElementById('email');
    const btn = document.getElementById('payNow');
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
    btn.addEventListener('click', async () => {
        console.log("paying...");
        const sResult = await stripe.confirmPayment({
            elements,
            redirect: 'if_required',
            confirmParams: {
                return_url: 'http://localhost:3000/src/index.php', // Replace with your return URL
            },
        });

        if (!!sResult.error) {
            alert(sResult.error.message);
            return;
        }

        const container = document.querySelector('.container');
        const success = document.querySelector('.success');

        if (container) {
            container.classList.add('hide');
        }

        if (success) {
            success.textContent += ` ${sResult.paymentIntent.id}`;
            success.classList.remove('hide');
        }
        updateRecepientEmail(paymentIntentId, email.value);

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
            console.log('thier');

            const result = await response.json();
            if (result.success) {
                alert('Email updated successfully!');
            } else {
                alert('Failed to update email: ' + result.error);
            }

        } catch (error) {
            console.error('Error updating email:', error);
            alert('An error occurred while updating the email.');
        }
    }
    // Initialize the payment flow
    load();
})();
