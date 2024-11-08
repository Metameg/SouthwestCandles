import { loadStripe } from '@stripe/stripe-js';

export const stripePromise = loadStripe('pk_test_51QHonuBzPFeYbIr4JLEEpJQbGR7P0WFb9dJH4HhjdXyq2DJqIh6I3TaWZLQ49ffz0VYtDupbBgByT6SVsYZpo7Rw00jwB23EOF');

export async function handleCheckout() {
    // $('#checkout-button').on('click', async function () {
    const stripe = await stripePromise;
    const name = 'test_product';
    const amount = '100.00';
    console.log("click");

    fetch('plugins/payments/charge.php', {
        method: 'POST',
        body: JSON.stringify({
            name: name,
            amount: amount
        }),
        headers: {
            'Content-type': 'application/json; charset=UTF-8'
        }
    })
    .then(function(response) {
        // console.log(response.json());
        return response.json();
    })
    .then(function(session) {
        console.log(session);
        return stripe.redirectToCheckout({ sessionId: session.id });
    })
    .then(function(result) {
        if (result.error) {
        alert(result.error.message);
        }
    })
    .catch(function(error) {
        console.log('Fetch Error :-S', error);
    });
    // });
};