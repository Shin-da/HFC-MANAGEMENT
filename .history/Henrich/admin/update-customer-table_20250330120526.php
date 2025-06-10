<?php
require_once '../includes/config.php';

try {
    // Add status column if it doesn't exist
    $sql = "ALTER TABLE customeraccount ADD COLUMN IF NOT EXISTS status ENUM('active', 'inactive') DEFAULT 'active' AFTER profilepicture";
    $GLOBALS['pdo']->exec($sql);
    
    // Update existing records to have 'active' status if status is NULL
    $sql = "UPDATE customeraccount SET status = 'active' WHERE status IS NULL";
    $GLOBALS['pdo']->exec($sql);
    
    echo "Customer table updated successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} 