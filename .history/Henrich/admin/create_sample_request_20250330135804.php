<?php
require_once '../includes/config.php';

try {
    // Get a sample user ID
    $stmt = $GLOBALS['pdo']->query("SELECT user_id FROM users LIMIT 1");
    $userId = $stmt->fetchColumn();

    if (!$userId) {
        echo "No users found in the database. Please create a user first.";
        exit;
    }

    // Insert a sample request
    $stmt = $GLOBALS['pdo']->prepare("
        INSERT INTO requests (
            user_id, 
            request_type, 
            status, 
            description, 
            details
        ) VALUES (
            :user_id,
            'account',
            'pending',
            'Request for account information update',
            'User needs to update their contact information'
        )
    ");

    $stmt->execute([':user_id' => $userId]);
    echo "Sample request created successfully!";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} 