<?php
include '../../config/database.php';

try {
    // SQL to create settings table
    $sql = "CREATE TABLE IF NOT EXISTS settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_name VARCHAR(100) NOT NULL,
        setting_value TEXT,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    // Execute the query
    $conn->exec($sql);
    echo "Settings table created successfully";

} catch(PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}

// Close connection
$conn = null;
?>