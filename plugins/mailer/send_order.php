<?php

/**

 * Plugin Name: Contact Form Plugin

 * Author: Metameg (Alex Metzger)

 * Version: 1.0.0

 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer.php';
require 'Exception.php';
require 'SMTP.php';
require_once  '../../vendor/autoload.php';
$dotenv_file_path = '../../.env';
if (file_exists($dotenv_file_path)) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname($dotenv_file_path));
    $dotenv->load();
}

function send_order($args) {

    // Email parameters
    // $to = 'admin@thedomaindesigners.com'; 
    $subject = 'New Candle Order!';
    $to = 'southwestcandles@yahoo.com';
    // $to = 'metameg8@gmail.com';
    $from = 'Southwest Candles';

    $body = generate_body($args);

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        // $mail->Host = 'smtp.gmail.com';
        $mail->Host = 'smtp.mail.yahoo.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'southwestcandles@yahoo.com'; 
        // $mail->Username = 'metameg8@gmail.com'; 
        $mail->Password = $_ENV['YAHOO_APP_PASSWORD']; 
        // $mail->Password = $_ENV['GMAIL_APP_PASSWORD']; 
        // $mail->Password = $_ENV['DEVMAIL_APP_PASSWORD']; 
        // $mail->SMTPSecure = 'ssl';
        $mail->SMTPSecure = 'tls';
        // $mail->Port = 465;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('southwestcandles@yahoo.com');
        // $mail->setFrom('metameg8@gmail.com', $from);
        $mail->addAddress($to);
        // $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        // Send email
        if($mail->send()){
            // echo "Thank you! Your message has been sent.";
            return "success";
        } else{
            // echo "Oops! Something went wrong. Please try again later. Error: {$mail->ErrorInfo}";
            return "Email not sent. Please try again";
        }
    
    } catch (Exception $e) {
        // echo "Oops! Something went wrong. Please try again later. Error: {$mail->ErrorInfo}";
    }
}

function generate_body($args) {
    
    extract($args);
    $line_items_array = json_decode($line_items, true);

    $body = "<h3>New Order Details</h3>";
    $body .= "<hr>";
    $body .= "<h4>Payment Information:</h4>";
    $body .= "<p><strong>Payment Intent ID:</strong> $payment_intent_id<br>";
    $body .= "<strong>Amount:</strong> $" . number_format($amount / 100, 2) . "</p>";

    $body .= "<h4>User Information:</h4>";
    $body .= "<p><strong>Email:</strong> $user_email</p>";

    // Line Items 
    $body .= "<h4>Line Items:</h4>";
    $body .= "<table border='1' cellspacing='0' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
    $body .= "<thead>
                <tr>
                    <th style='text-align: left;'>Name</th>
                    <th style='text-align: left;'>Wick Type</th>
                    <th style='text-align: left;'>Size</th>
                    <th style='text-align: right;'>Quantity</th>
                </tr>
            </thead>";
    $body .= "<tbody>";

    // Iterate through the array and add rows for each item
    foreach ($line_items_array as $item) {
        $body .= "<tr>
                    <td>{$item['name']}</td>
                    <td>{$item['wickType']}</td>
                    <td>{$item['selectedSize']}</td>
                    <td style='text-align: right;'>{$item['quantity']}</td>
                </tr>";
    }

    $body .= "</tbody>";
    $body .= "</table>";

    $body .= "<h4>Shipping Address:</h4>";
    $body .= "<p><strong>Address Line 1:</strong> $address1<br>";
    $body .= "<strong>Address Line 2:</strong> $address2<br>";
    $body .= "<strong>City:</strong> $city<br>";
    $body .= "<strong>State:</strong> $state<br>";
    $body .= "<strong>Zip Code:</strong> $zip<br>";
    $body .= "<strong>Country:</strong> $country</p>";

    $body .= "<h4>Shipping Option:</h4>";
    $body .= "<p>$shipping_option</p>";

    $body .= "<hr>";
    $body .= "<p>Thank you for your order!</p>";



    return $body;
}

?>