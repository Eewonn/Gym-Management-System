<?php
// Database configuration for InfinityFree
$host = 'sql211.infinityfree.com';              // MySQL hostname from InfinityFree
$dbname = 'if0_39475486_flexhub_db';            // Your full database name
$username = 'if0_39475486';                     // Your InfinityFree MySQL username
$password = 'LjX9EcYmmIHTR7';                   // Your InfinityFree MySQL password

try {
    // Create PDO connection with explicit port
    $pdo = new PDO("mysql:host=$host;port=3306;dbname=$dbname", $username, $password);
    
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
