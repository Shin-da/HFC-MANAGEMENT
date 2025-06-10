<?php
// Test file for the quick-actions component

// Include the quick-actions component
include_once './components/quick-actions.php';

// Check if the initQuickActions function exists
if (function_exists('initQuickActions')) {
    echo "SUCCESS: initQuickActions function exists!<br>";
    
    // Test the function
    $html = initQuickActions([
        'mainIcon' => 'fas fa-check',
        'mainColor' => 'success',
        'actions' => [
            [
                'icon' => 'fas fa-home',
                'label' => 'Home',
                'type' => 'primary',
                'url' => '#'
            ],
            [
                'icon' => 'fas fa-cog',
                'label' => 'Settings',
                'type' => 'info',
                'url' => '#'
            ]
        ]
    ]);
    
    echo "<h3>Quick Actions Component Test</h3>";
    echo "<p>The component should appear below:</p>";
    echo $html;
} else {
    echo "ERROR: initQuickActions function does not exist!<br>";
}
?> 