<?php
require_once('../../vendor/autoload.php');

$dotenv_file_path = __DIR__ . '/../../.env';
if (file_exists($dotenv_file_path)) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname($dotenv_file_path));
    $dotenv->load();
}

\Stripe\Stripe::setApiKey($_ENV['STRIPE_SK_TEST']);

header('Content-Type: application/json');

try {
    // Parse incoming JSON request
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate the PaymentIntent ID
    if (empty($data['paymentIntentId'])) {
        throw new Exception("Missing PaymentIntent ID.");
    }

    $paymentIntentId = $data['paymentIntentId'];

    // Retrieve and cancel the PaymentIntent
    $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
    $canceledIntent = $paymentIntent->cancel();

    // Respond with the canceled PaymentIntent details
    echo json_encode([
        'success' => true,
        'message' => 'PaymentIntent cancelled successfully.',
        'canceledIntent' => $canceledIntent
    ]);
    
} catch (\Stripe\Exception\ApiErrorException $e) {
    // Handle Stripe-specific API errors
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    // Handle other errors
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}