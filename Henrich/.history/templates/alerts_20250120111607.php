<?php
// Alert/Notification System

function showAlert($type, $message) {
    echo "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
            {$message}
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";
}

// Flash messages from session
if (isset($_SESSION['alert'])) {
    $alert = $_SESSION['alert'];
    showAlert($alert['type'], $alert['message']);
    unset($_SESSION['alert']); // Clear the alert after showing
}

// System notifications (if any)
if (isset($dashboard_data['notifications'])) {
    foreach ($dashboard_data['notifications'] as $notification) {
        showAlert($notification['type'], $notification['message']);
    }
}
?>
