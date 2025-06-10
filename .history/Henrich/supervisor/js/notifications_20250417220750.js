/**
 * Notifications JS Module
 * Handles fetching, displaying, and managing notifications
 */

const NotificationManager = {
    // API endpoint path
    apiUrl: '../api/notifications.php',
    
    // Cache for notifications
    notificationsCache: [],
    
    // Initialize notifications
    init: function() {
        this.setupNotificationCounter();
        this.setupNotificationRefresh();
        this.setupNotificationPanel();
        
        // Initial fetch of notification count
        this.fetchNotificationCount();
    },
    
    // Set up the notification counter in the navbar
    setupNotificationCounter: function() {
        // Find notification counter elements
        const notificationCounter = document.querySelector('.notification-counter');
        
        if (notificationCounter) {
            // Add click event to show notification panel
            notificationCounter.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleNotificationPanel();
            });
        }
    },
    
    // Set up automatic refresh of notifications
    setupNotificationRefresh: function() {
        // Fetch count every 60 seconds
        setInterval(() => {
            this.fetchNotificationCount();
        }, 60000);
    },
    
    // Set up notification panel
    setupNotificationPanel: function() {
        // Create notification panel if it doesn't exist
        if (!document.getElementById('notification-panel')) {
            const panel = document.createElement('div');
            panel.id = 'notification-panel';
            panel.className = 'notification-panel';
            panel.innerHTML = `
                <div class="notification-header">
                    <h3>Notifications</h3>
                    <button class="mark-all-read-btn">Mark All as Read</button>
                    <button class="close-btn">&times;</button>
                </div>
                <div class="notification-list"></div>
                <div class="notification-footer">
                    <button class="view-all-btn">View All</button>
                </div>
            `;
            
            document.body.appendChild(panel);
            
            // Add event listeners
            panel.querySelector('.close-btn').addEventListener('click', () => {
                this.toggleNotificationPanel(false);
            });
            
            panel.querySelector('.mark-all-read-btn').addEventListener('click', () => {
                this.markAllAsRead();
            });
            
            // Close panel when clicking outside
            document.addEventListener('click', (e) => {
                const panel = document.getElementById('notification-panel');
                const counter = document.querySelector('.notification-counter');
                
                if (panel && panel.classList.contains('active') && 
                    !panel.contains(e.target) && 
                    (!counter || !counter.contains(e.target))) {
                    this.toggleNotificationPanel(false);
                }
            });
        }
    },
    
    // Toggle notification panel visibility
    toggleNotificationPanel: function(show) {
        const panel = document.getElementById('notification-panel');
        
        if (!panel) return;
        
        if (show === undefined) {
            panel.classList.toggle('active');
        } else {
            if (show) {
                panel.classList.add('active');
            } else {
                panel.classList.remove('active');
            }
        }
        
        // Load notifications when panel is shown
        if (panel.classList.contains('active')) {
            this.fetchNotifications();
        }
    },
    
    // Fetch notification count from API
    fetchNotificationCount: function() {
        fetch(`${this.apiUrl}?action=count`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                this.updateNotificationCounter(data.count);
            })
            .catch(error => {
                console.error('Error fetching notification count:', error);
            });
    },
    
    // Update notification counter in UI
    updateNotificationCounter: function(count) {
        const counter = document.querySelector('.notification-counter');
        
        if (counter) {
            const badge = counter.querySelector('.badge') || document.createElement('span');
            
            if (!counter.querySelector('.badge')) {
                badge.className = 'badge';
                counter.appendChild(badge);
            }
            
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }
    },
    
    // Fetch notifications from API
    fetchNotifications: function(includeRead = false, limit = 10) {
        fetch(`${this.apiUrl}?action=list&include_read=${includeRead ? 1 : 0}&limit=${limit}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                this.notificationsCache = data.notifications || [];
                this.renderNotifications();
            })
            .catch(error => {
                console.error('Error fetching notifications:', error);
            });
    },
    
    // Render notifications in the panel
    renderNotifications: function() {
        const notificationList = document.querySelector('.notification-list');
        
        if (!notificationList) return;
        
        // Clear existing notifications
        notificationList.innerHTML = '';
        
        if (this.notificationsCache.length === 0) {
            notificationList.innerHTML = '<div class="no-notifications">No notifications</div>';
            return;
        }
        
        // Add notifications to the list
        this.notificationsCache.forEach(notification => {
            const notificationItem = document.createElement('div');
            notificationItem.className = `notification-item ${notification.is_read ? 'read' : 'unread'}`;
            notificationItem.setAttribute('data-id', notification.id);
            
            // Determine icon based on severity and type
            let icon = 'info-circle';
            let severityClass = 'info';
            
            switch (notification.severity) {
                case 'warning':
                    icon = 'exclamation-triangle';
                    severityClass = 'warning';
                    break;
                case 'danger':
                    icon = 'exclamation-circle';
                    severityClass = 'danger';
                    break;
                case 'success':
                    icon = 'check-circle';
                    severityClass = 'success';
                    break;
            }
            
            // Format time
            const date = new Date(notification.created_at);
            const timeStr = this.formatNotificationTime(date);
            
            notificationItem.innerHTML = `
                <div class="notification-icon ${severityClass}">
                    <i class="fa fa-${icon}"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">${notification.title}</div>
                    <div class="notification-message">${notification.message}</div>
                    <div class="notification-time">${timeStr}</div>
                </div>
                <div class="notification-actions">
                    <button class="mark-read-btn" title="Mark as read">
                        <i class="fa fa-check"></i>
                    </button>
                    <button class="delete-btn" title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            `;
            
            // Add event listeners
            notificationItem.querySelector('.mark-read-btn').addEventListener('click', (e) => {
                e.stopPropagation();
                this.markAsRead(notification.id);
            });
            
            notificationItem.querySelector('.delete-btn').addEventListener('click', (e) => {
                e.stopPropagation();
                this.deleteNotification(notification.id);
            });
            
            // Add click event to mark as read and navigate if there's a link
            notificationItem.addEventListener('click', () => {
                if (!notification.is_read) {
                    this.markAsRead(notification.id);
                }
                
                // Check if notification has metadata with a link
                if (notification.metadata) {
                    try {
                        const metadata = JSON.parse(notification.metadata);
                        if (metadata.link) {
                            window.location.href = metadata.link;
                        }
                    } catch (e) {
                        console.error('Error parsing notification metadata:', e);
                    }
                }
            });
            
            notificationList.appendChild(notificationItem);
        });
    },
    
    // Format notification time
    formatNotificationTime: function(date) {
        const now = new Date();
        const diffMs = now - date;
        const diffSec = Math.floor(diffMs / 1000);
        const diffMin = Math.floor(diffSec / 60);
        const diffHour = Math.floor(diffMin / 60);
        const diffDay = Math.floor(diffHour / 24);
        
        if (diffDay > 7) {
            return date.toLocaleDateString();
        } else if (diffDay > 0) {
            return `${diffDay} day${diffDay > 1 ? 's' : ''} ago`;
        } else if (diffHour > 0) {
            return `${diffHour} hour${diffHour > 1 ? 's' : ''} ago`;
        } else if (diffMin > 0) {
            return `${diffMin} minute${diffMin > 1 ? 's' : ''} ago`;
        } else {
            return 'Just now';
        }
    },
    
    // Mark a notification as read
    markAsRead: function(notificationId) {
        fetch(this.apiUrl, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'mark_read',
                notification_id: notificationId
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update cache
                const notification = this.notificationsCache.find(n => n.id === notificationId);
                if (notification) {
                    notification.is_read = true;
                }
                
                // Update UI
                const notificationItem = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                if (notificationItem) {
                    notificationItem.classList.remove('unread');
                    notificationItem.classList.add('read');
                }
                
                // Update counter
                this.fetchNotificationCount();
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    },
    
    // Mark all notifications as read
    markAllAsRead: function() {
        fetch(this.apiUrl, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'mark_all_read'
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update cache
                this.notificationsCache.forEach(notification => {
                    notification.is_read = true;
                });
                
                // Update UI
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                    item.classList.add('read');
                });
                
                // Update counter
                this.updateNotificationCounter(0);
            }
        })
        .catch(error => {
            console.error('Error marking all notifications as read:', error);
        });
    },
    
    // Delete a notification
    deleteNotification: function(notificationId) {
        fetch(`${this.apiUrl}?id=${notificationId}`, {
            method: 'DELETE'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update cache
                this.notificationsCache = this.notificationsCache.filter(n => n.id !== notificationId);
                
                // Update UI
                const notificationItem = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                if (notificationItem) {
                    notificationItem.remove();
                }
                
                // Check if list is empty
                if (this.notificationsCache.length === 0) {
                    const notificationList = document.querySelector('.notification-list');
                    if (notificationList) {
                        notificationList.innerHTML = '<div class="no-notifications">No notifications</div>';
                    }
                }
                
                // Update counter if needed
                this.fetchNotificationCount();
            }
        })
        .catch(error => {
            console.error('Error deleting notification:', error);
        });
    }
};

// Add CSS for notifications
function addNotificationStyles() {
    const style = document.createElement('style');
    style.textContent = `
        /* Notification Counter */
        .notification-counter {
            position: relative;
            cursor: pointer;
        }
        
        .notification-counter .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #f44336;
            color: white;
            border-radius: 50%;
            padding: 0.25rem 0.4rem;
            font-size: 0.75rem;
            min-width: 1rem;
            text-align: center;
        }
        
        /* Notification Panel */
        .notification-panel {
            position: fixed;
            top: 60px;
            right: -400px;
            width: 350px;
            max-width: 90vw;
            height: 500px;
            max-height: 80vh;
            background-color: white;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            transition: right 0.3s ease;
            z-index: 1000;
        }
        
        .notification-panel.active {
            right: 10px;
        }
        
        .notification-header {
            padding: 10px 15px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .notification-header h3 {
            margin: 0;
            font-size: 1.1rem;
        }
        
        .notification-header .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #777;
        }
        
        .notification-header .mark-all-read-btn {
            background: none;
            border: none;
            color: #2196F3;
            cursor: pointer;
            font-size: 0.8rem;
        }
        
        .notification-list {
            flex: 1;
            overflow-y: auto;
            padding: 0;
        }
        
        .notification-footer {
            padding: 10px 15px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
        }
        
        .notification-footer .view-all-btn {
            background: none;
            border: none;
            color: #2196F3;
            cursor: pointer;
        }
        
        /* Notification Item */
        .notification-item {
            padding: 10px 15px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .notification-item:hover {
            background-color: #f9f9f9;
        }
        
        .notification-item.unread {
            background-color: #f0f7ff;
        }
        
        .notification-item.unread:hover {
            background-color: #e6f2ff;
        }
        
        .notification-icon {
            flex: 0 0 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            color: white;
        }
        
        .notification-icon.info {
            background-color: #2196F3;
        }
        
        .notification-icon.success {
            background-color: #4CAF50;
        }
        
        .notification-icon.warning {
            background-color: #FFC107;
        }
        
        .notification-icon.danger {
            background-color: #F44336;
        }
        
        .notification-content {
            flex: 1;
        }
        
        .notification-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .notification-message {
            font-size: 0.9rem;
            color: #555;
            margin-bottom: 5px;
        }
        
        .notification-time {
            font-size: 0.75rem;
            color: #888;
        }
        
        .notification-actions {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .notification-actions button {
            background: none;
            border: none;
            color: #888;
            cursor: pointer;
            padding: 3px;
            margin: 2px 0;
            font-size: 0.9rem;
        }
        
        .notification-actions button:hover {
            color: #333;
        }
        
        .no-notifications {
            padding: 20px;
            text-align: center;
            color: #888;
        }
    `;
    
    document.head.appendChild(style);
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    addNotificationStyles();
    NotificationManager.init();
});

// Export for use in other modules
window.NotificationManager = NotificationManager; 