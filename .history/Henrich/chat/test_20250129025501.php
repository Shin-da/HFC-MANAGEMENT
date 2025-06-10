<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

echo "<h2>Chat System Diagnostic Test</h2>";

function testDatabaseConnection() {
    try {
        require_once '../includes/config.php';
        global $pdo;
        
        if (!$pdo) {
            throw new Exception("PDO object is null");
        }
        
        $pdo->query("SELECT 1");
        echo "<p style='color:green'>✓ Database connection successful</p>";
        
        // Test users table
        $result = $pdo->query("SHOW TABLES LIKE 'users'")->fetch();
        if ($result) {
            echo "<p style='color:green'>✓ Users table exists</p>";
            
            // Show table structure
            echo "<h3>Users Table Structure:</h3>";
            echo "<pre>";
            $columns = $pdo->query("DESCRIBE users")->fetchAll();
            print_r($columns);
            echo "</pre>";
        } else {
            echo "<p style='color:red'>× Users table not found!</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color:red'>× Database Error: " . $e->getMessage() . "</p>";
        return false;
    }
    return true;
}

function testSession() {
    echo "<h3>Session Data:</h3>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    
    if (!isset($_SESSION['user_id'])) {
        echo "<p style='color:red'>× No user_id in session - Please log in first</p>";
        return false;
    }
    
    echo "<p style='color:green'>✓ Session contains user_id: " . $_SESSION['user_id'] . "</p>";
    return true;
}

function testRequiredFiles() {
    $requiredFiles = [
        '../includes/config.php',
        '../includes/session.php',
        '../includes/Page.php',
        '../assets/js/chat.js',
        '../assets/css/chat.css'
    ];
    
    foreach ($requiredFiles as $file) {
        if (file_exists($file)) {
            echo "<p style='color:green'>✓ File exists: $file</p>";
        } else {
            echo "<p style='color:red'>× Missing file: $file</p>";
        }
    }
}

echo "<div style='padding: 20px; font-family: Arial, sans-serif;'>";

echo "<h3>1. Testing Database Connection:</h3>";
$dbOk = testDatabaseConnection();

echo "<h3>2. Testing Session:</h3>";
$sessionOk = testSession();

echo "<h3>3. Checking Required Files:</h3>";
testRequiredFiles();

if ($dbOk && $sessionOk) {
    echo "<h3>4. Testing User Authentication:</h3>";
    try {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT user_id, username, role, is_online 
            FROM users 
            WHERE user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "<p style='color:green'>✓ User authenticated</p>";
            echo "<pre>";
            print_r($user);
            echo "</pre>";
        } else {
            echo "<p style='color:red'>× User not found in database</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red'>× Authentication test failed: " . $e->getMessage() . "</p>";
    }
}

echo "</div>";

// Display PHP Info for debugging
echo "<h3>PHP Environment Information:</h3>";
echo "<div style='font-size: 12px;'>";
phpinfo(INFO_VARIABLES | INFO_ENVIRONMENT);
echo "</div>";
