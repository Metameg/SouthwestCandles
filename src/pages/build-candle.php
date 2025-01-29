<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/build-candle.css">
        <link rel="stylesheet" href="../css/navbar.css">
        <link rel="stylesheet" href="../css/cart.css">
        <link rel="stylesheet" href="../css/footer.css">
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
            <div class="text-box">
                <h1 class="text-title">Handcrafted candles made for you!</h1>
                <p class="text-description">
                Create your perfect candle with our Build Your Own Candle experience! Start by selecting your favorite fragrance from a variety of rich, inviting scents. Next, choose the wick type that best suits your burn preference, whether it's a classic cotton wick or a long-lasting wooden wick. Then, pick the size that fits your space, from compact and cozy to large and luxurious. Once you've crafted your ideal combination, simply add it to your cart and complete your purchase. It's that easy to design a candle that's uniquely yours!
                </p>
            </div>
            
            <!-- FRAGERANCES STEP -->
            <div class="step">
                <div class="step-number">1</div>
                <div class="step-title">
                    <h3>Select your Fragrance</h3>
                </div>
            </div>
            
            <ul class="fragerance-list">

                <?php foreach ($products as $product): ?>
                    <li class="list-item fragerance-selector">
                            <input class="custom-radio" type="radio" name="fragranceType">
                            <div class="list-text">
                                <h4 class="name"><?php echo htmlspecialchars($product['name']); ?>
                                    <span class="tag <?php echo htmlspecialchars($product['tag']); ?>"><?php echo htmlspecialchars($product['tag']); ?></span>
                                </h4>
                                <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
                            </div>
                    </li>
                        
                <?php endforeach; ?>
            </ul>

            <!-- WICK STEP -->
            <div class="step">
                <div class="step-number">2</div>
                
                <div class="step-title">
                    <h3>Pick Your Wick</h3>
                </div>
            </div>

            <div class="selection">
                <div class="selection-card wick-selector">
                    <h4 class="card-name">Cotton</h4>
                    
                    <picture> 
                        <source srcset="../assets/images/webp/cotton-wick.webp" type="image/webp">
                        <img src="../assets/images/cotton-wick.png" alt="Cotton Wick" class="wick-img">
                    </picture>
                    <input class="custom-radio" type="radio" name="wickType" value="cotton">
                </div>

                <div class="selection-card wick-selector">
                    <h4 class="card-name">Wood</h4>
                    <picture> 
                        <source srcset="../assets/images/webp/wood-wick.webp" type="image/webp">
                        <img src="../assets/images/wood-wick.png" alt="Wooden Wick" class="wick-img">
                    </picture>
                    <input class="custom-radio" type="radio" name="wickType" value="wood">
                
                </div>
            </div>


            <!-- SIZE STEP -->
            <div class="step">
                <div class="step-number">3</div>
                
                <div class="step-title">
                    <h3>Select Size</h3>
                </div>
            </div>

            <div class="selection">
                <div id="size4oz" class="selection-card size-selector">
                    <h4 class="card-name">4oz - <span class="price">$9</span></h4>
                    <picture>
                        <source srcset="../assets/images/webp/4oz.webp" type="image/webp">
                        <img src="../assets/images/4oz.png" alt="4oz sized tin" class="card-image">
                    </picture>
                    <input class="custom-radio" type="radio" name="sizeType" value="4oz">
                    <p id="woodWickErrorMsg" class="wood-wick-error-msg">Wood wicks are not supported for this size.</p>
                </div>

                <div id="size8oz" class="selection-card size-selector">
                    <h4 class="card-name">8oz - <span class="price">$12</span></h4>
                    <picture>
                        <source srcset="../assets/images/webp/8oz.webp" type="image/webp">
                        <img src="../assets/images/8oz.png" alt="8oz sized tin" class="card-image size-8oz">
                    </picture>
                    <input class="custom-radio" type="radio" name="sizeType" value="8oz">
                
                </div>

                <div id="size16oz" class="selection-card size-selector">
                    <h4 class="card-name">16oz - <span class="price">$23</span></h4>
                    <picture>
                        <source srcset="../assets/images/webp/16oz.webp" type="image/webp">
                        <img src="../assets/images/16oz.png" alt="16oz sized tin" class="card-image">
                    </picture>
                    <input class="custom-radio" type="radio" name="sizeType" value="16oz">
                
                </div>
            </div>
        </section>

        <section class="product-summary">
            <h2>Product Summary</h2>
            <div class="product">
                <div class="product-info">
                    <h3 id="productSummaryName">--
                        <!-- Dynamic Field from JS -->
                    </h3>
                    <p id="productSummaryDesc" class="description">
                        <!-- Dynamic Field from JS -->
                    </p>
                    <p>Wick Type: <span id="productSummaryWick">
                        --<!-- Dynamic Field from JS -->
                    </span></p>
                    <p>Size: <span id="productSummarySize">
                        --<!-- Dynamic Field from JS -->
                    </span></p>
                    <p><i>Subtotal: </i> <span id="productSummaryPrice" class="price">
                        $--.--<!-- Dynamic Field from JS -->
                    </span></p>
                    <p class="error">You must select a fragrance, wick, and size.</p>
                    <button id="addBuildToCartBtn" class="add-to-cart" disabled>Add To Cart</button>
                </div>
            </div>
        </section>

        <section class="disclosure text-box">
            <h1 class="text-title">Disclosure</h1>
            <p class="text-description">
            Our candles are handcrafted using high-quality ingredients, but slight variations in color, size, or fragrance may occur. Always burn candles on a heat-resistant surface, trim wicks before use, and never leave them unattended. Individuals with allergies or sensitivities should review ingredients before purchase. Orders are final, but we will replace items damaged during shipping if contacted within 7 days. By purchasing, you agree to follow all safety guidelines and assume responsibility for proper use. For questions, contact us at <a href="mailto:southwestcandles@yahoo.com">southwestcandles@yahoo.com</a>. Thank you for supporting our small business!
            </p>
                </section>

        <?php include $basePath . '/components/footer.php';?>
    </body>
</html>