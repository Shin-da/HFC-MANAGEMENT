<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('DB_HOST', 'localhost');
define('DB_NAME', 'dbhenrichfoodcorps');
define('DB_USER', 'root');
define('DB_PASS', '');

echo "<h1>Database Connection Test</h1>";

// Test 1: MySQLi Connection
echo "<h2>1. Testing MySQLi Connection</h2>";
try {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_error) {
        throw new Exception($mysqli->connect_error);
    }
    echo "<p style='color:green'>MySQLi connection successful!</p>";
    $mysqli->close();
} catch (Exception $e) {
    echo "<p style='color:red'>MySQLi connection failed: " . $e->getMessage() . "</p>";
}

// Test 2: PDO Connection
echo "<h2>2. Testing PDO Connection</h2>";
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    echo "<p style='color:green'>PDO connection successful!</p>";
    
    // Test query
    $stmt = $pdo->query("SELECT DATABASE()");
    echo "Connected to database: " . $stmt->fetchColumn();
} catch (PDOException $e) {
    echo "<p style='color:red'>PDO connection failed: " . $e->getMessage() . "</p>";
}

// Test 3: Check if users table exists and has correct structure
echo "<h2>3. Testing Users Table</h2>";
try {
    $sql = "DESCRIBE users";
    $result = $pdo->query($sql);
    $columns = $result->fetchAll();
    
    echo "<pre>";
    print_r($columns);
    echo "</pre>";
} catch (PDOException $e) {
    echo "<p style='color:red'>Error checking users table: " . $e->getMessage() . "</p>";
}

// Test 4: Check user count
echo "<h2>4. Testing User Count</h2>";
try {
    $sql = "SELECT COUNT(*) FROM users";
    $count = $pdo->query($sql)->fetchColumn();
    echo "Total users in database: " . $count;
} catch (PDOException $e) {
    echo "<p style='color:red'>Error counting users: " . $e->getMessage() . "</p>";
}
