<?php
require '../../session/session.php';
// ...existing process code...

if ($success) {
    $_SESSION['success'] = "Operation completed successfully";
} else {
    $_SESSION['error'] = "Operation failed: " . $error_message;
}
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
