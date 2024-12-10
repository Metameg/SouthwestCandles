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

        <section class="products-section">
            <?php foreach ($products as $product): ?>
                <div class="product">
                    <h2 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h2>
                    <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
                    <img src="../product-pic.webp" alt="Deco Collection" class="product-image">
                    <button class="add-to-cart">Add To Cart</button>
                    <span class="item-id"><?php echo htmlspecialchars($product['id']); ?></span>
                </div>
                    
            <?php endforeach; ?>
            <!-- <div class="product-collection">
            <h3 class="product-title">Holiday Collection</h3>
            <a href="#" class="view-all">View All</a>
            <img src="../product-pic.webp" alt="Holiday Collection" class="product-image">
            </div>
            <div class="product-collection">
            <h3 class="product-title">Artisan Collection</h3>
            <a href="#" class="view-all">View All</a>
            <img src="../product-pic.webp" alt="Artisan Collection" class="product-image">
            </div>
            <div class="product-collection">
            <h3 class="product-title">Deco Collection</h3>
            <a href="#" class="view-all">View All</a>
            <img src="../product-pic.webp" alt="Deco Collection" class="product-image">
            </div>
            <div class="product-collection">
            <h3 class="product-title">28oz Black Label Collection</h3>
            <a href="#" class="view-all">View All</a>
            <img src="../product-pic.webp" alt="28oz Black Label Collection" class="product-image">
            </div>
            <div class="product-collection">
            <h3 class="product-title">Pearl Collection 300mg CBD</h3>
            <a href="#" class="view-all">View All</a>
            <img src="../product-pic.webp" alt="Pearl Collection 300mg CBD" class="product-image">
            </div>
            <div class="product-collection">
            <h3 class="product-title">Botanical Collection</h3>
            <a href="#" class="view-all">View All</a>
            <img src="../product-pic.webp" alt="Botanical Collection" class="product-image">
            </div> -->
        </section>

        <?php include $basePath . '/components/footer.php';?>
    </body>
</html>