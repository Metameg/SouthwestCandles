<?php

// Include the db.php file to use the getPDO function
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../mailer/send_order.php';
require_once('../../vendor/autoload.php');

$dotenv_file_path = __DIR__ . '/../../.env';
if (file_exists($dotenv_file_path)) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname($dotenv_file_path));
    $dotenv->load();
}

\Stripe\Stripe::setApiKey($_ENV['STRIPE_SK_TEST']);
// \Stripe\Stripe::setApiKey($_ENV['STRIPE_SK_LIVE']);

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$payment_intent_id = $data['paymentIntentId'];
$user_email = filter_var($data['userEmail'], FILTER_SANITIZE_EMAIL);
processTransaction($payment_intent_id, $user_email);

function processTransaction($payment_intent_id, $user_email) {
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
    // SQL query to insert a new transaction
    $sql = "INSERT INTO transactions (payment_intent_id, tax_calc_id, amount, line_items, created_at, user_email, shipping_option, 
    address1, address2, city, state, zip, country, latest_charge_id) 
            VALUES (:payment_intent_id, :tax_calc_id, :amount,
            :line_items, NOW(), :user_email, :shipping_option, :address1, :address2,
            :city, :state, :zip, :country, :latest_charge_id)";

    try {
        $args = [
            'payment_intent_id' => $paymentIntent['id'],
            'tax_calc_id' => $paymentIntent['metadata']['taxCalculationId'],
            'amount' => $paymentIntent['amount_received'] / 100,
            'line_items' => $paymentIntent['metadata']['line_items'],
            'user_email' => $user_email,
            'shipping_option' => $paymentIntent['metadata']['shippingOption'],
            'address1' => $paymentIntent['shipping']['address']['line1'],
            'address2' => $paymentIntent['shipping']['address']['line2'],
            'city' => $paymentIntent['shipping']['address']['city'],
            'state' => $paymentIntent['shipping']['address']['state'],
            'zip' => $paymentIntent['shipping']['address']['postal_code'],
            'country' => $paymentIntent['shipping']['address']['country'],
            'latest_charge_id' => $paymentIntent['latest_charge'],
        ];
        // Prepare the SQL statement
        $stmt = $pdo->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(':payment_intent_id', $args['payment_intent_id'], PDO::PARAM_STR);
        $stmt->bindParam(':tax_calc_id', $args['tax_calc_id'], PDO::PARAM_STR);
        $stmt->bindParam(':amount', $args['amount'], PDO::PARAM_STR);
        $stmt->bindParam(':line_items', $args['line_items'], PDO::PARAM_STR);
        $stmt->bindParam(':user_email', $args['user_email'], PDO::PARAM_STR);
        $stmt->bindParam(':shipping_option', $args['shipping_option'], PDO::PARAM_STR);
        $stmt->bindParam(':address1', $args['address1'], PDO::PARAM_STR);
        $stmt->bindParam(':address2', $args['address2'], PDO::PARAM_STR);
        $stmt->bindParam(':city', $args['city'], PDO::PARAM_STR);
        $stmt->bindParam(':state', $args['state'], PDO::PARAM_STR);
        $stmt->bindParam(':zip', $args['zip'], PDO::PARAM_STR);
        $stmt->bindParam(':country', $args['country'], PDO::PARAM_STR);
        $stmt->bindParam(':latest_charge_id', $args['latest_charge_id'], PDO::PARAM_STR);

        // Email order
        // \Stripe\PaymentIntent::update(
        //     $payment_intent_id,
        //     ['receipt_email' => $user_email]
        // );
        
        send_order($args);
        // Execute the statement and send to db
        $result = $stmt->execute();
        add_tax_calc($pdo, $args['tax_calc_id']);

        // Prepare response
        $response = [
            'success' => $result,  // True or false depending on the result
            'message' => $result ? 'Transaction added successfully' : 'Error adding transaction'
        ];

        // Send JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
        
    } catch (Exception $e) {
        // Log or handle the error as needed
        error_log("Error adding transaction: " . $e->getMessage());
        // Return a JSON response with the error
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error processing transaction',
            'error' => $e->getMessage()
        ]);
        exit;
    }
}

function add_tax_calc($pdo, $tax_calc_id) {
    $sql = "INSERT INTO taxes (tax_calc_id, amount_total, tax_amount, percentage, tax_type, tax_date) 
            VALUES (:tax_calc_id, :amount_total, :tax_amount, :percentage, :tax_type, :tax_date)";

    try {
        $taxCalc = \Stripe\Tax\Calculation::retrieve($tax_calc_id);
        $args = [
            'tax_calc_id' => $taxCalc['id'],
            'amount_total' => $taxCalc['amount_total'] / 100,
            'tax_amount' => $taxCalc['tax_amount_exclusive'] / 100,
            'percentage' => $taxCalc['tax_breakdown'][0]['tax_rate_details']['percentage_decimal'],
            'tax_type' => $taxCalc['tax_breakdown'][0]['tax_rate_details']['tax_type'],
            'tax_date' => $taxCalc['tax_date']
        ];
        // Prepare the SQL statement
        $stmt = $pdo->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(':tax_calc_id', $args['tax_calc_id'], PDO::PARAM_STR);
        $stmt->bindParam(':amount_total', $args['amount_total'], PDO::PARAM_STR);
        $stmt->bindParam(':tax_amount', $args['tax_amount'], PDO::PARAM_STR);
        $stmt->bindParam(':percentage', $args['percentage'], PDO::PARAM_STR);
        $stmt->bindParam(':tax_type', $args['tax_type'], PDO::PARAM_STR);
        $stmt->bindParam(':tax_date', $args['tax_date'], PDO::PARAM_STR);
        
        
        // Execute the statement and send to db
        $result = $stmt->execute();
        
    } catch (Exception $e) {
        // Log or handle the error as needed
        error_log("Error adding transaction: " . $e->getMessage());
        return false;
    }
}


?>
