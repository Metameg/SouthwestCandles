<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Contact Us - Southwest Candles</title>
        <link rel="stylesheet" href="../css/contact.css">
        <link rel="stylesheet" href="../css/navbar.css">
        <link rel="stylesheet" href="../css/cart.css">
        <link rel="stylesheet" href="../css/footer.css">
        <script src="/dist/bundle.js"></script>
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
                    <p><a href="mailto:southwestcandles@yahoo.com">southwestcandles@yahoo.com</a></p>
                    
                    <h3>Home Office</h3>
                    <p>5347 Wolfpen Ridge Ln</p>
                    <p>Missouri City, TX, 77459</p>
                    
                    <!-- <h3>Wholesale Inquiries</h3>
                    <p><a href="mailto:southwestcandles@yahoo.com">southwestcandles@yahoo.com</a></p>
                    <p><a href="tel:5046060139">504-606-0139</a></p> -->
                </div>
            </div>
        </section>

        <?php include $basePath . '/components/footer.php';?>

    </body>
</html>