<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Chat System Test</h2>";

try {
    require_once '../includes/config.php';
    echo "✓ Config loaded<br>";
    
    require_once '../includes/session.php';
    echo "✓ Session loaded<br>";
    
    require_once '../includes/Page.php';
    echo "✓ Page class loaded<br>";
    
    // Test database connection
    $stmt = $pdo->query("SELECT 1");
    echo "✓ Database connection successful<br>";
    
    // Test session data
    echo "<h3>Session Data:</h3>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    
    // Test user authentication
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT username, role FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        echo "<h3>Current User:</h3>";
        echo "<pre>";
        print_r($user);
        echo "</pre>";
    } else {
        echo "× Not logged in<br>";
    }
    
} catch (Exception $e) {
    echo "<div style='color:red'>";
    echo "Error: " . $e->getMessage();
    echo "<br>File: " . $e->getFile();
    echo "<br>Line: " . $e->getLine();
    echo "</div>";
}
