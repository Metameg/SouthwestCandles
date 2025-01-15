<?php

// Include the db.php file to use the getPDO function
require_once __DIR__ . '/../../db.php';

/**
 * Add a record to the transactions table.
 *
 * @param string $userId The ID of the user making the transaction.
 * @param float $amount The transaction amount.
 * @param string $status The status of the transaction (e.g., 'pending', 'completed').
 * @param string|null $description Optional description of the transaction.
 * @return bool True on success, false on failure.
 */
function addTransaction($payment_intent_id) {
    // Get the PDO instance
    $pdo = getPDO();

    $paymentIntent = \Stripe\PaymentIntent::retrieve($payment_intent_id);
    
    // SQL query to insert a new transaction
    $sql = "INSERT INTO transactions (user_id, amount, status, description, created_at) 
            VALUES (:user_id, :amount, :status, :description, NOW())";

    try {
        // Prepare the SQL statement
        $stmt = $pdo->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);

        // Execute the statement
        return $stmt->execute();
    } catch (PDOException $e) {
        // Log or handle the error as needed
        error_log("Error adding transaction: " . $e->getMessage());
        return false;
    }
}
?>
