import { loadStripe } from '@stripe/stripe-js';
import cart from './cart';

(function () {
    const STRIPE_PUBLIC_KEY = 'pk_test_51QHonuBzPFeYbIr4JLEEpJQbGR7P0WFb9dJH4HhjdXyq2DJqIh6I3TaWZLQ49ffz0VYtDupbBgByT6SVsYZpo7Rw00jwB23EOF'; // Replace with your Stripe public key
    const paymentElement = document.getElementById('paymentOptions');
    const addressElement = document.getElementById('addressElement');
    const email = document.getElementById('email');
    const emailErrorMsg = document.getElementById('emailErrorMsg');
    const payBtn = document.getElementById('payNow');
    const backBtn = document.getElementById('continueShoppingLink');
    const shippingOptionsContainer = document.getElementById('shipping-options-container');
    const errorMsg = document.getElementById('payErrorMsg');
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
    payBtn.addEventListener('click', async () => {
        const loadingOverlay = document.getElementById('loadingOverlay');

        // Function to check if email is filled out
        if (!validateEmail(email.value)) {
            errorMsg.style.display = "block";
            errorMsg.textContent = "Fill out all required fields and select a shipping option."
            emailErrorMsg.style.display = "block";
            return;
        } else {
            errorMsg.style.display = "none";
            emailErrorMsg.style.display = "none";
        }

        // Function to check if a shipping option is selected
        if (!shippingOptionSelected()) {
            errorMsg.style.display = "block";
            errorMsg.textContent = "Fill out all required fields and select a shipping option."
            return;
        } else {
            errorMsg.style.display = "none";
        }

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
    
            // Unsuccessful Card Payments
            if (sResult.error) {
                // Hide the overlay
                loadingOverlay.style.display = 'none';
                errorMsg.textContent = sResult.error.message;
                errorMsg.style.display = "block";
                return;
            }
         

            // Other payment states
            if (sResult.paymentIntent.status === 'succeeded') {
                // add Transaction to db and email order
                await processTransaction(paymentIntentId, email.value);
                // Hide the overlay
                loadingOverlay.style.display = 'none';
                // updateRecepientEmail(paymentIntentId, email.value);
                window.location.href = `http://localhost:3000/src/pages/thank-you.php?success=true&paymentIntentId=${sResult.paymentIntent.id}`;;
            } else if (sResult.paymentIntent.status === 'requires_action') {
                // Handle further actions if required (e.g., Cash App validation)
            } else {
                window.location.href = `http://localhost:3000/src/pages/thank-you.php?success=false&error=${encodeURIComponent(sResult.error.message)}`;
            }

            
            
            
        } catch (error) {
            // Hide the overlay in case of errors
            loadingOverlay.style.display = 'none';
            errorMsg.style.display = "block";
            errorMsg.textContent = 'Something went wrong. Please try again.';
            console.error('Error during payment:', error);
        }

    });

    // Email address validation
    email.addEventListener('change', async () => {
        // Validate email format
        if (!validateEmail(email.value)) {
            email.classList.add('invalid');
            emailErrorMsg.style.display = 'block';
            return;
        }

        else {
            email.classList.remove('invalid');
            emailErrorMsg.style.display = 'none';
        }
    });

    backBtn.addEventListener('click', async (e) => {
        cancelIntent(paymentIntentId);
    });


    // async function updateRecepientEmail(paymentIntentId, email) {
    //      // Send the updated email to the backend
    //      try {
    //         const response = await fetch('../../plugins/payments/update_intent_email.php', {
    //             method: 'POST',
    //             headers: {
    //                 'Content-Type': 'application/json',
    //             },
    //             body: JSON.stringify({
    //                 email: email,
    //                 paymentIntentId: paymentIntentId
    //              }),
    //         });

    //         const result = await response.json();

    //     } catch (error) {
    //         errorMsg.style.display = "block";
    //         errorMsg.textContent = 'Something went wrong. Please try again.';
    //         console.error('Error updating email:', error);
    //     }
    // }

    async function triggerTaxCalculation(address) {
        if (!address) return;
        const shippingSpinner = document.getElementById('shippingLoadingOverlay');
        const orderSpinner = document.getElementById('orderLoadingOverlay');
        let shippingPrice = 0.00;

        // Fetch and Render the shipping options
        shippingSpinner.style.display = 'flex';
        orderSpinner.style.display = 'flex';
        const options = await fetchUSPSOptions(address);
        const pickupOption = {
            rates: [
                {
                    SKU: "PICKUP",
                    price: 0.00, // Replace with the desired price
                    mailClass: "PICKUP" // Replace with the desired mail class
                }
            ]
        };
        
        // Add the pickup option to the options array
        options.push(pickupOption);
        
        if (options) { 
            await fetchTaxCalculation(address, null);
            renderShippingOptions(options);
        }
        shippingSpinner.style.display = 'none';
        orderSpinner.style.display = 'none';

        // Dynamically change the shipping price based on selected shipping option
        const radios = document.querySelectorAll(`input[name="shippingOption"]`);
        radios.forEach((radio) => {
            radio.addEventListener('change', async () => {
                const selectedShippingOption = document.querySelector(`input[name="shippingOption"]:checked`);
                orderSpinner.style.display = 'flex';
                if (selectedShippingOption) {
                    // Traverse the DOM to find the associated price 
                    const priceEl = selectedShippingOption.closest('.shipping-card').querySelector('.price');
                    shippingPrice = parseFloat(priceEl.textContent.replace('$', ''));
                    const sku = selectedShippingOption.value;
                    await fetchTaxCalculation(address, sku);
                    orderSpinner.style.display = 'none';
                }

            });
        });
 
    }

    async function fetchTaxCalculation(address, sku) {
         // Pass the address to your backend for tax calculation
         const response = await fetch('../../plugins/payments/calc_price.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                cart: cart.getCart(),
                payment_intent_id: paymentIntentId, 
                address: address, 
                sku: sku 
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
            errorMsg.textContent = result.error;
            errorMsg.style.display = "block";
            payBtn.disabled = true;
            // console.error('Error calculating price:', result.error);
            return;
        } else {
            payBtn.disabled = false;
            errorMsg.style.display = 'none';
        }


        updateCartSummary(result.subtotal, result.estimated_tax, result.shipping_price, result.total_price);
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

    async function fetchUSPSOptions(address) {
        const response = await fetch('../../plugins/shipping/usps/get_rates.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                cart: cart.getCart(),
                address: address
            }),
        });

        
        if (response.ok) {
            const result = await response.json();
            if (result.error) {
                errorMsg.style.display = "block";
                errorMsg.textContent = result.error;
                payBtn.disabled = true;
                return;
            }
            payBtn.disabled = false;
            const rateResponse = result.rateResponse;
            const rateOptions = rateResponse.rateOptions;
            const options = extractUSPSOptions(rateOptions);
            errorMsg.style.display = 'none';
            return options;
            
        } else {
            const error = await response.json();
            errorMsg.style.display = "block";
            errorMsg.textContent = "Unable to determine shipping options. Please refresh the page and try again.";
            console.error('Error calculating shipping:', error);
            return;
        }
    }

    async function processTransaction(paymentIntentId, userEmail) {
        const response = await fetch('../../plugins/payments/process_transaction.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                paymentIntentId: paymentIntentId,
                userEmail: userEmail
            }),
        });

        
        if (response.ok) {
            const result = await response.json();
            
        } else {
            const error = await response.json();
            console.error('Error adding transaction:', error);
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
            "DPXX0XXXXR0",
            "DEXX0XXXXR0",
            "DUXP0XXXXR0",
            "DUXP0XXXUR0"
        ]
        let validOptions = [];

        rateOptions.forEach(opt => {
            const opt_sku = opt.rates[0].SKU.slice(0, -4);
            if (skus.includes(opt_sku)) {
                validOptions.push(opt);
            }
        });

        return validOptions;
    }


    // Function to render the shipping options dynamically
    function renderShippingOptions(options) {
        const deliveryTimes = {
            "DPXX0XXXXR0": "1-3 days",
            "DEXX0XXXXR0": "1-2 days guaranteed by 6:00pm",
            "DUXP0XXXXR0": "2-5 days",
            "DUXP0XXXUR0": "2-5 days",
            'PICKUP': 'If you are a member of Memorial Southwest Hospital, select this option'
        };

        shippingOptionsContainer.innerHTML = ""; // Clear existing content if any

        options.forEach(opt => {
            let sku = opt.rates[0].SKU;
            if (sku != 'PICKUP') {
                sku = opt.rates[0].SKU.slice(0,-4);
            }
            const deliveryTime = deliveryTimes[sku];
            const price = opt.rates[0].price;
            const name = opt.rates[0].mailClass
            .toLowerCase()     // Convert to lowercase to normalize
            .split('_')        // Split the string into words
            .map(word => word.charAt(0).toUpperCase() + word.slice(1)) // Capitalize each word
            .join(' ');   
            // Create the card elements dynamically
            const shippingCard = document.createElement('div');
            shippingCard.classList.add('shipping-card');

            const input = document.createElement('input');
            input.type = 'radio';
            input.id = `shipping-${sku}`;
            input.name = 'shippingOption';
            input.value = sku;

            const label = document.createElement('label');
            label.htmlFor = `shipping-${sku}`;

            const cardContent = document.createElement('div');
            cardContent.classList.add('card-content');

            const nameEl = document.createElement('h3');
            nameEl.textContent = name;

            const deliveryTimeEl = document.createElement('p');
            if (sku === 'PICKUP') {
                deliveryTimeEl.textContent = `${deliveryTime}`;
            } else {
                deliveryTimeEl.textContent = `Estimated delivery: ${deliveryTime}`;
            }

            const priceEl = document.createElement('p');
            priceEl.textContent = `$${price.toFixed(2)}`;
            priceEl.classList.add('price');

            // Append elements to construct the card
            cardContent.appendChild(nameEl);
            cardContent.appendChild(deliveryTimeEl);
            cardContent.appendChild(priceEl);
            label.appendChild(cardContent);
            shippingCard.appendChild(input);
            shippingCard.appendChild(label);
            shippingOptionsContainer.appendChild(shippingCard);
        });
    }

    // Function to check if a shipping option is selected
    function shippingOptionSelected() {
        // Get all shipping option radio inputs
        const shippingOptions = document.querySelectorAll('input[name="shippingOption"]');

        // Check if any of the options is selected
        const isSelected = Array.from(shippingOptions).some(option => option.checked);

        // Enable or disable the button based on the selection
        return isSelected;
    }

    function validateEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }


    function updateCartSummary(subtotal, tax, shipping, total) {
        document.getElementById('orderSummarySubtotal').textContent = `$${(subtotal / 100).toFixed(2)}`;
        document.getElementById('taxAmount').textContent = `$${(tax / 100).toFixed(2)}`;
        document.getElementById('shippingAmount').textContent = `$${(shipping / 100).toFixed(2)}`;
        document.getElementById('totalAmount').textContent = `$${(total / 100).toFixed(2)}`;
    }

    // Initialize the payment flow
    load();
})();
