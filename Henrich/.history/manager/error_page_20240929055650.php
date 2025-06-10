<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Error Page</title>
</head>
<body>
    <h1>Error</h1>
    <p><?php echo $_GET['error'] ?? 'No error message provided'; ?></p>
</body>

SHEEEEESH SHETT