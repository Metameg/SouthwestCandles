<?php
// Determine the relative path to index.php based on the current directory
function getIndexPath() {
    // Count the number of directory levels from the root (index.php) to the current file
    $depth = substr_count($_SERVER['PHP_SELF'], '/') - 1;

    // Create the relative path to index.php based on depth
    $path = str_repeat('../', $depth) . 'src/index.php';
    
    return $path;
}
?>

<header>
    <div class="logo">
        <a href="<?php echo getIndexPath(); ?>">
            <picture>
                <source srcset="assets/images/webp/logo.webp" type="image/webp">
                <img src="assets/images/logo.png" alt="Soutwest Candles Logo">
            </picture>
        </a>
    </div>
    <nav>
        <ul>
            <li><a href="#">Shop</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#" id="cartNavBtn">Cart</a></li>
        </ul>
    </nav>
</header>