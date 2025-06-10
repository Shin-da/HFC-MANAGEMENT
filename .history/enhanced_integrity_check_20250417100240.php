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

echo "<html><head><title>Enhanced Database Integrity Check</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h1 { color: #333; }
    h2 { color: #555; margin-top: 20px; }
    h3 { color: #666; margin-top: 15px; }
    table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .message { font-weight: bold; margin: 10px 0; }
    .issue { color: #d9534f; }
    .warning { color: #f0ad4e; }
    .success { color: #5cb85c; }
    .section { background-color: #f8f9fa; padding: 15px; margin-bottom: 20px; border-left: 4px solid #007bff; }
</style></head><body>";

echo "<h1>Enhanced Database Integrity Check for dbhenrichfoodcorps</h1>";

// Step 1: Get all tables and their structure
echo "<div class='section'>";
echo "<h2>Database Structure Overview</h2>";
$tables_query = "SHOW TABLES";
$result = $conn->query($tables_query);

$tables = [];
$table_structures = [];
$primary_keys = [];
$foreign_key_candidates = [];

if ($result) {
    echo "<h3>Tables Found</h3>";
    echo "<table><tr><th>Table Name</th><th>Records</th></tr>";
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $table_name = $row["Tables_in_" . $dbname];
            $tables[] = $table_name;
            
            // Get record count
            $count_query = "SELECT COUNT(*) as total FROM `$table_name`";
            $count_result = $conn->query($count_query);
            $count = 0;
            if ($count_result) {
                $count_row = $count_result->fetch_assoc();
                $count = $count_row['total'];
            }
            
            echo "<tr><td>$table_name</td><td>$count</td></tr>";
            
            // Get table structure
            $structure_query = "DESCRIBE `$table_name`";
            $structure_result = $conn->query($structure_query);
            
            if ($structure_result) {
                $table_structures[$table_name] = [];
                while ($column = $structure_result->fetch_assoc()) {
                    $table_structures[$table_name][] = $column;
                    
                    // Identify potential primary and foreign keys
                    $column_name = $column['Field'];
                    
                    // If this is a primary key
                    if ($column['Key'] == 'PRI') {
                        $primary_keys[$table_name] = $column_name;
                    }
                    
                    // If column name ends with 'id' or 'ID' and is not a primary key in this table
                    // it might be a foreign key to another table
                    if ((substr(strtolower($column_name), -2) == 'id' || 
                         strpos(strtolower($column_name), '_id') !== false) && 
                        $column['Key'] != 'PRI') {
                        
                        // Try to guess the referenced table
                        $potential_table = str_replace(array('_id', 'id'), '', strtolower($column_name));
                        
                        // Add this to foreign key candidates
                        $foreign_key_candidates[] = [
                            'table' => $table_name,
                            'column' => $column_name,
                            'potential_ref_table' => $potential_table,
                            'potential_ref_column' => $primary_keys[$potential_table] ?? $column_name
                        ];
                    }
                }
            }
        }
    } else {
        echo "<tr><td colspan='2'>No tables found in database</td></tr>";
    }
    echo "</table>";
}
echo "</div>";

// Step 2: Table Structure Details
echo "<div class='section'>";
echo "<h2>Detailed Table Structures</h2>";
foreach ($tables as $table_name) {
    echo "<h3>$table_name</h3>";
    echo "<table><tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($table_structures[$table_name] as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>" . ($column['Default'] === NULL ? 'NULL' : $column['Default']) . "</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
}
echo "</div>";

// Step 3: Data Integrity Checks
echo "<div class='section'>";
echo "<h2>Data Integrity Checks</h2>";

// Check 1: NULL values in primary keys
echo "<h3>NULL Values in Primary Keys</h3>";
$found_issues = false;

foreach ($tables as $table) {
    if (isset($primary_keys[$table])) {
        $pk = $primary_keys[$table];
        $null_check_query = "SELECT COUNT(*) as null_count FROM `$table` WHERE `$pk` IS NULL";
        $null_result = $conn->query($null_check_query);
        
        if ($null_result) {
            $null_row = $null_result->fetch_assoc();
            if ($null_row['null_count'] > 0) {
                echo "<div class='message issue'>Table '$table' has {$null_row['null_count']} NULL values in primary key column '$pk'</div>";
                $found_issues = true;
            }
        }
    }
}

if (!$found_issues) {
    echo "<div class='message success'>No NULL values found in primary key columns.</div>";
}

// Check 2: Duplicate values in unique columns
echo "<h3>Duplicate Values in Unique Columns</h3>";
$found_issues = false;

foreach ($tables as $table) {
    // Get all unique constraints
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
                $found_issues = true;
            }
        }
    }
}

if (!$found_issues) {
    echo "<div class='message success'>No duplicate values found in unique columns.</div>";
}

// Check 3: Auto-detected foreign key relationships
echo "<h3>Automatically Detected Foreign Key Relationships</h3>";

// Display detected foreign key candidates
echo "<h4>Potential Foreign Key Relationships</h4>";
echo "<table><tr><th>Table</th><th>Column</th><th>Referenced Table</th><th>Referenced Column</th><th>Status</th></tr>";

foreach ($foreign_key_candidates as $fk) {
    // Check if the potential referenced table exists
    if (in_array($fk['potential_ref_table'], $tables)) {
        $ref_table = $fk['potential_ref_table'];
        $ref_column = $fk['potential_ref_column'];
        
        // Check for orphaned records (records with no matching reference)
        $orphan_check_query = "SELECT COUNT(*) as orphan_count 
                              FROM `{$fk['table']}` t1 
                              LEFT JOIN `$ref_table` t2 ON t1.`{$fk['column']}` = t2.`$ref_column` 
                              WHERE t1.`{$fk['column']}` IS NOT NULL AND t2.`$ref_column` IS NULL";
        
        $orphan_result = $conn->query($orphan_check_query);
        $status = "Unknown";
        
        if ($orphan_result) {
            $orphan_row = $orphan_result->fetch_assoc();
            if ($orphan_row['orphan_count'] > 0) {
                $status = "<span class='issue'>{$orphan_row['orphan_count']} orphaned records</span>";
            } else {
                $status = "<span class='success'>No orphaned records</span>";
            }
        }
        
        echo "<tr>";
        echo "<td>{$fk['table']}</td>";
        echo "<td>{$fk['column']}</td>";
        echo "<td>$ref_table</td>";
        echo "<td>$ref_column</td>";
        echo "<td>$status</td>";
        echo "</tr>";
    } else {
        // Referenced table doesn't exist, still show it but mark as warning
        echo "<tr>";
        echo "<td>{$fk['table']}</td>";
        echo "<td>{$fk['column']}</td>";
        echo "<td>{$fk['potential_ref_table']} (not found)</td>";
        echo "<td>{$fk['potential_ref_column']}</td>";
        echo "<td><span class='warning'>Referenced table not found</span></td>";
        echo "</tr>";
    }
}
echo "</table>";

// Check 4: Common predefined relationships (as a fallback)
echo "<h4>Checking Common Table Relationships</h4>";

$common_relationships = [
    ['from_table' => 'customerorder', 'from_column' => 'customerid', 'to_table' => 'customeraccount', 'to_column' => 'customerid'],
    ['from_table' => 'orderlog', 'from_column' => 'orderid', 'to_table' => 'customerorder', 'to_column' => 'orderid'],
    ['from_table' => 'orderlog', 'from_column' => 'productcode', 'to_table' => 'products', 'to_column' => 'productcode'],
    ['from_table' => 'inventory', 'from_column' => 'productcode', 'to_table' => 'products', 'to_column' => 'productcode'],
    ['from_table' => 'branch_inventory', 'from_column' => 'branch_id', 'to_table' => 'branches', 'to_column' => 'branch_id'],
    ['from_table' => 'branch_inventory', 'from_column' => 'productcode', 'to_table' => 'products', 'to_column' => 'productcode'],
    ['from_table' => 'stockmovement', 'from_column' => 'productcode', 'to_table' => 'products', 'to_column' => 'productcode'],
    ['from_table' => 'stockmovement', 'from_column' => 'branch_id', 'to_table' => 'branches', 'to_column' => 'branch_id']
];

$found_issues = false;

foreach ($common_relationships as $rel) {
    // Check if both tables exist
    if (in_array($rel['from_table'], $tables) && in_array($rel['to_table'], $tables)) {
        // Check for orphaned records
        $query = "SELECT COUNT(*) as orphan_count 
                 FROM `{$rel['from_table']}` t1 
                 LEFT JOIN `{$rel['to_table']}` t2 ON t1.`{$rel['from_column']}` = t2.`{$rel['to_column']}` 
                 WHERE t1.`{$rel['from_column']}` IS NOT NULL AND t2.`{$rel['to_column']}` IS NULL";
        
        $result = $conn->query($query);
        
        if ($result) {
            $row = $result->fetch_assoc();
            if ($row['orphan_count'] > 0) {
                echo "<div class='message issue'>Found {$row['orphan_count']} orphaned records in '{$rel['from_table']}' referencing non-existent '{$rel['to_table']}' records (column '{$rel['from_column']}').</div>";
                $found_issues = true;
                
                // Get sample of orphaned records
                $sample_query = "SELECT t1.`{$rel['from_column']}` as id_value 
                               FROM `{$rel['from_table']}` t1 
                               LEFT JOIN `{$rel['to_table']}` t2 ON t1.`{$rel['from_column']}` = t2.`{$rel['to_column']}` 
                               WHERE t1.`{$rel['from_column']}` IS NOT NULL AND t2.`{$rel['to_column']}` IS NULL 
                               LIMIT 5";
                
                $sample_result = $conn->query($sample_query);
                if ($sample_result && $sample_result->num_rows > 0) {
                    echo "<table><tr><th>Orphaned {$rel['from_column']} Values (Sample)</th></tr>";
                    while ($sample_row = $sample_result->fetch_assoc()) {
                        echo "<tr><td>{$sample_row['id_value']}</td></tr>";
                    }
                    echo "</table>";
                }
            } else {
                echo "<div class='message success'>No orphaned records found between '{$rel['from_table']}' and '{$rel['to_table']}' (column '{$rel['from_column']}').</div>";
            }
        }
    } else {
        if (!in_array($rel['from_table'], $tables) && !in_array($rel['to_table'], $tables)) {
            echo "<div class='warning'>Relationship check skipped: Tables '{$rel['from_table']}' and '{$rel['to_table']}' do not exist.</div>";
        } else if (!in_array($rel['from_table'], $tables)) {
            echo "<div class='warning'>Relationship check skipped: Table '{$rel['from_table']}' does not exist.</div>";
        } else {
            echo "<div class='warning'>Relationship check skipped: Table '{$rel['to_table']}' does not exist.</div>";
        }
    }
}

if (!$found_issues) {
    echo "<div class='message success'>All common relationships checked are valid.</div>";
}

// Check 5: Data type and format consistency
echo "<h3>Data Type and Format Consistency</h3>";

// Check email format in user tables
$email_tables = [
    ['table' => 'users', 'column' => 'email'],
    ['table' => 'users', 'column' => 'usermail'],
    ['table' => 'users', 'column' => 'useremail'],
    ['table' => 'customeraccount', 'column' => 'email'],
    ['table' => 'approved_account', 'column' => 'usermail']
];

$found_issues = false;

foreach ($email_tables as $et) {
    if (in_array($et['table'], $tables)) {
        // Check if column exists
        $column_exists = false;
        foreach ($table_structures[$et['table']] as $column) {
            if ($column['Field'] == $et['column']) {
                $column_exists = true;
                break;
            }
        }
        
        if ($column_exists) {
            // Check for invalid email formats
            $email_check_query = "SELECT COUNT(*) as invalid_count 
                                FROM `{$et['table']}` 
                                WHERE `{$et['column']}` IS NOT NULL 
                                AND `{$et['column']}` NOT LIKE '%@%.%'";
            
            $email_result = $conn->query($email_check_query);
            
            if ($email_result) {
                $email_row = $email_result->fetch_assoc();
                if ($email_row['invalid_count'] > 0) {
                    echo "<div class='message issue'>Found {$email_row['invalid_count']} invalid email formats in '{$et['table']}.{$et['column']}'.</div>";
                    $found_issues = true;
                    
                    // Get sample of invalid emails
                    $sample_query = "SELECT `{$et['column']}` as email_value 
                                   FROM `{$et['table']}` 
                                   WHERE `{$et['column']}` IS NOT NULL 
                                   AND `{$et['column']}` NOT LIKE '%@%.%' 
                                   LIMIT 5";
                    
                    $sample_result = $conn->query($sample_query);
                    if ($sample_result && $sample_result->num_rows > 0) {
                        echo "<table><tr><th>Invalid Email Values (Sample)</th></tr>";
                        while ($sample_row = $sample_result->fetch_assoc()) {
                            echo "<tr><td>{$sample_row['email_value']}</td></tr>";
                        }
                        echo "</table>";
                    }
                }
            }
        }
    }
}

if (!$found_issues) {
    echo "<div class='message success'>No data format issues found.</div>";
}

echo "</div>";

// Step 4: Summary and Recommendations
echo "<div class='section'>";
echo "<h2>Summary and Recommendations</h2>";
echo "<p>The enhanced database integrity check has been completed for the dbhenrichfoodcorps database.</p>";

// Generate recommendations based on issues found
echo "<h3>Recommendations:</h3>";
echo "<ol>";
echo "<li>Review and fix any identified issues, particularly orphaned records and invalid data formats.</li>";
echo "<li>Consider implementing proper foreign key constraints in your database schema to prevent future orphaned records.</li>";
echo "<li>Regularly perform database integrity checks, especially after large data imports or schema changes.</li>";
echo "<li>Create a database backup before making any corrections to address the issues identified.</li>";
echo "</ol>";

echo "</div>";

echo "<h2>Done!</h2>";
echo "<div class='message success'>Enhanced integrity check complete. Please review any issues found above.</div>";
echo "</body></html>";

// Close connection
$conn->close();
?> 