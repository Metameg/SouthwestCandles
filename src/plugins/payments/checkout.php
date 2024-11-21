<!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Southwest Candle Products</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/cart.css">
    <script src="/dist/bundle.js"></script>
    </head>
    <body>

        <!-- Navbar -->
        <?php $basePath = '..';?>
        <?php include $basePath . '/components/navbar.php';?>
      

        <!-- Fetch all products -->
        <?php
            // Include the database connection
            include 'db.php';

            $pdo = getPDO();
            // Fetch products from the database
            $stmt = $pdo->query("SELECT * FROM products");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>


        <!-- Cart -->
        <?php include $basePath . '/components/cart.php';?>
        
        <main>
            
        </main>
    </body>
</html>
