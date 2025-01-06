<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/products.css">
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
                    
                    <img src="../product-pic.webp" alt="Deco Collection" class="card-image">
                    <input class="custom-radio" type="radio" name="wickType">
                </div>

                <div class="selection-card wick-selector">
                    <h4 class="card-name">Wood</h4>
  

                    <img src="../product-pic.webp" alt="Deco Collection" class="card-image">
                    <input class="custom-radio" type="radio" name="wickType">
                
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
                <div class="selection-card size-selector">
                    <h4 class="card-name">4oz - <span class="price">$9</span></h4>
                    
                    <img src="../product-pic.webp" alt="Deco Collection" class="card-image">
                    <input class="custom-radio" type="radio" name="sizeType">
                
                </div>

                <div class="selection-card size-selector">
                    <h4 class="card-name">8oz - <span class="price">$12</span></h4>
                    <img src="../product-pic.webp" alt="Deco Collection" class="card-image">
                    <input class="custom-radio" type="radio" name="sizeType">
                
                </div>

                <div class="selection-card size-selector">
                    <h4 class="card-name">16oz - <span class="price">$23</span></h4>
                    
                    <img src="../product-pic.webp" alt="Deco Collection" class="card-image">
                    <input class="custom-radio" type="radio" name="sizeType">
                
                </div>
            </div>
        </section>

        <section class="product-summary">
            <h2>Product Summary</h2>
            <div class="product">
                <div class="product-info">
                    <h3 id="productSummaryName">
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
                    <button id="addBuildToCartBtn" class="add-to-cart">Add To Cart</button>
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

        <?php include $basePath . '/components/footer.php';?>
    </body>
</html>