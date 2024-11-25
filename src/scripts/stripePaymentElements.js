import { loadStripe } from '@stripe/stripe-js';

(function () {
    const STRIPE_PUBLIC_KEY = 'pk_test_51QHonuBzPFeYbIr4JLEEpJQbGR7P0WFb9dJH4HhjdXyq2DJqIh6I3TaWZLQ49ffz0VYtDupbBgByT6SVsYZpo7Rw00jwB23EOF'; // Replace with your Stripe public key
    const paymentElement = document.getElementById('paymentOptions');
    const addressElement = document.getElementById('addressElement');
    const btn = document.getElementById('payNow');
    let stripe;
    let elements;

    async function load() {
        
        if (!paymentElement || !stripeClientSecret) {
            console.error('Payment options or client secret is missing.');
            return;
        }

        // Fetch the payment intent from the server
        // const rprom = fetch('/plugins/payments/payment_intent', {
        //     method: 'POST',
        //     headers: {
        //         'Content-Type': 'application/json',
        //     },
        //     body: JSON.stringify({
        //         amount: totalPrice * 100, // Amount in cents
        //     }),
        // });

        // Load the Stripe library
        stripe = await loadStripe(STRIPE_PUBLIC_KEY);

        // const res = await rprom;
        // const data = await res.json();

        // Initialize Stripe Elements with the client secret
        elements = stripe.elements({
            clientSecret: stripeClientSecret,
            loader: 'auto',
        });

        const s_payEl = elements.create('payment', {
            layout: 'tabs',
        });

        s_payEl.mount(paymentElement);

        const s_addressEl = elements.create("address", {
            mode: "shipping",
        });
        s_addressEl.mount(addressElement);
    }

    // Handle the payment submission
    btn.addEventListener('click', async () => {
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
    });

    // Initialize the payment flow
    load();
})();
