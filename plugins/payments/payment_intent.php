<?php
require_once('../../vendor/autoload.php');

use Stripe\Stripe;
use Stripe\PaymentIntent;

$dotenv_file_path = __DIR__ . '/../../.env';
if (file_exists($dotenv_file_path)) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname($dotenv_file_path));
    $dotenv->load();
}
Stripe::setApiKey($_ENV['STRIPE_SK_TEST']);

function createPaymentIntent($amount, $currency = 'USD') {

    try {
        // Create the PaymentIntent
        $paymentIntent = PaymentIntent::create([
            'amount' => $amount * 100,
            'currency' => $currency,
            'automatic_payment_methods' => ['enabled' => true],
            'description' => 'Thanks for your purchase!',
            'receipt_email' => 'metameg8@gmail.com',
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

function updatePaymentIntent($paymentIntentId, $amount,  $tax_calculation_id, $currency = 'USD') {

    try {
        // Create the PaymentIntent
        $updatedPaymentIntent = PaymentIntent::update(
            $paymentIntentId,  
            [
                'amount' => $amount,  
                'metadata' => [
                    'taxCalculationId' => $tax_calculation_id  
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