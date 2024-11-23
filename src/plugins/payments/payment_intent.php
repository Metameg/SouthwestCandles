<?php
require_once('../../vendor/autoload.php');

use Stripe\Stripe;
use Stripe\PaymentIntent;

function createPaymentIntent($amount, $currency = 'USD') {
    $dotenv_file_path = __DIR__ . '/../../../.env';
    if (file_exists($dotenv_file_path)) {
        $dotenv = Dotenv\Dotenv::createImmutable(dirname($dotenv_file_path));
        $dotenv->load();
    }
    Stripe::setApiKey($_ENV['STRIPE_SK_TEST']);

    try {
        // Create the PaymentIntent
        $paymentIntent = PaymentIntent::create([
            'amount' => $amount * 100,
            'currency' => $currency,
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        // Return the client secret
        return [
            'clientSecret' => $paymentIntent->client_secret,
        ];
    } catch (\Stripe\Exception\ApiErrorException $e) {
        // Handle error
        return [
            'error' => $e->getMessage(),
        ];
    }
}


?>