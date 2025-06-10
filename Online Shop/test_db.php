<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once './database/dbconnect.php';

echo "<h2>Database Connection Test</h2>";

// Test database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Database connection successful!<br>";

// Test if database exists
$result = $conn->query("SHOW DATABASES LIKE 'dbHenrichFoodCorps'");
if ($result->num_rows > 0) {
    echo "Database 'dbHenrichFoodCorps' exists!<br>";
} else {
    echo "Database 'dbHenrichFoodCorps' does not exist!<br>";
}

// Test if products table exists
$result = $conn->query("SHOW TABLES LIKE 'products'");
if ($result->num_rows > 0) {
    echo "Table 'products' exists!<br>";
    
    // Show table structure
    $result = $conn->query("DESCRIBE products");
    echo "<h3>Table Structure:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show sample data
    $result = $conn->query("SELECT * FROM products LIMIT 5");
    echo "<h3>Sample Data:</h3>";
    echo "<table border='1'>";
    if ($result->num_rows > 0) {
        $first = true;
        while ($row = $result->fetch_assoc()) {
            if ($first) {
                echo "<tr>";
                foreach ($row as $key => $value) {
                    echo "<th>" . $key . "</th>";
                }
                echo "</tr>";
                $first = false;
            }
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . $value . "</td>";
            }
            echo "</tr>";
        }
    }
    echo "</table>";
} else {
    echo "Table 'products' does not exist!<br>";
}

$conn->close(); 