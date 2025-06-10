<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/config.php';

if ($conn && $conn->ping()) {
    echo "Database connection successful!";
    echo "<br>Server info: " . $conn->server_info;
} else {
    echo "Database connection failed!";
    echo "<br>Error: " . ($conn->connect_error ?? 'Unknown error');
}
