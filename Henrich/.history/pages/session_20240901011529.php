

<?php
session_start();
// set session timeout to 2 minutes
if (isset($_SESSION['uid']) && isset($_SESSION['role'])) {
    if (isset($_SESSION['timeout'])) {
        if (time() - $_SESSION['timeout'] > 120) {
            session_unset();
            session_destroy();
            header("Location: ../login/login.php");
            exit();
        } else {
            $_SESSION['timeout'] = time();
        }
    } else {
        $_SESSION['timeout'] = time();
    }
}