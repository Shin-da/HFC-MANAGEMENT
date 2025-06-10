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