const WS_CONFIG = {
    host: '127.0.0.1',
    port: 8080,
    secure: false // Set to true if using SSL/TLS
};

// Add BASE_URL constant
const BASE_URL = '/HFC MANAGEMENT/Henrich';

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
        this.initWebSocket();
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
            const response = await fetch(`${BASE_URL}/api/chat/send.php`, {
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
            const response = await fetch(`${BASE_URL}/api/chat/recent.php`);
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
        const container = document.getElementById('onlineUsers');
        
        try {
            const response = await fetch(`${BASE_URL}/api/users/online.php`);
            const data = await response.json();
            
            console.log('Online users response:', data); // Debug log
            
            if (data.success && Array.isArray(data.users)) {
                const onlineUsers = data.users.filter(user => user.is_online);
                const onlineCount = document.getElementById('onlineCount');
                if (onlineCount) {
                    onlineCount.textContent = onlineUsers.length;
                }

                container.innerHTML = data.users.map(user => `
                    <div class="user-item ${user.is_online ? 'online' : ''}" data-user-id="${user.user_id}">
                        <div class="user-avatar">
                            <i class='bx bxs-user-circle'></i>
                            <span class="online-indicator ${user.is_online ? 'active' : ''}"></span>
                        </div>
                        <div class="user-info">
                            <div class="user-name-role">
                                <span class="user-name">${user.username}</span>
                                <span class="user-role ${user.role.toLowerCase()}">${user.role}</span>
                            </div>
                            <span class="last-seen">
                                ${user.is_online ? 'Online' : this.getLastSeenTime(user.last_online)}
                            </span>
                        </div>
                    </div>
                `).join('');

                // Add click handlers
                container.querySelectorAll('.user-item').forEach(item => {
                    item.addEventListener('click', () => this.openChat(item.dataset.userId));
                });
            }
        } catch (error) {
            console.error('Error updating online status:', error);

        if (users.length === 0) {
            container.innerHTML = '<div class="no-users">No users available</div>';
            return;
        }

        container.innerHTML = users.map(user => `
            <div class="user-item" data-user-id="${user.user_id}">
                <div class="user-avatar">
                    <i class='bx bxs-user-circle'></i>
                    <span class="online-indicator ${user.is_active ? 'active' : ''}"></span>
                </div>
                <div class="user-info">
                    <div class="user-name-role">
                        <span class="user-name">
                            ${this.escapeHtml(user.first_name)} ${this.escapeHtml(user.last_name)}
                            <small>(${this.escapeHtml(user.username)})</small>
                        </span>
                        <span class="user-role ${user.role.toLowerCase()}">
                            ${this.capitalizeFirst(user.role)}
                            ${user.department ? ` - ${user.department}` : ''}
                        </span>
                    </div>
                    <span class="last-seen">
                        ${user.is_active ? 'Online' : this.getLastSeenTime(user.last_online)}
                    </span>
                </div>
            </div>
        `).join('');

        // Add click handlers
        container.querySelectorAll('.user-item').forEach(item => {
            item.addEventListener('click', () => this.openChat(item.dataset.userId));
        });
    }

    escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    capitalizeFirst(string) {
        return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
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

    initWebSocket() {
        const protocol = WS_CONFIG.secure ? 'wss' : 'ws';
        const wsUrl = `${protocol}://${WS_CONFIG.host}:${WS_CONFIG.port}`;
        
        this.socket = new WebSocket(wsUrl);
        this.socket.onopen = () => {
            console.log('WebSocket connected');
            this.authenticateWebSocket();
        };
    }

    authenticateWebSocket() {
        this.socket.send(JSON.stringify({
            type: 'auth',
            userId: currentUser.id,
            role: currentUser.role
        }));
    }

    async openChat(userId) {
        try {
            // Update UI state
            this.currentChatUser = userId;
            document.getElementById('messageInput').disabled = false;
            document.getElementById('sendMessage').disabled = false;

            // Load user info
            const userInfo = await this.loadUserInfo(userId);
            this.updateChatHeader(userInfo);

            // Load messages
            await this.loadMessages(userId);
            
            // Show chat view
            document.querySelector('.chat-welcome').style.display = 'none';
            document.querySelector('.chat-conversation').style.display = 'flex';
            
            // Update read status
            this.markMessagesAsRead(userId);
            
        } catch (error) {
            console.error('Error opening chat:', error);
        }
    }

    async loadUserInfo(userId) {
        const response = await fetch(`${BASE_URL}/api/users/info.php?user_id=${userId}`);
        if (!response.ok) throw new Error('Failed to load user info');
        return response.json();
    }

    updateChatHeader(userInfo) {
        const header = document.querySelector('.user-info');
        header.querySelector('.user-name').textContent = userInfo.username;
        header.querySelector('.user-status').className = 
            `user-status ${userInfo.is_online ? 'online' : ''}`;
    }

    async markMessagesAsRead(userId) {
        try {
            await fetch(`${BASE_URL}/api/chat/mark-read.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ sender_id: userId })
            });
        } catch (error) {
            console.error('Error marking messages as read:', error);
        }
    }

    async loadMessages(userId) {
        try {
            const response = await fetch(`/api/chat/messages.php?user_id=${userId}`);
            const data = await response.json();
            
            if (!data.success) throw new Error(data.error);
            
            this.displayMessages(data.messages);
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }

    displayMessages(messages) {
        const container = document.getElementById('messagesContainer');
        container.innerHTML = messages.map(msg => this.createMessageElement(msg)).join('');
        container.scrollTop = container.scrollHeight;
    }

    createMessageElement(message) {
        const isMine = message.sender_id == currentUser.id;
        return `
            <div class="message ${isMine ? 'sent' : 'received'}">
                <div class="message-content">
                    <p>${this.escapeHtml(message.message)}</p>
                    <span class="message-time">
                        ${this.formatMessageTime(message.created_at)}
                    </span>
                </div>
            </div>
        `;
    }

    formatMessageTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
}

// Initialize chat when document is ready
document.addEventListener('DOMContentLoaded', () => {
    window.chatManager = new ChatManager();
});
