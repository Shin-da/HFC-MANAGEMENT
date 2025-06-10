class CEONotifications {
    constructor() {
        this.container = document.getElementById('notificationCenter');
        this.badge = document.getElementById('notificationBadge');
        this.checkInterval = 60000; // Check every minute
        this.init();
    }

    async init() {
        await this.loadNotifications();
        this.setupEventListeners();
        this.startPeriodicCheck();
    }

    async loadNotifications() {
        try {
            const response = await fetch('/api/ceo/notifications/get.php');
            if (!response.ok) throw new Error('Failed to fetch notifications');
            const data = await response.json();
            this.updateNotificationCenter(data);
            this.updateBadge(data.filter(n => !n.is_read).length);
        } catch (error) {
            console.error('Notification Error:', error);
        }
    }

    updateNotificationCenter(notifications) {
        if (!this.container) return;
        
        this.container.innerHTML = notifications.map(notification => `
            <div class="notification-item ${notification.priority}" data-id="${notification.id}">
                <div class="notification-content">
                    <p>${notification.message}</p>
                    <span class="notification-time">
                        ${this.formatTime(notification.created_at)}
                    </span>
                </div>
                ${notification.action_url ? `
                    <a href="${notification.action_url}" class="notification-action">
                        View Details
                    </a>
                ` : ''}
            </div>
        `).join('');
    }

    startPeriodicCheck() {
        setInterval(() => this.loadNotifications(), this.checkInterval);
    }

    formatTime(timestamp) {
        return new Date(timestamp).toLocaleString();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new CEONotifications();
});
