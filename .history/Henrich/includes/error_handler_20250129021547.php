<?php
function handleError($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno]: $errstr in $errfile:$errline");
    
    if (ini_get('display_errors')) {
        echo "<div style='color:red;'>";
        echo "An error occurred. Please try again later.";
        if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1') {
            echo "<br>Debug: $errstr in $errfile:$errline";
        }
        echo "</div>";
    }
    
    return true;
}

set_error_handler('handleError');
