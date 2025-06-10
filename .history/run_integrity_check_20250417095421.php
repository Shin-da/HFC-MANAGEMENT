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

// Instead of using the SQL file, let's get the table info directly from the database
echo "<h2>Tables in dbhenrichfoodcorps database</h2>";
$tables_query = "SHOW TABLES";
$result = $conn->query($tables_query);

if ($result) {
    echo "<table><tr><th>Table Name</th></tr>";
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["Tables_in_" . $dbname] . "</td></tr>";
        }
    } else {
        echo "<tr><td>No tables found in database</td></tr>";
    }
    echo "</table>";
}

// Get structure of each table
echo "<h2>Table Structure</h2>";
$result = $conn->query($tables_query);
if ($result) {
    while($row = $result->fetch_assoc()) {
        $table_name = $row["Tables_in_" . $dbname];
        echo "<h3>Structure of $table_name</h3>";
        
        $structure_query = "DESCRIBE `$table_name`";
        $structure_result = $conn->query($structure_query);
        
        if ($structure_result) {
            echo "<table><tr>";
            // Get field information
            $fields = $structure_result->fetch_fields();
            foreach ($fields as $field) {
                echo "<th>{$field->name}</th>";
            }
            echo "</tr>";
            
            // Output data
            while ($struct_row = $structure_result->fetch_assoc()) {
                echo "<tr>";
                foreach ($struct_row as $value) {
                    echo "<td>$value</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // Get number of records
        $count_query = "SELECT COUNT(*) as total FROM `$table_name`";
        $count_result = $conn->query($count_query);
        if ($count_result) {
            $count_row = $count_result->fetch_assoc();
            echo "<p>Total records: " . $count_row['total'] . "</p>";
        }
    }
}

// Check for common data integrity issues
echo "<h2>Data Integrity Checks</h2>";

// Get all tables again for integrity checks
$result = $conn->query($tables_query);
$tables = [];
if ($result) {
    while($row = $result->fetch_assoc()) {
        $tables[] = $row["Tables_in_" . $dbname];
    }
}

// Check 1: Primary key columns with NULL values
echo "<h3>NULL Values in Key Columns</h3>";
foreach ($tables as $table) {
    // Get primary key columns
    $pk_query = "SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'";
    $pk_result = $conn->query($pk_query);
    
    if ($pk_result && $pk_result->num_rows > 0) {
        while ($pk_row = $pk_result->fetch_assoc()) {
            $column = $pk_row['Column_name'];
            $null_check_query = "SELECT COUNT(*) as null_count FROM `$table` WHERE `$column` IS NULL";
            $null_result = $conn->query($null_check_query);
            
            if ($null_result) {
                $null_row = $null_result->fetch_assoc();
                if ($null_row['null_count'] > 0) {
                    echo "<div class='message issue'>Table '$table' has {$null_row['null_count']} NULL values in primary key column '$column'</div>";
                }
            }
        }
    }
}

// Check 2: Duplicate values in unique fields
echo "<h3>Duplicate Values in Unique Fields</h3>";
foreach ($tables as $table) {
    // Get unique key columns
    $uk_query = "SHOW KEYS FROM `$table` WHERE Non_unique = 0";
    $uk_result = $conn->query($uk_query);
    
    if ($uk_result && $uk_result->num_rows > 0) {
        while ($uk_row = $uk_result->fetch_assoc()) {
            $column = $uk_row['Column_name'];
            $dup_check_query = "SELECT `$column`, COUNT(*) as dup_count FROM `$table` 
                              WHERE `$column` IS NOT NULL 
                              GROUP BY `$column` HAVING COUNT(*) > 1";
            $dup_result = $conn->query($dup_check_query);
            
            if ($dup_result && $dup_result->num_rows > 0) {
                echo "<div class='message issue'>Table '$table' has duplicate values in unique column '$column':</div>";
                echo "<table><tr><th>$column</th><th>Count</th></tr>";
                while ($dup_row = $dup_result->fetch_assoc()) {
                    echo "<tr><td>{$dup_row[$column]}</td><td>{$dup_row['dup_count']}</td></tr>";
                }
                echo "</table>";
            }
        }
    }
}

// Check 3: Foreign key relationships (if tables exist)
echo "<h3>Foreign Key Relationship Checks</h3>";

// Common foreign key relationships to check
$fk_checks = [
    ['from_table' => 'customerorder', 'from_column' => 'customerid', 'to_table' => 'customeraccount', 'to_column' => 'customerid'],
    ['from_table' => 'orderlog', 'from_column' => 'orderid', 'to_table' => 'customerorder', 'to_column' => 'orderid']
];

foreach ($fk_checks as $fk) {
    // Check if both tables exist
    if (in_array($fk['from_table'], $tables) && in_array($fk['to_table'], $tables)) {
        $fk_query = "SELECT f.`{$fk['from_column']}`, COUNT(*) as record_count 
                    FROM `{$fk['from_table']}` f
                    LEFT JOIN `{$fk['to_table']}` t ON f.`{$fk['from_column']}` = t.`{$fk['to_column']}`
                    WHERE f.`{$fk['from_column']}` IS NOT NULL AND t.`{$fk['to_column']}` IS NULL
                    GROUP BY f.`{$fk['from_column']}`";
        
        $fk_result = $conn->query($fk_query);
        
        if ($fk_result && $fk_result->num_rows > 0) {
            echo "<div class='message issue'>Found orphaned records in '{$fk['from_table']}' referencing non-existent '{$fk['to_table']}' records:</div>";
            echo "<table><tr><th>{$fk['from_column']}</th><th>Record Count</th></tr>";
            while ($fk_row = $fk_result->fetch_assoc()) {
                echo "<tr><td>{$fk_row[$fk['from_column']]}</td><td>{$fk_row['record_count']}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='message success'>No orphaned records found between '{$fk['from_table']}' and '{$fk['to_table']}'</div>";
        }
    } else {
        if (!in_array($fk['from_table'], $tables)) {
            echo "<div>Skipping foreign key check: Table '{$fk['from_table']}' does not exist</div>";
        }
        if (!in_array($fk['to_table'], $tables)) {
            echo "<div>Skipping foreign key check: Table '{$fk['to_table']}' does not exist</div>";
        }
    }
}

echo "<h2>Done!</h2>";
echo "<div class='message success'>Integrity check complete. Please review any issues found above.</div>";
echo "</body></html>";

// Close connection
$conn->close();
?> 