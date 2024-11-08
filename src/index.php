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

        <!-- Cart -->
        <?php include $basePath . '/components/cart.php';?>

        <!-- Fetch all products -->
        <?php
            // Include the database connection
            include 'db.php';

            $pdo = getPDO();
            // Fetch products from the database
            $stmt = $pdo->query("SELECT * FROM products");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($products as $product):
                echo $product;
            endforeach
        ?>
        
        <main>
            <section class="hero">
                <div class="hero-image">
                <picture>
                    <source srcset="assets/images/webp/three_candle_lids.webp" type="image/webp">
                    <source srcset="assets/images/three_candle_lids.jpg" type="image/jpeg">
                    <img src="assets/images/candle_glow.jpg" alt="Product candles displayed on table">
                </picture>
                </div>
                <div class="hero-info">
                    <h1>Handmade Candle Scents</h1>
                    <p>At Southwest Candles, our hand-poured scents are like little trips in a jar. From desert sunsets to cozy mountain cabins, each candle is crafted to take you somewhere special—no passport needed. Just light up, relax, and let us handle the getaway!</p>
                </div>
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
                            <p><i>From  </i> <span class="price">$11.99</span></p>
                            <button class="add-to-cart">Add To Cart</button>
                        </div>
                    </div>
                <?php endforeach; ?>
                
            </section>
        </main>
    </body>
</html>
