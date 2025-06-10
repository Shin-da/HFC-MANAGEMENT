class ChatManager {
    constructor() {
        this.socket = null;
        this.messageContainer = document.querySelector('.chat-messages');
        this.onlineUsersInterval = null;
        this.activeTab = 'online';
        this.init();
    }

    init() {
        this.initializeWebSocket();
        this.bindEvents();
        this.loadRecentChats();
        this.startOnlineTracking();
        this.initializeTabs();
    }

    initializeWebSocket() {
        this.socket = new WebSocket(`ws://${window.location.hostname}:8080`);
        
        this.socket.onmessage = (event) => {
            const data = JSON.parse(event.data);
            this.handleIncomingMessage(data);
        };
    }

    bindEvents() {
        const sendButton = document.querySelector('#sendMessage');
        const messageInput = document.querySelector('#messageInput');

        sendButton?.addEventListener('click', () => this.sendMessage());
        messageInput?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.sendMessage();
        });
    }

    async sendMessage() {
        const input = document.querySelector('#messageInput');
        const receiverId = document.querySelector('#receiverId').value;
        
        if (!input.value.trim()) return;

        try {
            const response = await fetch('/api/chat/send', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    receiverId,
                    message: input.value
                })
            });

            if (!response.ok) throw new Error('Failed to send message');

            input.value = '';
            this.loadMessages(receiverId);
        } catch (error) {
            console.error('Error sending message:', error);
        }
    }

    async loadRecentChats() {
        try {
            const response = await fetch('/api/chat/recent');
            const chats = await response.json();
            this.updateChatList(chats);
        } catch (error) {
            console.error('Error loading chats:', error);
        }
    }

    updateChatList(chats) {
        const chatList = document.querySelector('.chat-list');
        if (!chatList) return;

        chatList.innerHTML = chats.map(chat => `
            <div class="chat-item" data-user-id="${chat.user_id}">
                <div class="chat-avatar">
                    <i class='bx bxs-user-circle'></i>
                </div>
                <div class="chat-info">
                    <span class="chat-name">${chat.username}</span>
                    <span class="chat-preview">${chat.last_message}</span>
                </div>
                ${chat.unread ? '<span class="unread-badge"></span>' : ''}
            </div>
        `).join('');
    }

    handleIncomingMessage(data) {
        if (data.type === 'message') {
            this.addMessageToChat(data);
            this.updateNotificationBadge();
        }
    }

    startOnlineTracking() {
        // Update online status immediately
        this.updateOnlineStatus();
        
        // Update every 30 seconds
        this.onlineUsersInterval = setInterval(() => {
            this.updateOnlineStatus();
        }, 30000);
    }

    async updateOnlineStatus() {
        try {
            const response = await fetch('/api/users/online.php');
            const data = await response.json();
            
            // Debug log
            console.log('Online users response:', data);
            
            if (data.success) {
                this.updateOnlineUsers(data.users);
                this.pingOnlineStatus();
            } else {
                console.error('Failed to fetch online users:', data.error);
            }
        } catch (error) {
            console.error('Error updating online status:', error);
        }
    }

    async pingOnlineStatus() {
        try {
            await fetch('/api/users/update-status.php', {
                method: 'POST'
            });
        } catch (error) {
            console.error('Error updating online status:', error);
        }
    }

    updateOnlineUsers(users) {
        const onlineUsersContainer = document.getElementById('onlineUsers');
        const onlineCount = document.getElementById('onlineCount');
        
        if (!onlineUsersContainer || !onlineCount) {
            console.error('Required DOM elements not found');
            return;
        }

        // Debug log
        console.log(`Updating users list with ${users.length} users`);

        onlineCount.textContent = users.length;

        if (users.length === 0) {
            onlineUsersContainer.innerHTML = '<div class="no-users">No users online</div>';
            return;
        }
        
        onlineUsersContainer.innerHTML = users.map(user => `
        `).join('');

        // Add click handlers
        onlineUsersContainer.querySelectorAll('.user-item').forEach(item => {
            item.addEventListener('click', () => {
                this.openChat(item.dataset.userId);
            });
        });
    }

    getLastSeenTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000); // difference in seconds

        if (diff < 60) return 'Just now';
        if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
        if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
        return date.toLocaleDateString();
    }

    initializeTabs() {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                this.switchTab(btn.dataset.tab);
            });
        });
    }

    switchTab(tab) {
        this.activeTab = tab;
        
        // Update tab buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.tab === tab);
        });
        
        // Update content visibility
        document.getElementById('onlineUsers').classList.toggle('active', tab === 'online');
        document.getElementById('recentChats').classList.toggle('active', tab === 'recent');
        
        // Load appropriate content
        if (tab === 'online') {
            this.updateOnlineStatus();
        } else {
            this.loadRecentChats();
        }
    }
}

// Initialize chat when document is ready
document.addEventListener('DOMContentLoaded', () => {
    window.chatManager = new ChatManager();
});
