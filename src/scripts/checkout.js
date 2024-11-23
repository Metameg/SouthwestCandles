import { loadStripe } from '@stripe/stripe-js';



export async function handleCheckout() {
    const stripe = await loadStripe('pk_test_51QHonuBzPFeYbIr4JLEEpJQbGR7P0WFb9dJH4HhjdXyq2DJqIh6I3TaWZLQ49ffz0VYtDupbBgByT6SVsYZpo7Rw00jwB23EOF');;
    
    fetch('plugins/payments/checkout.php', {
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

};