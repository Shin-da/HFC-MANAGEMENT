$(document).ready(function() {
    // Check for new notifications every 30 seconds
    setInterval(updateNotificationCount, 30000);

    // Handle notification click
    $(document).on('click', '.notification-list .unread', function() {
        const notifId = $(this).data('id');
        const $notif = $(this);
        
        $.post('mark_notification_read.php', { id: notifId })
            .done(function(response) {
                if (response.success) {
                    $notif.removeClass('unread').addClass('read');
                    updateNotificationCount();
                }
            });
    });

    // Update notification badge
    function updateNotificationCount() {
        $.get('get_notification_count.php')
            .done(function(response) {
                const count = parseInt(response.count);
                const $badge = $('.notification-dropdown .badge');
                const $icon = $('.notification-dropdown .fa-bell');
                
                if (count > 0) {
                    if ($badge.length) {
                        $badge.text(count);
                    } else {
                        $icon.after(`<span class="badge">${count}</span>`);
                    }
                } else {
                    $badge.remove();
                }
            });
    }
});
