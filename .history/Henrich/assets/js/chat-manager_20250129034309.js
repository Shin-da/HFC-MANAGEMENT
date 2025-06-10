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
        try {
            const response = await fetch(`${BASE_URL}/api/users/online.php`);
            const data = await response.json();
            if (data.success) {
                this.updateUsersList(data.users);
            }
        } catch (error) {
            console.error('Error updating online status:', error);
        }
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

    // ...rest of the ChatManager methods...
}
