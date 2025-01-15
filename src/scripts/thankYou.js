import cart from './cart';

(function () {
    const urlParams = new URLSearchParams(window.location.search);
    const paymentIntent = urlParams.get('payment_intent') ? urlParams.get('payment_intent') : urlParams.get('paymentIntentId');
    const redirectStatus = urlParams.get('redirect_status');
    const success = urlParams.get('success');
    const successContainer = document.querySelector('.success-container');
    const errorContainer = document.querySelector('.error-container');
    const successMessage = document.querySelector('.success-msg');
    const errorMessage = document.querySelector('.error-msg');

    if (redirectStatus === 'succeeded' || success == 'true') {
        successMessage.innerHTML = `Payment Success! <br><br>
        We will send a receipt to the provided email shortly. <br>
        Please keep this confirmation number for your records: <br><br>
        <strong>${paymentIntent}</strong>`;

        successContainer.style.display = 'flex';
        successMessage.style.display = 'block';
        errorContainer.style.display = 'none';
        errorMessage.style.display = 'none';
        cart.clearCart();

    } else {
        errorMessage.textContent = `Payment failed or requires further action. Please try again.`;
        successContainer.style.display = 'none';
        successMessage.style.display = 'none';
        errorContainer.style.display = 'flex';
        errorMessage.style.display = 'block';
    }
})();