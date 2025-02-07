<?php
require_once('../../vendor/autoload.php');

$dotenv_file_path = __DIR__ . '/../../.env';
if (file_exists($dotenv_file_path)) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname($dotenv_file_path));
    $dotenv->load();
}
Stripe\Stripe::setApiKey($_ENV['STRIPE_SK_LIVE']);

function createPaymentIntent($amount, $line_items, $currency = 'USD') {

    try {
        // Create the PaymentIntent
        $paymentIntent = Stripe\PaymentIntent::create([
            'amount' => $amount * 100,
            'currency' => $currency,
            'automatic_payment_methods' => ['enabled' => true],
            'description' => 'Thanks for your purchase!',
            'receipt_email' => 'metameg8@gmail.com',
            'metadata' => [
                'line_items' => json_encode($line_items), // Add line_items to metadata
            ],
        ]);

        // Return the client secret
        return [
            'clientSecret' => $paymentIntent->client_secret,
            'paymentIntentId' => $paymentIntent->id
        ];
        
    } catch (\Stripe\Exception\ApiErrorException $e) {
        // Handle error
        return [
            'error' => $e->getMessage(),
        ];
    }
}

function updatePaymentIntent($paymentIntentId, $amount,  $tax_calculation_id, $shipping_option, $address, $currency = 'USD') {
    try {
        
        // Create the PaymentIntent
        $updatedPaymentIntent = Stripe\PaymentIntent::update(
            $paymentIntentId,  
            [
                'amount' => $amount,  
                'metadata' => [
                    'taxCalculationId' => $tax_calculation_id,  
                    'shippingOption' => $shipping_option,  
                    'address' => json_encode($address),  
                ],
            ]
        );
        
        
    } catch (\Stripe\Exception\ApiErrorException $e) {
        // Handle error
        return [
            'error' => $e->getMessage(),
        ];
    }
}

?>