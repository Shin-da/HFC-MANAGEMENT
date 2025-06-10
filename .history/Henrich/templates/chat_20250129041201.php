<!DOCTYPE html>
<html>
<head>
    <!-- ...existing head content... -->
    
    <!-- Add config.js before chat-manager.js -->
    <script src="<?php echo BASE_URL; ?>/assets/js/config.js"></script>
    <script>
        // Add global user info
        const currentUser = {
            id: <?php echo $_SESSION['user_id']; ?>,
            token: '<?php echo $_SESSION['token']; ?>',
            username: '<?php echo $_SESSION['username']; ?>
        };
    </script>
    <script src="<?php echo BASE_URL; ?>/assets/js/chat-manager.js"></script>
</head>
<!-- ...rest of the file... -->
