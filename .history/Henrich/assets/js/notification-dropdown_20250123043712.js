class NotificationDropdown {
    constructor() {
        this.btn = document.querySelector('.notification-btn');
        this.dropdown = document.querySelector('.notification-dropdown');
        this.content = document.querySelector('.notification-dropdown-content');
        this.init();
    }

    init() {
        console.log('Initializing notification dropdown');
        this.addEventListeners();
        this.startAutoUpdate();
    }

    addEventListeners() {
        // Toggle dropdown
        this.btn?.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggle();
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!this.dropdown?.contains(e.target)) {
                this.hide();
            }
        });

        // Handle notification clicks
        document.querySelectorAll('.notification-item:not(.empty)').forEach(item => {
            item.addEventListener('click', () => this.markAsRead(item));
        });
    }

    toggle() {
        this.dropdown?.classList.toggle('active');
        console.log('Dropdown toggled:', this.dropdown?.classList.contains('active'));
    }

    hide() {
        this.dropdown?.classList.remove('active');
    }

    async markAsRead(item) {
        const notifId = item.dataset.id;
        if (!notifId) return;

        try {
            const response = await fetch('../includes/mark_notification_read.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: notifId })
            });

            if (response.ok) {
                item.classList.remove('unread');
                this.updateBadge();
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    updateBadge() {
        const badge = this.btn?.querySelector('.badge');
        if (badge) {
            const currentCount = parseInt(badge.textContent) - 1;
            if (currentCount <= 0) {
                badge.remove();
            } else {
                badge.textContent = currentCount;
            }
        }
    }

    startAutoUpdate() {
        setInterval(() => this.checkNewNotifications(), 30000);
    }

    async checkNewNotifications() {
        try {
            const response = await fetch('../includes/get_notification_count.php');
            const data = await response.json();
            console.log('Notification check:', data);
            
            if (data.success && data.count > 0) {
                this.updateNotificationCount(data.count);
            }
        } catch (error) {
            console.error('Error checking notifications:', error);
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new NotificationDropdown();
});
