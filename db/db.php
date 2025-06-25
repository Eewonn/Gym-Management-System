<?php
// Database configuration (open using phpMyAdmin)
$host = 'localhost';
$dbname = 'gym_management';
$username = 'root';
$password = '';

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
