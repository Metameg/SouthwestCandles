<?php
// db.php
function getPDO() {
    require_once('../vendor/autoload.php');
    $dotenv_file_path = __DIR__ . '/../.env';
    if (file_exists($dotenv_file_path)) {
        $dotenv = Dotenv\Dotenv::createImmutable(dirname($dotenv_file_path));
        $dotenv->load();
    }

    $host = $_ENV['DB_HOST'];
    $db = $_ENV['DB'];
    $user = $_ENV['DB_USER'];
    $password = $_ENV['DB_PWD'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Could not connect to the database $db :" . $e->getMessage());
    }
}
?>
