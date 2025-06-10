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

    initNotifications();
});

function initNotifications() {
    const notificationBtn = document.querySelector('.notification-btn');
    const notificationDropdown = document.querySelector('.notification-dropdown');

    if (notificationBtn) {
        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationDropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.remove('active');
            }
        });

        // Handle notification read status
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', async () => {
                const notifId = item.dataset.id;
                if (notifId) {
                    try {
                        const response = await fetch('../includes/mark_notification_read.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ id: notifId })
                        });

                        if (response.ok) {
                            item.classList.remove('unread');
                            updateNotificationBadge();
                        }
                    } catch (error) {
                        console.error('Error marking notification as read:', error);
                    }
                }
            });
        });
    }
}

function updateNotificationBadge() {
    const badge = document.querySelector('.notification-btn .badge');
    const unreadCount = document.querySelectorAll('.notification-item.unread').length;
    
    if (badge) {
        if (unreadCount > 0) {
            badge.textContent = unreadCount;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
}
