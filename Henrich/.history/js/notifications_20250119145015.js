$(document).ready(function() {
    // Mark notification as read when clicked
    $('.notification-list .unread').click(function() {
        const notifId = $(this).data('id');
        $.post('mark_notification_read.php', { id: notifId }, function(response) {
            if (response.success) {
                updateNotificationCount();
            }
        });
    });

    // Update notification count
    function updateNotificationCount() {
        $.get('get_notification_count.php', function(response) {
            const count = parseInt(response.count);
            const badge = $('.notification-dropdown .badge');
            
            if (count > 0) {
                if (badge.length) {
                    badge.text(count);
                } else {
                    $('.notification-dropdown .nav-link').append(`<span class="badge">${count}</span>`);
                }
            } else {
                badge.remove();
            }
        });
    }
});
