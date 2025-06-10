// Add this in the HTML head section
<script src="path/to/sweetalert2.min.js"></script>
<script src="path/to/toastr.min.js"></script>

// ...existing code...

<script>
// Form submission handler
$('form').on('submit', function(e) {
    e.preventDefault();
    
    toastr.info('Processing...', 'Please wait');
    
    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            toastr.success('Operation completed successfully');
            // Handle success (refresh/redirect if needed)
        },
        error: function(xhr) {
            toastr.error('An error occurred');
        }
    });
});

// Check for SweetAlert messages from PHP session
<?php if (isset($_SESSION['sweetalert'])): ?>
Swal.fire({
    icon: '<?php echo $_SESSION['sweetalert']['icon']; ?>',
    title: '<?php echo $_SESSION['sweetalert']['title']; ?>',
    text: '<?php echo $_SESSION['sweetalert']['text']; ?>'
});
<?php unset($_SESSION['sweetalert']); endif; ?>
</script>
