    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Southwest Candles Shop</title>
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
                <div class="hero-info">
                    <h1><span class="title-offcolor">SOUTHWEST</span> Candles</h1>
                    <p class="cta-1">A Wick Above the Rest</p>
                    <p class="cta-2">Five Featured Seasonals</p>
                    <p class="cta-3">ALL YEAR 'ROUND</p>
                    <a href="./pages/build-candle.php" class="primary-btn">Shop Now</a>
                </div>
            </section>

            <section class="about-section">
                <h2 class="about-title">Our Promise</h2>
                <div class="about-content">
                    <div class="about-item">
                        <h3>Handcrafted with Love</h3>
                        <p>Every candle is lovingly handcrafted to bring warmth, light, and beautiful scents into your home.</p>
                    </div>
                    <div class="about-item">
                        <h3>Home Manufactured</h3>
                        <p>Our home collection is designed, developed, produced, tested, packed, and shipped by the founder, Carrie, in her shop located in Houston, TX </p>
                    </div>
                    <div class="about-item">
                        <h3>Affordability is #1 Concern</h3>
                        <p>With candles ranging from $9 to $23, Our goal is to provide luxury-quality candles at prices that are accessible for everyone.</p>
                    </div>
                </div>
                <div class="button-container">
                    <a href="./pages/contact.php" class="primary-btn">Contact Us</a>
                </div>
            </section>

            
            <!-- Build A Candle Section -->
            <section class="build-candle-section">
                <h2 class="section-header">How to Build a Candle</h2>
                <div class="steps-container">
                    <div class="step">
                        <div class="step-number">1</div>
                        <h3>Select your Fragrance</h3>
                        <picture> 
                            <source srcset="assets/images/webp/fragrance-varieties.webp" type="image/webp">
                            <img src="assets/images/fragrance-varieties.png" alt="Several different fragrances">
                        </picture>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <h3>Pick your Wick</h3>
                        <picture> 
                            <source srcset="assets/images/webp/wick-varieties.webp" type="image/webp">
                            <img src="assets/images/wick-varieties.png" alt="Different wicks to choose from" class="wick-img">
                        </picture>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <h3>Choose a size</h3>
                        <picture> 
                            <source srcset="assets/images/webp/three_sizes.webp" type="image/webp">
                            <img src="assets/images/three_sizes.png" alt="Several different sizes to choose from">
                        </picture>
                    </div>
                </div>
                <a href="./pages/build-candle.php" class="primary-btn">Start building a candle</a>
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
                    <p>New fragrances added throughout the year</p>
                    <h2>Got any Ideas<br><span>Let Us Know!</span></h2>
                    <p>I'm always open to creating new fragrances and bringing my customer's ideas to life! 
                        Don't hesitate to contact me if you have any ideas!
                    </p>
                    <a href="./pages/contact.php" class="primary-btn">Contact Now</a>
                </div>
            </section>
            
            <?php include $basePath . '/components/footer.php';?>

        </main>
    </body>
</html>
