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
        <?php $basePath = '.';?>
        <?php include $basePath . '/components/navbar.php';?>
      

        <!-- Fetch all products -->
        <?php
            // Include the database connection
            include '../db.php';

            $pdo = getPDO();
            // Fetch products from the database
            $stmt = $pdo->query("SELECT * FROM products");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <h1><span class="title-offcolor">SOUTHWEST</span> Candles</h1>
                    <p class="cta-1">Limited Edition Seasonals</p>
                    <p class="cta-2">Five Featured Seasonals</p>
                    <p class="cta-3">ALL YEAR 'ROUND</p>
                    <a href="#" class="shop-now-btn">Shop Now</a>
                </div>
            </section>

            <section class="">
                
            </section>

            <section class="products-section">
                <?php foreach ($products as $product): ?>
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
        </main>
    </body>
</html>
