<?php
// Test file for the quick-actions component in the admin directory
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Testing Quick Actions Component in Admin Directory</h1>";

// Define BASEPATH to prevent direct access restrictions
define('BASEPATH', true);

// Test paths
$paths = [
    // Absolute paths
    'C:/xampp/htdocs/HFCManagement/Henrich/components/quick-actions.php',
    // Relative paths
    '../components/quick-actions.php',
    '../../components/quick-actions.php',
    '../../../components/quick-actions.php',
    '../../../Henrich/components/quick-actions.php',
    // Special paths
    $_SERVER['DOCUMENT_ROOT'] . '/HFCManagement/Henrich/components/quick-actions.php'
];

echo "<h2>Checking Paths:</h2>";
echo "<ul>";
foreach ($paths as $path) {
    echo "<li>Path: " . htmlspecialchars($path);
    
    if (file_exists($path)) {
        echo " - <span style='color:green'>EXISTS</span>";
        
        echo "<br>Trying to include: ";
        try {
            include_once $path;
            echo "<span style='color:green'>Included successfully!</span>";
        } catch (Exception $e) {
            echo "<span style='color:red'>Error: " . $e->getMessage() . "</span>";
        }
        
        if (function_exists('initQuickActions')) {
            echo "<br><span style='color:green'>✓ initQuickActions function exists</span>";
        } else {
            echo "<br><span style='color:red'>✗ initQuickActions function does not exist</span>";
        }
    } else {
        echo " - <span style='color:red'>NOT FOUND</span>";
    }
    
    echo "</li>";
}
echo "</ul>";

// Show current directory information
echo "<h2>Directory Information:</h2>";
echo "<p>Current script: " . __FILE__ . "</p>";
echo "<p>Current directory: " . __DIR__ . "</p>";
echo "<p>Document root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Current working directory: " . getcwd() . "</p>";
?> 