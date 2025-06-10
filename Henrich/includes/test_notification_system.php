<?php
require_once 'config.php';
require_once 'session.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h2>Testing Notification System</h2>";

// 1. Test Session
echo "<h3>1. Session Test</h3>";
echo "Session Data:<pre>";
print_r($_SESSION);
echo "</pre>";

// 2. Test Database Connection
echo "<h3>2. Database Connection Test</h3>";
if ($conn->ping()) {
    echo "Database connection is working<br>";
} else {
    echo "Database connection failed<br>";
}

// 3. Test Creating Notification
echo "<h3>3. Create Test Notification</h3>";
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $message = "Test notification - " . date('Y-m-d H:i:s');
    
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, is_read) VALUES (?, ?, 0)");
    $stmt->bind_param("is", $user_id, $message);
    
    if ($stmt->execute()) {
        echo "Test notification created successfully<br>";
        echo "Notification ID: " . $conn->insert_id . "<br>";
    } else {
        echo "Error creating notification: " . $stmt->error . "<br>";
    }
} else {
    echo "No user_id in session<br>";
}

// 4. Test Counting Notifications
echo "<h3>4. Count Notifications Test</h3>";
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    echo "Unread notifications count: " . $count . "<br>";
} else {
    echo "No user_id in session<br>";
}

// Add a button to refresh the page
echo "<br><button onclick='window.location.reload()'>Refresh Test</button>";
