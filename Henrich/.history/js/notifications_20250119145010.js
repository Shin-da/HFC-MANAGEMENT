$(document).ready(function() {
    // Mark notification as read when clicked
    $('.notification-list .unread').click(function() {
        const notifId = $(this).data('id');
        $.post('mark_notification_read.php', { id: notifId }, function(response) {
            if (response.success) {
                updateNotificationCount();