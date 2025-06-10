<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Basic Connection Test</h1>";

// Test 1: PHP Version
echo "<h2>1. PHP Version</h2>";
echo "PHP Version: " . phpversion();

// Test 2: Database Connection
echo "<h2>2. Direct Database Test</h2>";
try {
    $dsn = "mysql:host=localhost;dbname=dbhenrichfoodcorps";
    $pdo = new PDO($dsn, "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>Database connection successful!</p>";
    
    // Test basic query
    $result = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<pre>Available tables:\n";
    print_r($result);
    echo "</pre>";
} catch (PDOException $e) {
    echo "<p style='color:red'>Connection failed: " . $e->getMessage() . "</p>";
    die();
}

// Test 3: Session
echo "<h2>3. Session Test</h2>";
session_start();
echo "<pre>Session data:\n";
print_r($_SESSION);
echo "</pre>";

// Test 4: File Permissions
echo "<h2>4. File Permission Test</h2>";
$testPaths = [
    '/includes/config.php',
    '/includes/session.php',
    '/api/users/online.php',
    '/chat/index.php'
];

foreach ($testPaths as $path) {
    $fullPath = __DIR__ . $path;
    echo "Testing $path: ";
    if (file_exists($fullPath)) {
        echo "Exists, Permissions: " . substr(sprintf('%o', fileperms($fullPath)), -4);
        echo ", Readable: " . (is_readable($fullPath) ? "Yes" : "No");
    } else {
        echo "File not found";
    }
    echo "<br>";
}
