<?php
require '../../session/session.php';
// ...existing process code...

if ($success) {
    $_SESSION['sweetalert'] = [
        'icon' => 'success',
        'title' => 'Success!',
        'text' => 'Operation completed successfully'
    ];
} else {
    $_SESSION['sweetalert'] = [
        'icon' => 'error',
        'title' => 'Error!',
        'text' => 'Operation failed: ' . $error_message
    ];
}
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
