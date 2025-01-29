<?php
// Determine the relative path to index.php based on the current directory
function getRootPath() {
    // Count the number of directory levels from the root (index.php) to the current file
    $depth = substr_count($_SERVER['PHP_SELF'], '/') - 1;

    // Create the relative path to index.php based on depth
    $path = str_repeat('../', $depth);
    
    return $path;
}

$currentFile = basename($_SERVER['PHP_SELF']);
$isCheckoutPage = ($currentFile === 'checkout.php');
?>

<header>
    <nav>
        
        <div class="logo">
            <a href="<?php echo getRootPath(); ?>src/index.php">
                <picture>
                    <source srcset="<?php echo getRootPath();?>src/assets/images/webp/logo.webp" type="image/webp">
                    <img src="<?php echo getRootPath();?>src/assets/images/logo.png" alt="Soutwest Candles Logo">
                </picture>
            </a>
        </div>
        <div class="nav-links">
            <div id="cartContainer">
                <?php if ($isCheckoutPage): ?>
                    <!-- Link to index.php if on checkout.php -->
                    <a id="continueShoppingLink" href="<?php echo getRootPath(); ?>src/pages/build-candle.php">&#8592; Continue Shopping</a>
                <?php else: ?>
                    <a id="cartNavBtn">
                        <img src="<?php echo getRootPath();?>src/assets/images/svg/cart.svg" alt="Cart Icon" width="30" height="30">
                        <span id="cartItemCount"><!-- Created from JS --></span>
                    </a>
                    
                <?php endif; ?>
            </div>
            <ul>
                <li class="nav-link"><a href="<?php echo getRootPath(); ?>src/index.php">Home</a></li>
                <li id="buildCandleLink" class="nav-link"><a href="<?php echo getRootPath(); ?>src/pages/build-candle.php">Build a Candle</a></li>
                <li class="nav-link"><a href="<?php echo getRootPath(); ?>src/pages/contact.php">Contact</a></li>
                
            </ul>
            <div id="hamburgerMenu" class="hamburger-menu">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </nav>
</header>