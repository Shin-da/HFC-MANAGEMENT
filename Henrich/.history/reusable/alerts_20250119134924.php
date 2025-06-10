<!-- SweetAlert2 Resources -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="../resources/css/alerts.css" rel="stylesheet">
<script src="../resources/js/alerts.js"></script>

<!-- Session Message Handler -->
<?php
if (isset($_SESSION['success']) || isset($_SESSION['error'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('" . 
                (isset($_SESSION['success']) ? 'success' : 'error') . "', '" . 
                (isset($_SESSION['success']) ? $_SESSION['success'] : $_SESSION['error']) . "'
            );
        });
    </script>";
    unset($_SESSION['success']);
    unset($_SESSION['error']);
}
?>
