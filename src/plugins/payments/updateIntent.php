<?php
require_once('../../../vendor/autoload.php');

$dotenv_file_path = __DIR__ . '/../../../.env';
if (file_exists($dotenv_file_path)) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname($dotenv_file_path));
    $dotenv->load();
}

\Stripe\Stripe::setApiKey($_ENV['STRIPE_SK_TEST']);

header('Content-Type: application/json');

try {
    // Parse the incoming JSON request
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate the email
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email address.");
    }

    $email = $data['email'];
    $paymentIntentId = $data['paymentIntentId'];

    // Retrieve the PaymentIntent or Charge ID (update as per your logic)
    $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);


    // Update the charge with the new email
    // $charge = \Stripe\Charge::update($chargeId, [
    //     'receipt_email' => $email,
    // ]);
    $paymentIntent->receipt_email = $email;
    $paymentIntent->save();

    // Return success response
    echo json_encode(['success' => true, 'message' => 'Email updated successfully.']);
} catch (Exception $e) {
    // Handle errors
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
