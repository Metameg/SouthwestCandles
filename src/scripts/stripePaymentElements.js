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
    let s_payEl; 
    let s_addressEl; 

    async function load() {
        
        if (!paymentElement || !stripeClientSecret) {
            console.error('Payment options or client secret is missing.');
            return;
        }

        // Load the Stripe library
        stripe = await loadStripe(STRIPE_PUBLIC_KEY);

        // Appearance of Stripe Elements
        const appearance = {
            theme: 'stripe',
          
            variables: {
                colorPrimary: '#495aaa',
                colorBackground: '#faf8f3',
                colorText: '#30313d',
                colorDanger: '#df1b41',
                // fontFamily: 'Ideal Sans, system-ui, sans-serif',
                spacingUnit: '8px',
                borderRadius: '0',
            },

            rules: {
                '.Input': {
                    border: '1px solid #000',
                },
                '.Input:disabled': {
                    backgroundColor: '#000',
                },
          
                // See all supported class names and selector syntax below
            }
        };

        // Initialize Stripe Elements with the client secret
        elements = stripe.elements({
            clientSecret: stripeClientSecret,
            appearance: appearance,
            loader: 'auto',
        });

        // Payment Element
        s_payEl = elements.create('payment', {
            layout: 'tabs',
            
        });
        s_payEl.mount(paymentElement);

        // Address Element
        s_addressEl = elements.create("address", {
            mode: "shipping",
        });
        s_addressEl.mount(addressElement);

        // Check if Address is in Focus
        let isAddressInFocus = false;
        s_addressEl.on('focus', (event) => {
            isAddressInFocus = true;
        });

        s_addressEl.on('blur', (event) => {
            isAddressInFocus = false;
        
            // Check the address completeness after a short delay to account for user input
            
            if (!isAddressInFocus) {
                s_addressEl.getValue().then((result) => {
                    checkAndTriggerTaxCalculation(result.value);
                });
            }
            
        });

        // When the address changes, calculate tax
        s_addressEl.on('change', (event) => {
        
            // Check the address completeness after a short delay to account for user input
           
            if (!isAddressInFocus) {
                checkAndTriggerTaxCalculation(event.value);
            }
             
        });
    }

    // Handle the payment submission
    pay_btn.addEventListener('click', async () => {
        const loadingOverlay = document.getElementById('loadingOverlay');
        const error = document.getElementById('payErrorMsg');

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
    
            // Unsuccessful Card Payments
            if (sResult.error) {
                    error.textContent = sResult.error.message;
                    error.style.display = "block";
                return;
            }
         

            // Other payment states
            if (sResult.paymentIntent.status === 'succeeded') {
                window.location.href = `http://localhost:3000/src/pages/thank-you.php?success=true&paymentIntentId=${sResult.paymentIntent.id}`;;
            } else if (sResult.paymentIntent.status === 'requires_action') {
                // Handle further actions if required (e.g., Cash App validation)
            } else {
                window.location.href = `http://localhost:3000/src/pages/thank-you.php?success=false&error=${encodeURIComponent(sResult.error.message)}`;
            }

            // Update email
            updateRecepientEmail(paymentIntentId, email.value);
            
        } catch (error) {
            // Hide the overlay in case of errors
            loadingOverlay.style.display = 'none';
            console.error('Error during payment:', error);
        }

    });

    // Update email address on the charge (for receipt purposes) 
    email.addEventListener('change', async (e) => {
        const email = e.target.value;
        const error = document.getElementById('emailErrorMsg');
        // Validate email format
        if (!validateEmail(email)) {
            error.style.display = 'block';
            return;
        }

        else {
            error.style.display = 'none';
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

        // Check if the HTTP request was successful (For Debugging)
        // if (!response.ok) {
        //     const error = await response.json();
        //     console.error('Network error calculating price:', error);
        //     return;
        // }

        // Parse the JSON response
        const result = await response.json();

        // Check if the backend logic succeeded
        if (!result.success) {
            console.error('Error calculating price:', result.error);
            return;
        }
        const shippingTotal = calcShippingPrice();
        updateCartSummary(result.estimated_tax, shippingTotal, result.total_price);
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

        // For Debugging Response
        // if (response.ok) {
        //     const result = await response.json();
        //     console.log('Payment Intent canceled:', result);
        // } else {
        //     const error = await response.json();
        //     console.error('Error canceling intent:', error);
        // }
    }

    async function calcShippingPrice() {
        const response = await fetch('../../plugins/shipping/usps/get_rates.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                paymentIntentId: paymentIntentId
             }),
        });

        // For Debugging Response
        if (response.ok) {
            const result = await response.json();
            const rateResponse = JSON.parse(result.rateResponse);
            const rateOptions = rateResponse.rateOptions;
            const validOptions = extractUSPSOptions(rateOptions);

            console.log('Shipping Respose:', validOptions);
        } else {
            const error = await response.json();
            console.error('Error calculating shipping:', error);
        }
    }

    // Helper Functions
    function checkAndTriggerTaxCalculation(eventValue) {
        const address = eventValue.address;
        const requiredFields = ['line1', 'city', 'state', 'postal_code', 'country'];

        const isComplete = requiredFields.every((field) => {
            if (address['country'] !== 'US' && (field === 'state' || field === 'postal_code')) {
                return true; // Skip the check for 'state' and 'postal_code' for non-US addresses
            }
            return address[field] && address[field].trim() !== ''; // Check other fields
        });

        if (isComplete) {
            triggerTaxCalculation(address);
        }
    }

    function extractUSPSOptions(rateOptions) {
        const skus = [
            "DPXX0XXXXR05010",
            "DEXX0XXXXR05010",
            "DUXP0XXXXR05010"
        ]
        let validOptions = [];

        rateOptions.forEach(opt => {
            const opt_sku = opt.rates[0].SKU;
            console.log(opt_sku);
            if (skus.includes(opt_sku)) {
                validOptions.push(opt);
            }
        });

        return validOptions;
    }


    function validateEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }


    function updateCartSummary(tax, shipping, total) {
        document.getElementById('taxAmount').textContent = `$${(tax / 100).toFixed(2)}`;
        document.getElementById('shippingAmount').textContent = `$${(shipping / 100).toFixed(2)}`;
        document.getElementById('totalAmount').textContent = `$${(total / 100).toFixed(2)}`;
    }

    // Initialize the payment flow
    load();
})();
