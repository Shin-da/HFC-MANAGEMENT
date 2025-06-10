<?php
// Check if the requested page exists
$uri = $_SERVER['REQUEST_URI'];
$page = basename($uri);

if ($page !== 'home.php') {
    // Redirect to the 404 error page
    header('Location: /HenrichProto/404.html');
    exit;
}
