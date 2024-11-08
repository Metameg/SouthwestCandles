<?php
// require_once('./php/init.php');
require_once('../../../vendor/autoload.php');

$dotenv_file_path = __DIR__ . '/../../../.env';
if (file_exists($dotenv_file_path)) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname($dotenv_file_path));
    $dotenv->load();
}
\Stripe\Stripe::setApiKey($_ENV['STRIPE_SK_TEST']);

$content = json_decode(file_get_contents('php://input'), true);

$name = $content['name'];
$amount = intval($content['amount']*100);
// echo 'NAMEs:' . $name;

$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
      'price_data' => [
        'currency' => 'usd',
        'product_data' => [
          'name' => $name,
        ],
        'unit_amount' => $amount,
      ],
      'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => 'https://example.com/success',
    'cancel_url' => 'https://example.com/cancel',
  ]);

echo json_encode($session);

?>