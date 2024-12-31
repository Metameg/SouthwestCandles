<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/products.css">
        <link rel="stylesheet" href="../css/navbar.css">
        <link rel="stylesheet" href="../css/cart.css">
        <link rel="stylesheet" href="../css/footer.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
        <script src="/dist/bundle.js"></script>
        <title>Products Page</title>
    </head>
    <body>

        <!-- Navbar -->
        <?php $basePath = '..';?>
        <?php include $basePath . '/components/navbar.php';?>

        <!-- Fetch all products -->
        <?php
            // Include the database connection
            include '../../db.php';

            try {
                $pdo = getPDO();
                // Fetch products from the database
                $stmt = $pdo->query("SELECT * FROM products");
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Fetch featured products
                $stmt = $pdo->query("SELECT * FROM products");
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                // Display the error message
                echo "Error: " . $e->getMessage();
                // Optionally, display the stack trace for more debugging details
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
            }
        ?>

        <!-- Cart -->
        <?php include $basePath . '/components/cart.php';?>

        <section class="build-candle-section">
            <h2 class="section-title">Build Your Candle</h2>
            <div class="step">
                <div class="step-number">1</div>
                <div class="step-title">
                    <h3>Select your Fragrance</h3>
                </div>
            </div>
            
            <ul class="fragerance-list">
                <li class="list-item">
                    <div class="list-number">01</div>
                    <div class="list-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
                </li>

                <li class="list-item">
                    <div class="list-number">02</div>
                    <div class="list-text">Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</div>
                </li>

                <li class="list-item">
                    <div class="list-number">03</div>
                    <div class="list-text">Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
                </li>

                <li class="list-item">
                    <div class="list-number">04</div>
                    <div class="list-text">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</div>
                </li>
            </ul>
        
            <div class="step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h3>Pick your Wick</h3>
                  
                </div>
            </div>
        </section>

        <div class="text-box">
            <h1 class="text-title">Handcrafted candles made for you!</h1>
            <p class="text-description">
            Immerse yourself in the soothing ambiance of our handcrafted candle collection, 
            designed to elevate your home and well-being. Each candle is meticulously crafted 
            with premium ingredients, blending the calming essence of CBD with luxurious 
            fragrances that transport your senses. Whether you're seeking to unwind after 
            a long day, create a warm and inviting atmosphere, or find the perfect gift 
            for a loved one, our curated collections offer something for every occasion. 
            Experience the art of relaxation with candles that combine beauty, tranquility, 
            and a touch of indulgence.
            </p>
        </div>

        <!-- <section class="products-section">
            <?php foreach ($products as $product): ?>
                <div class="product">
                    <h2 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h2>
                    <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
                    <img src="../product-pic.webp" alt="Deco Collection" class="product-image">
                    <button class="add-to-cart">Add To Cart</button>
                    <span class="item-id"><?php echo htmlspecialchars($product['id']); ?></span>
                </div>
                    
            <?php endforeach; ?>
            
        </section> -->

        <?php include $basePath . '/components/footer.php';?>
    </body>
</html>