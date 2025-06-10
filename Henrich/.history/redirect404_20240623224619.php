<?php
// Check if the requested page exists
if (file_exists($_SERVER['REQUEST_URI'])) {
    // Redirect to the 404 error page
    header('Location: /404.html');
    exit;
}
?>