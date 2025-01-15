<?php

// Include the db.php file to use the getPDO function
require_once __DIR__ . '/../../db.php';
require_once('../../vendor/autoload.php');

$dotenv_file_path = __DIR__ . '/../../.env';
if (file_exists($dotenv_file_path)) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname($dotenv_file_path));
    $dotenv->load();
}

\Stripe\Stripe::setApiKey($_ENV['STRIPE_SK_TEST']);

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$payment_intent_id = $data['paymentIntentId'];

addTransaction($payment_intent_id);

function addTransaction($payment_intent_id) {
    /**
     * Add a record to the transactions table.
     *
     * @param string $userId The ID of the user making the transaction.
     * @param float $amount The transaction amount.
     * @param string $status The status of the transaction (e.g., 'pending', 'completed').
     * @param string|null $description Optional description of the transaction.
     * @return bool True on success, false on failure.
     */
    // Get the PDO instance
    $pdo = getPDO();

    $paymentIntent = \Stripe\PaymentIntent::retrieve($payment_intent_id);
    error_log("This is a message logged to the PHP error log." . $paymentIntent);
    // SQL query to insert a new transaction
    $sql = "INSERT INTO transactions (payment_intent_id, tax_calc_id, amount, status, line_items, created_at, shipping_option, address, latest_charge_id) 
            VALUES (:payment_intent_id, :tax_calc_id, :amount, :status, :line_items, NOW(), :shipping_option, :address, :latest_charge_id)";

    try {
        $payment_intent_id = $paymentIntent['id'];
        $tax_calc_id = $paymentIntent['metamdata']['taxCalculationId'];
        $amount = $paymentIntent['amount_received'];
        $status = 'completed';
        $line_items = $paymentIntent['metadata']['line_items'];
        $shipping_option = $paymentIntent['metadata']['shippingOption'];
        $address = $paymentIntent['metadata']['address'];
        $latest_charge_id = $paymentIntent['latest_charge'];
        // Prepare the SQL statement
        $stmt = $pdo->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(':payment_intent_id', $payment_intent_id, PDO::PARAM_STR);
        $stmt->bindParam(':tax_calc_id', $tax_calc_id, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':line_items', $line_items, PDO::PARAM_STR);
        $stmt->bindParam(':shipping_option', $shipping_option, PDO::PARAM_STR);
        $stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $stmt->bindParam(':latest_charge_id', $latest_charge_id, PDO::PARAM_STR);

        // Execute the statement
        return $stmt->execute();
    } catch (PDOException $e) {
        // Log or handle the error as needed
        error_log("Error adding transaction: " . $e->getMessage());
        return false;
    }
}


?>
