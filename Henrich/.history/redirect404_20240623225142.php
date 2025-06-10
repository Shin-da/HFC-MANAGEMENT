<?php
// Check if the requested page exists
var_dump($_SERVER['REQUEST_URI']); // Add this line for debugging
if (!file_exists($_SERVER['REQUEST_URI'])) {
    // Redirect to the 404 error page
    var_dump($_SERVER['REQUEST_URI']); // Add this line for debugging
    header('Location: /');
    exit;
} else {
    // Redirect to the requested page
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}
?>