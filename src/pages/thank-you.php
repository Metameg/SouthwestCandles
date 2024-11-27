<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You | Southwest Candle Products</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/cart.css">
    <link rel="stylesheet" href="../css/thank-you.css">
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


                <!-- </div>
                    <a href="/" class="button">Return to Home</a>
                </div> -->
        </main>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const urlParams = new URLSearchParams(window.location.search);
                const paymentIntent = urlParams.get('payment_intent');
                const redirectStatus = urlParams.get('redirect_status');

                const successContainer = document.querySelector('.success-container');
                const errorContainer = document.querySelector('.error-container');
                const successMessage = document.querySelector('.success-msg');
                const errorMessage = document.querySelector('.error-msg');

                if (redirectStatus === 'succeeded') {
                    successMessage.innerHTML = `Payment Success! <br><br>
                    We will send a receipt to the provided email shortly. <br>
                    Please keep this confirmation number for your records: <br><br>
                    <strong>${paymentIntent}</strong>`;

                    successContainer.style.display = 'flex';
                    successMessage.style.display = 'block';
                    errorContainer.style.display = 'none';
                    errorMessage.style.display = 'none';
                } else {
                    errorMessage.textContent = `Payment failed or requires further action. Please try again.`;
                    successContainer.style.display = 'none';
                    successMessage.style.display = 'none';
                    errorContainer.style.display = 'flex';
                    errorMessage.style.display = 'block';
                }
            });
        </script>
    </body>
</html>