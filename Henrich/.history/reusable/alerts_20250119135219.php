<!-- SweetAlert2 Resources -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="../resources/css/alerts.css" rel="stylesheet">
<script src="../resources/js/alerts.js"></script>

<!-- Session Message Handler -->
<?php
// SweetAlert2 Messages
if (isset($_SESSION['sweetalert'])) {
    ?>
    <script>
        Swal.fire({
            icon: '<?php echo $_SESSION['sweetalert']['icon']; ?>',
            title: '<?php echo $_SESSION['sweetalert']['title']; ?>',
            text: '<?php echo $_SESSION['sweetalert']['text']; ?>'
        });
    </script>
    <?php
    unset($_SESSION['sweetalert']);
}

// Toast Messages
if (isset($_SESSION['success'])) {
    ?>
    <script>
        toastr.success('<?php echo $_SESSION['success']; ?>');
    </script>
    <?php
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    ?>
    <script>
        toastr.error('<?php echo $_SESSION['error']; ?>');
    </script>
    <?php
    unset($_SESSION['error']);
}
?>
