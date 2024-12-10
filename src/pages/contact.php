<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Contact Us - The Candle Co.</title>
        <link rel="stylesheet" href="../css/contact.css">
        <link rel="stylesheet" href="../css/navbar.css">
        <link rel="stylesheet" href="../css/cart.css">
        <link rel="stylesheet" href="../css/footer.css">
    </head>
    <body>

         <!-- Navbar -->
         <?php $basePath = '..';?>
        <?php include $basePath . '/components/navbar.php';?>

        <!-- Cart -->
        <?php include $basePath . '/components/cart.php';?>

        <!-- Contact Section -->
        <section class="contact-section">
            <div class="container">
                <h1>CONTACT US</h1>
                
                <!-- Contact Info Box -->
                <div class="contact-info-box">
                    <p>For questions about any of our products, or help with placing your order, don't hesitate to contact us at:</p>
                    <h3>General Inquiries</h3>
                    <p>support@thecandleco.com</p>
                    
                    <h3>Corporate Office</h3>
                    <p>1234 Wax Lane</p>
                    <p>Candleville, CA 90210</p>
                    
                    <h3>Wholesale Inquiries</h3>
                    <p>wholesale@thecandleco.com</p>
                    <p>(555) 987-6543</p>
                </div>
            </div>
        </section>

        <?php include $basePath . '/components/footer.php';?>

    </body>
</html>