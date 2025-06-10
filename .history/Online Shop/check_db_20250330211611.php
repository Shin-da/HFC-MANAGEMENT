<?php
include "./database/dbconnect.php";

echo "<h2>Database Check</h2>";

// Check branches table
echo "<h3>Checking branches table:</h3>";
$result = $conn->query("SHOW TABLES LIKE 'branches'");
if ($result->num_rows > 0) {
    echo "branches table exists<br>";
    
    // Check branches data
    $branches = $conn->query("SELECT * FROM branches");
    if ($branches->num_rows > 0) {
        echo "Found " . $branches->num_rows . " branches:<br>";
        while ($branch = $branches->fetch_assoc()) {
            echo "Branch ID: " . $branch['branch_id'] . ", Name: " . $branch['branch_name'] . "<br>";
        }
    } else {
        echo "No branches found in the table<br>";
        // Insert default branch if none exists
        $conn->query("INSERT INTO branches (branch_name, branch_address) VALUES ('Main Branch', 'Default Address')");
        echo "Inserted default branch<br>";
    }
} else {
    echo "branches table does not exist<br>";
    // Create branches table if it doesn't exist
    $conn->query("CREATE TABLE branches (
        branch_id INT PRIMARY KEY AUTO_INCREMENT,
        branch_name VARCHAR(100) NOT NULL,
        branch_address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Created branches table<br>";
    
    // Insert default branch
    $conn->query("INSERT INTO branches (branch_name, branch_address) VALUES ('Main Branch', 'Default Address')");
    echo "Inserted default branch<br>";
}

// Check customerorder table structure
echo "<h3>Checking customerorder table structure:</h3>";
$result = $conn->query("DESCRIBE customerorder");
if ($result->num_rows > 0) {
    echo "customerorder table structure:<br>";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "<br>";
    }
} else {
    echo "customerorder table does not exist<br>";
}

// Check orderlog table structure
echo "<h3>Checking orderlog table structure:</h3>";
$result = $conn->query("DESCRIBE orderlog");
if ($result->num_rows > 0) {
    echo "orderlog table structure:<br>";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "<br>";
    }
} else {
    echo "orderlog table does not exist<br>";
}

$conn->close();
?> 