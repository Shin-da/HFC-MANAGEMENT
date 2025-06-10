<?php
require_once '../includes/config.php';

try {
    // Check if requests table exists
    $stmt = $GLOBALS['pdo']->query("SHOW TABLES LIKE 'requests'");
    if ($stmt->rowCount() === 0) {
        echo "Requests table does not exist!";
        exit;
    }

    // Get total count of requests
    $stmt = $GLOBALS['pdo']->query("SELECT COUNT(*) FROM requests");
    $total = $stmt->fetchColumn();
    echo "Total number of requests: " . $total . "\n\n";

    // Get sample of requests
    $stmt = $GLOBALS['pdo']->query("
        SELECT r.*, u.username, u.useremail 
        FROM requests r 
        LEFT JOIN users u ON r.user_id = u.user_id 
        LIMIT 5
    ");
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($requests) > 0) {
        echo "Sample requests:\n";
        foreach ($requests as $request) {
            echo "ID: " . $request['request_id'] . "\n";
            echo "Type: " . $request['request_type'] . "\n";
            echo "User: " . $request['username'] . "\n";
            echo "Status: " . $request['status'] . "\n";
            echo "Description: " . $request['description'] . "\n";
            echo "Created: " . $request['created_at'] . "\n";
            echo "-------------------\n";
        }
    } else {
        echo "No requests found in the database.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} 