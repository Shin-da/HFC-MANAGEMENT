<!-- META TAGS -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- <meta http-equiv="refresh" content="120"> -->

<!-- FAVICON -->
<link rel="icon" href="../resources/images/henrichlogo.png">

<!-- FONTS -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

<!-- STYLESHEETS -->

<link rel="stylesheet" href="../assets/css/admin-nav.css">
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<link rel="stylesheet" href="../assets/css/shared-dashboard.css">
<link rel="stylesheet" href="../assets/css/admin.css">
<link rel="stylesheet" href="../assets/css/admin-dashboard.css">
<link rel="stylesheet" href="../assets/css/admin-layout.css">
<link rel="stylesheet" type="text/css" href="../assets/css/style.css">
<link rel="stylesheet" type="text/css" href="../assets/css/sidebar.css">
<link rel="stylesheet" type="text/css" href="../assets/css/calendar.css">
<link rel="stylesheet" type="text/css" href="../assets/css/navbar.css">
<link rel="stylesheet" type="text/css" href="../assets/css/dashboard.css">
<link rel="stylesheet" type="text/css" href="../assets/css/shared-dashboard.css">
<link rel="stylesheet" type="text/css" href="../assets/css/customer-pages.css">
<link rel="stylesheet" type="text/css" href="../assets/css/form.css">
<!-- <link rel="stylesheet" type="text/css" href="../assets/css/dashboard.css"> -->

<!-- BOXICONS -->
<link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>

<!-- Add Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

<!-- Add Toast notifications -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

<!-- JAVASCRIPTS -->

<script src="../assets/js/script.js"></script>
<script src="../assets/js/datetime.js"></script> <!-- datetime -->
<script src="../assets/js/weather.js"></script> <!-- For weather -->
<script src="../assets/js/product_rank.js"></script> <!-- For Product rankin -->
<script src="../assets/js/holidays.js"></script><!-- For holidays -->
<script src="../assets/js/search.js"> </script> <!-- JS for search -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jquery -->

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!-- Remove old SweetAlert2 references -->
<!-- Add single include for alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<?php
// Fix the path resolution
$alertsPath = __DIR__ . '/alerts.php';
require_once $alertsPath;
?>

<!-- Add Animate.css for smooth animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<!-- Add Select2 CSS and JS via CDN -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- <style>
    /* SweetAlert2 Custom Styling */
    .swal2-popup {
        font-size: 0.9rem !important;
        border-radius: 12px !important;
    }
    .swal2-toast {
        padding: 0.5rem !important;
        background: var(--background) !important;
        box-shadow: 0 0 10px rgba(0,0,0,0.1) !important;
    }
    .swal2-toast .swal2-title {
        margin: 0 0 0 0.75rem !important;
        font-size: 0.9rem !important;
        color: var(--text-primary) !important;
    }
    .swal2-toast .swal2-icon {
        margin: 0 !important;
        font-size: 1.5rem !important;
    }
    .swal2-confirm {
        background: var(--primary) !important;
        color: var(--sand) !important;
        border-radius: 8px !important;
        padding: 0.75rem 1.5rem !important;
        font-weight: 500 !important;
    }
    .swal2-cancel {
        background: var(--secondary) !important;
        color: var(--sand) !important;
        border-radius: 8px !important;
        padding: 0.75rem 1.5rem !important;
        font-weight: 500 !important;
    }
    .swal2-title {
        color: var(--text-primary) !important;
    }
    .swal2-html-container {
        color: var(--text-secondary) !important;
    }

    /* Toast Styling */
    .colored-toast {
        background: var(--primary) !important;
        color: var(--sand) !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        border-radius: 8px !important;
        padding: 12px 20px !important;
        margin: 20px !important;
    }
    .colored-toast.swal2-icon-success {
        background: var(--success) !important;
    }
    .colored-toast.swal2-icon-error {
        background: var(--error) !important;
    }
    .colored-toast.swal2-icon-warning {
        background: var(--warning) !important;
    }
    .colored-toast.swal2-icon-info {
        background: var(--info) !important;
    }
    .toast-title {
        color: var(--sand) !important;
        font-size: 0.95rem !important;
        font-weight: 500 !important;
        margin-left: 10px !important;
    }
    .toast-progress {
        background: rgba(255, 255, 255, 0.3) !important;
    }
    /* Hide the close button in toasts */
    .colored-toast .swal2-close {
        display: none !important;
    }
    /* Adjust icon size in toasts */
    .colored-toast .swal2-icon {
        width: 1.5em !important;
        height: 1.5em !important;
        margin: 0 !important;
    }
    .colored-toast .swal2-icon-content {
        font-size: 1.25em !important;
    }

    /* Toast Position Fix */
    .custom-toast {
        z-index: 9999 !important;
        margin: 1rem !important;
        padding: 0.75rem 1.5rem !important;
    }

    /* Ensure navbar stays on top */
    .navbar {
        z-index: 10000 !important;
    }
</style> -->
<script src="../includes/js/alerts.js"></script>