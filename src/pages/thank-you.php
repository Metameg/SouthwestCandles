<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You | Southwest Candle Products</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/cart.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/thank-you.css">
    <script src="/dist/bundle.js"></script>
    </head>
    <body>

        <!-- Navbar -->
        <?php $basePath = '../';?>
        <?php include $basePath . '/components/navbar.php';?>


        <!-- Cart -->
        <?php include $basePath . '/components/cart.php';?>
        
        <main>
            <div class="success-container">
                <h1 id="thankYouHeader">Thank You!</h1>
                <div class="success-msg">
                    Payment Success! We will send a receipt to the provided email shortly. 
                    Please keep this confirmation number for your records. 
                    <br />
                    <!-- Confirmation will be created dynamically in script -->      
                </div>
            </div>

            <div class="error-container">
                <h1 id="errorHeader">Oops!</h1>
                <div class="error-msg">
                    <!-- Error will be sent from script -->
                </div>
            </div>
            
            <?php include $basePath . '/components/footer.php';?>
        </main>
    </body>
</html>