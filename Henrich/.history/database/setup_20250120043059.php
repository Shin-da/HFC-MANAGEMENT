<?php
require_once '../includes/config.php';

// Read and execute SQL file
function executeSQLFile($filepath, $conn) {
    try {
        $sql = file_get_contents($filepath);
        if ($sql === false) {
            throw new Exception("Error reading SQL file");
        }

        // Split SQL commands by semicolon
        $commands = array_filter(
            array_map('trim', explode(';', $sql)),
            function($cmd) { return !empty($cmd); }
        );

        // Execute each command
        foreach ($commands as $command) {
            if (!$conn->query($command)) {
                throw new Exception("Error executing SQL: " . $conn->error);
            }
        }
        
        echo "Database tables created successfully!";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Execute the SQL file
executeSQLFile(__DIR__ . '/tables.sql', $conn);
?>
