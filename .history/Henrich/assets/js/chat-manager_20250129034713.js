class ChatManager {
    constructor() {
        this.socket = null;
        this.messageContainer = document.querySelector('.chat-messages');
        this.onlineUsersInterval = null;
        this.activeTab = 'online';
        this.initialize();
    }

    initialize() {
        this.initializeWebSocket();
        this.bindEventListeners();
        this.loadRecentChats();
        this.startOnlineTracking();
        this.initializeTabs();
        this.setupUnloadHandler();
    }

    bindEventListeners() {
        const sendButton = document.querySelector('#sendMessage');
        const messageInput = document.querySelector('#messageInput');

        sendButton?.addEventListener('click', () => this.sendMessage());
        messageInput?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.sendMessage();
        });
    }

    setupUnloadHandler() {
        window.addEventListener('beforeunload', async () => {
            try {
                await fetch(`${BASE_URL}/api/users/offline.php`, {
                    method: 'POST',
                    keepalive: true
                });
            } catch (error) {
                console.error('Error marking user offline:', error);
            }
        });
    }

    startOnlineTracking() {
        this.updateOnlineStatus();
        this.onlineUsersInterval = setInterval(() => {
            this.updateOnlineStatus();
        }, 30000);
    }

    async updateOnlineStatus() {
        const container = document.getElementById('onlineUsers');
        
        try {
            const response = await fetch(`${BASE_URL}/api/users/online.php`);
            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();
            console.log('Online users response:', data);
            
            if (data.success) {
                this.updateUsersList(data.users);
            } else {
                throw new Error(data.error || 'Failed to fetch users');
            }
        } catch (error) {
            console.error('Error updating online status:', error);
            if (container) {
                container.innerHTML = '<div class="error">Failed to load users</div>';
            }
        }
    }

    updateUsersList(users) {
        const container = document.getElementById('onlineUsers');
        const count = document.getElementById('onlineCount');
        
        if (!container || !count) {
            console.error('Required DOM elements not found');
            return;
        }

        count.textContent = users.filter(user => user.is_online).length;

        if (users.length === 0) {
            container.innerHTML = '<div class="no-users">No users available</div>';
            return;
        }

        container.innerHTML = users.map(user => `
            <div class="user-item ${user.is_online ? 'online' : ''}" data-user-id="${user.user_id}">
                <div class="user-avatar">
                    <i class='bx bxs-user-circle'></i>
                    <span class="online-indicator ${user.is_online ? 'active' : ''}"></span>
                </div>
                <div class="user-info">
                    <div class="user-name-role">
                        <span class="user-name">${this.escapeHtml(user.username)}</span>
                        <span class="user-role ${user.role.toLowerCase()}">${this.capitalizeFirst(user.role)}</span>
                    </div>
                    <span class="last-seen">
                        ${user.is_online ? 'Online' : this.formatLastSeen(user.last_online)}
                    </span>
                </div>
            </div>
        `).join('');

        // Add click handlers
        container.querySelectorAll('.user-item').forEach(item => {
            item.addEventListener('click', () => this.openChat(item.dataset.userId));
        });
    }

    formatLastSeen(timestamp) {
        if (!timestamp) return 'Never';
        
        const date = new Date(timestamp);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);

        if (diff < 60) return 'Just now';
        if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
        if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
        return date.toLocaleDateString();
    }

    initializeWebSocket() {
        const protocol = WS_CONFIG.secure ? 'wss' : 'ws';
        const wsUrl = `${protocol}://${WS_CONFIG.host}:${WS_CONFIG.port}`;
        
        this.socket = new WebSocket(wsUrl);
        this.socket.onopen = () => {
            console.log('WebSocket connected');
            this.authenticateWebSocket();
        };
    }

    async loadRecentChats() {
        try {
            const response = await fetch(`${BASE_URL}/api/chat/recent.php`);
            if (!response.ok) throw new Error('Failed to load recent chats');
            
            const data = await response.json();
            if (data.success) {
                this.updateRecentChats(data.chats);
            }
        } catch (error) {
            console.error('Error loading recent chats:', error);
            document.getElementById('recentChats').innerHTML = 
                '<div class="error">Failed to load recent chats</div>';
        }
    }

    updateRecentChats(chats) {
        const container = document.getElementById('recentChats');
        if (!container) return;

        if (!chats.length) {
            container.innerHTML = '<div class="no-chats">No recent conversations</div>';
            return;
        }

        container.innerHTML = chats.map(chat => `
            <div class="chat-item" data-user-id="${chat.user_id}">
                <div class="chat-avatar">
                    <i class='bx bxs-user-circle'></i>
                    <span class="online-indicator ${chat.is_online ? 'active' : ''}"></span>
                </div>
                <div class="chat-info">
                    <div class="chat-name-time">
                        <span class="chat-name">${this.escapeHtml(chat.username)}</span>
                        <span class="chat-time">${this.formatTime(chat.last_message_time)}</span>
                    </div>
                    <div class="chat-preview">
                        ${chat.unread ? '<span class="unread-badge">' + chat.unread + '</span>' : ''}
                        <span class="last-message">${this.escapeHtml(chat.last_message)}</span>
                    </div>
                </div>
            </div>
        `).join('');

        // Add click handlers
        container.querySelectorAll('.chat-item').forEach(item => {
            item.addEventListener('click', () => this.openChat(item.dataset.userId));
        });
    }

    formatTime(timestamp) {
        if (!timestamp) return '';
        const date = new Date(timestamp);
        const now = new Date();
        const diff = now - date;
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));

        if (days > 7) {
            return date.toLocaleDateString();
        } else if (days > 0) {
            return `${days}d ago`;
        } else {
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
    }

    escapeHtml(text) {
        if (!text) return '';
        return text
            .toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    capitalizeFirst(string) {
        if (!string) return '';
        return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
    }

    // ...rest of the ChatManager methods...
}
