document.addEventListener('DOMContentLoaded', function() {
    initializeNotifications();
    startNotificationPolling();
});

function initializeNotifications() {
    const notificationBtn = document.querySelector('.notification-btn');
    const dropdownContent = document.querySelector('.notification-dropdown-content');

    if (notificationBtn && dropdownContent) {
        // Toggle dropdown
        notificationBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropdownContent.classList.toggle('active');
        });

        // Close when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownContent.contains(e.target) && !notificationBtn.contains(e.target)) {
                dropdownContent.classList.remove('active');
            }
        });

        // Handle notification item clicks
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function() {
                const id = this.dataset.id;
                if (id) markNotificationAsRead(id);
            });
        });
    }
}

function startNotificationPolling() {
    updateNotificationCount();
    // Poll for new notifications every 30 seconds
    setInterval(updateNotificationCount, 30000);
}

function updateNotificationCount() {
    fetch('../includes/get_notification_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateNotificationBadge(data.count.total);
            }
        })
        .catch(error => console.error('Error fetching notifications:', error));
}

function updateNotificationBadge(count) {
    const badge = document.querySelector('.notification-btn .badge');
    if (count > 0) {
        if (badge) {
            badge.textContent = count;
        } else {
            const newBadge = document.createElement('span');
            newBadge.className = 'badge';
            newBadge.textContent = count;
            document.querySelector('.notification-btn').appendChild(newBadge);
        }
    } else if (badge) {
        badge.remove();
    }
}

function markNotificationAsRead(id) {
    fetch('../includes/mark_notification_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector(`.notification-item[data-id="${id}"]`).classList.remove('unread');
            updateNotificationCount();
        }
    })
    .catch(error => console.error('Error marking notification as read:', error));
}
