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

    extract($args);
    // Email parameters
    // $to = 'admin@thedomaindesigners.com'; 
    $subject = 'New Candle Order Submission';
    $to = 'metameg8@gmail.com';
    $from = 'Carrie\'s Candles';

    // Compose email message
    $body = "New Order: \n\n"; 
    $body .= "Payment Intent: \n$payment_intent_id\n";
    $body .= "\nUser Email: \n$user_email\n";
    $body .= "\Amount:\n$amount\n";
    $body .= "\nLine Items:\n$line_items\n";
    $body .= "\n\nAddress:\n";
    $body .= "\nAddress Line 1:\n$address1\n";
    $body .= "\nAddress Line 2:\n$address2\n";
    $body .= "\nCity:\n$city\n";
    $body .= "\nState:\n$state\n";
    $body .= "\nZip Code:\n$zip\n";
    $body .= "\nCountry:\n$country\n";
    $body .= "\n\Shipping Option:\n";
    $body .= "\n$shipping_option\n";
    

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'metameg8@gmail.com'; 
        // $mail->Username = 'metameg8@gmail.com'; 
        $mail->Password = $_ENV['MAIL_APP_PASSWORD']; 
        // $mail->Password = $_ENV['DEVMAIL_APP_PASSWORD']; 
        // $mail->SMTPSecure = 'ssl';
        $mail->SMTPSecure = 'tls';
        // $mail->Port = 465;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('metameg8@gmail.com', $from);
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

?>