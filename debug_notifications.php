<?php
// Debug notifications handler
error_reporting(E_ALL);
ini_set('display_errors', 1);

function debugLog($message, $level = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp][$level] $message\n";
    
    // Log to file
    error_log($logMessage, 3, __DIR__ . '/debug.log');

    // Output to console
    echo $logMessage;
}