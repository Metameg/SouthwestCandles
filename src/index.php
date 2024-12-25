    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrie's Candle Products</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/cart.css">
    <link rel="stylesheet" href="css/footer.css">
    <script src="/dist/bundle.js"></script>
    </head>
    <body>

        <!-- Navbar -->
        <?php $basePath = '.';?>
        <?php include $basePath . '/components/navbar.php';?>

        <!-- Fetch all products -->
        <?php
            // Include the database connection
            include '../db.php';

            try {
                $pdo = getPDO();
                // Fetch products from the database
                $stmt = $pdo->query("SELECT * FROM products");
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Fetch featured products
                $stmt = $pdo->query("SELECT * FROM products WHERE featured = 1");
                $featuredProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                // Display the error message
                echo "Error: " . $e->getMessage();
                // Optionally, display the stack trace for more debugging details
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
            }
        ?>



        <!-- Cart -->
        <?php include $basePath . '/components/cart.php';?>
        
        <main>
            <section class="hero">
                <div class="hero-overlay"></div>
                <!-- <div class="hero-image">
                    <picture>
                        <source srcset="assets/images/webp/pink-candle-symmetric.webp" type="image/webp">
                        <source srcset="assets/images/pink-candle-symmetric.jpg" type="image/jpeg">
                        <img src="assets/images/pink-candle-symmetric.jpg" alt="Product candles displayed on table">
                    </picture>
                </div> -->
                <div class="hero-info">
                    <h1><span class="title-offcolor">CARRIE'S</span> Candles</h1>
                    <p class="cta-1">A Wick Above the Rest</p>
                    <p class="cta-2">Five Featured Seasonals</p>
                    <p class="cta-3">ALL YEAR 'ROUND</p>
                    <a href="./pages/products.php" class="primary-btn">Shop Now</a>
                </div>
            </section>

            <section class="about-section">
                <h2 class="about-title">Carrie's Promise</h2>
                <div class="about-content">
                    <div class="about-item">
                        <h3>Handcrafted with Love</h3>
                        <p>Every candle is lovingly handcrafted to bring warmth, light, and beautiful scents into your home.</p>
                    </div>
                    <div class="about-item">
                        <h3>Home Manufactured</h3>
                        <p>Carrie's home collection is designed, developed, produced, tested, packed, and shipped by Carrie herself in her shop located in Houston, TX </p>
                    </div>
                    <div class="about-item">
                        <h3>Affordability is #1 Concern</h3>
                        <p>With candles ranging from $9 to $23, Our goal is to provide luxury-quality candles at prices that are accessible for everyone.</p>
                    </div>
                </div>
                <div class="button-container">
                    <a href="./pages/contact.php" class="primary-btn">Contact</a>
                </div>
            </section>

            <h2 class="section-header">Featured Products</h2>
            <section class="featured-products">
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="product">
                        <div class="product-image">
                            <!-- <picture> -->
                                <!-- <source srcset="assets/images/webp/candle_glow.webp" type="image/webp"> -->
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Placeholder candle product image">
                            <!-- </picture> -->
                            <span class="tag <?php echo htmlspecialchars($product['tag']); ?>"><?php echo htmlspecialchars($product['tag']); ?></span>
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p><i>From  </i> <span class="price">$8.00</span></p>
                            <button class="add-to-cart">Add To Cart</button>
                            <span class="item-id"><?php echo htmlspecialchars($product['id']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>

            <section class="promo-section">
                <div class="promo-image">
                    <picture>
                        <source srcset="assets/images/webp/tilted_on_table.webp" type="image/webp">
                        <source srcset="assets/images/tilted_on_table.jpg" type="image/jpeg">
                        <img src="assets/images/tilted_on_table.jpg" alt="Product candles displayed on table">
                    </picture>
                </div>
                <div class="promo-content">
                    <h3>New fragrances added.</h3>
                    <h1>New Year Sale.<br>Up to <span>10%<span> Off.</h1>
                    <a href="./pages/products.php" class="promo-link">Shop here</a>
                </div>
            </section>
            
            <?php include $basePath . '/components/footer.php';?>

        </main>
    </body>
</html>
