<?php
// Database connection parameters
$host = 'localhost';
$username = 'root';  // Default XAMPP MySQL username
$password = '';      // Default XAMPP MySQL password (empty)
$dbname = 'dbhenrichfoodcorps';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<html><head><title>Database Integrity Check</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h1 { color: #333; }
    h2 { color: #555; margin-top: 20px; }
    table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .message { font-weight: bold; margin: 10px 0; }
    .issue { color: #d9534f; }
    .success { color: #5cb85c; }
</style></head><body>";
echo "<h1>Database Integrity Check Results for dbhenrichfoodcorps</h1>";

// Read the SQL file
$sqlFilePath = 'data_integrity_check_ultra_flexible.sql';
if (!file_exists($sqlFilePath)) {
    die("Error: SQL file not found at $sqlFilePath");
}

$sqlContent = file_get_contents($sqlFilePath);

// Split the SQL into individual queries
// This simple approach splits on semicolons, but ignores semicolons within quotes
$queries = [];
$current = '';
$inQuote = false;
$quoteChar = '';

for ($i = 0; $i < strlen($sqlContent); $i++) {
    $char = $sqlContent[$i];
    
    // Handle quotes
    if (($char == "'" || $char == '"') && ($i == 0 || $sqlContent[$i-1] != '\\')) {
        if (!$inQuote) {
            $inQuote = true;
            $quoteChar = $char;
        } else if ($char == $quoteChar) {
            $inQuote = false;
        }
    }
    
    // Add character to current query
    $current .= $char;
    
    // If we hit a semicolon and we're not in a quote, we've finished a query
    if ($char == ';' && !$inQuote) {
        $queries[] = $current;
        $current = '';
    }
}

// If there's anything left in $current, add it as the last query
if (trim($current) != '') {
    $queries[] = $current;
}

// Execute each query and collect results
$results = [];
$currentSection = 'General';

foreach ($queries as $query) {
    $query = trim($query);
    if (empty($query)) continue;
    
    // Check if this is a section header comment
    if (preg_match('/-- ==========\s*(.*?)\s*==========/', $query, $matches)) {
        $currentSection = $matches[1];
        echo "<h2>$currentSection</h2>";
        continue;
    }
    
    // Skip comments
    if (substr($query, 0, 2) == '--') {
        continue;
    }
    
    // Execute the query
    $result = $conn->multi_query($query);
    
    if ($result) {
        do {
            if ($result = $conn->store_result()) {
                echo "<table><tr>";
                // Get field information
                $fields = $result->fetch_fields();
                foreach ($fields as $field) {
                    echo "<th>{$field->name}</th>";
                }
                echo "</tr>";
                
                // Output data
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        // Make issue text red
                        if (isset($row['issue']) && $fields[0]->name == 'issue') {
                            echo "<td class='issue'>$value</td>";
                        } else {
                            echo "<td>$value</td>";
                        }
                    }
                    echo "</tr>";
                }
                echo "</table>";
                $result->free();
            }
        } while ($conn->next_result());
    } else {
        if ($conn->error) {
            echo "<div class='message issue'>Error: " . $conn->error . "</div>";
        }
    }
}

echo "<h2>Done!</h2>";
echo "<div class='message success'>Integrity check complete. Please review any issues found above.</div>";
echo "</body></html>";

// Close connection
$conn->close();
?> 