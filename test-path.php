<?php
// Test script to check if paths to quick-actions.php are correct
echo "Testing paths to quick-actions.php<br>";

$paths = [
    // Absolute paths
    'C:\xampp\htdocs\HFCManagement\Henrich\components\quick-actions.php',
    'C:\xampp\htdocs\HFCManagement\henrich\components\quick-actions.php',
    
    // Relative paths
    __DIR__ . '/Henrich/components/quick-actions.php',
    __DIR__ . '/henrich/components/quick-actions.php',
    
    // Admin include paths
    __DIR__ . '/Henrich/admin/components/quick-actions.php',
    __DIR__ . '/henrich/admin/components/quick-actions.php'
];

foreach ($paths as $path) {
    echo "Checking path: $path<br>";
    if (file_exists($path)) {
        echo "✅ File exists<br>";
    } else {
        echo "❌ File does not exist<br>";
    }
}

// Try to include the global file
echo "<hr>";
echo "Trying to include the global quick-actions.php file<br>";
@include_once 'Henrich/components/quick-actions.php';

if (function_exists('initQuickActions')) {
    echo "✅ initQuickActions function exists after include<br>";
} else {
    echo "❌ initQuickActions function does not exist after include<br>";
}
?> 