<?php
/**
 * Test Quick Actions Component
 * This is a simplified version for testing include paths
 */

// Remove the BASEPATH check to allow direct access for testing
// if (!defined('BASEPATH')) exit('No direct script access allowed');

// Define the initQuickActions function
if (!function_exists('initQuickActions')) {
    function initQuickActions($config) {
        // Return a simple HTML output
        $html = '<div class="quick-actions-test">Quick Actions Test Successfully Loaded!</div>';
        return $html;
    }
}

// Indicate this file was loaded successfully
echo "TEST QUICK ACTIONS COMPONENT LOADED SUCCESSFULLY!\n";
echo "This is a test version of the quick-actions.php file.\n";
echo "If you're seeing this message, your include path is working correctly.\n";
?> 