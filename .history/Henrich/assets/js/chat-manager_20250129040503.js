class ChatManager {
    constructor() {
        this.socket = null;
        this.messageContainer = document.querySelector('.chat-messages');
        this.onlineUsersInterval = null;
        this.activeTab = 'online';
        this.wsReconnectAttempts = 0;
        this.wsMaxReconnectAttempts = 5;
        this.wsReconnectDelay = 3000;
        this.isConnecting = false;
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
        if (this.isConnecting) return;
        this.isConnecting = true;

        try {
            const protocol = WS_CONFIG.secure ? 'wss' : 'ws';
            const wsUrl = `${protocol}://${WS_CONFIG.host}:${WS_CONFIG.port}`;
            
            this.socket = new WebSocket(wsUrl);

            this.socket.onopen = () => {
                console.log('WebSocket connected');
                this.wsReconnectAttempts = 0;
                this.isConnecting = false;
                this.authenticateWebSocket();
            };

            this.socket.onclose = (event) => {
                console.log('WebSocket disconnected:', event.code, event.reason);
                this.isConnecting = false;
                this.handleWebSocketReconnection();
            };

            this.socket.onerror = (error) => {
                console.error('WebSocket error:', error);
                this.isConnecting = false;
            };

        } catch (error) {
            console.error('WebSocket initialization error:', error);
            this.isConnecting = false;
            this.handleWebSocketReconnection();
        }
    }

    handleWebSocketReconnection() {
        if (this.wsReconnectAttempts >= this.wsMaxReconnectAttempts) {
            console.error('Max reconnection attempts reached');
            return;
        }

        this.wsReconnectAttempts++;
        console.log(`Attempting to reconnect (${this.wsReconnectAttempts}/${this.wsMaxReconnectAttempts})...`);
        
        setTimeout(() => {
            this.initializeWebSocket();
        }, this.wsReconnectDelay * this.wsReconnectAttempts);
    }

    authenticateWebSocket() {
        if (this.socket?.readyState === WebSocket.OPEN) {
            this.socket.send(JSON.stringify({
                type: 'auth',
                userId: currentUser.id,
                token: currentUser.token // Make sure this is available
            }));
        }
    }

    async loadRecentChats() {
        try {
            const response = await fetch(`${BASE_URL}/api/chat/recent.php`);
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

    async openChat(userId) {
        try {
            const chatMain = document.querySelector('.chat-main');
            if (!chatMain) {
                throw new Error('Chat main container not found');
            }

            // Show loading state
            chatMain.innerHTML = `
                <div class="chat-loading">
                    <div class="spinner"></div>
                    <span>Loading chat...</span>
                </div>
            `;

            // Get user info
            const response = await fetch(`${BASE_URL}/api/users/info.php?user_id=${userId}`);
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.error || 'Failed to load user info');
            }

            // Recreate chat interface
            chatMain.innerHTML = `
                <div id="chatHeader" class="chat-header">
                    <div class="user-info">
                        <span class="user-name">${this.escapeHtml(data.user.username)}</span>
                        <span class="user-status ${data.user.is_online ? 'online' : ''}"></span>
                    </div>
                </div>
                <div class="messages-container"></div>
                <div class="chat-input-area">
                    <input type="text" id="messageInput" placeholder="Type a message..." data-receiver-id="${userId}">
                    <button id="sendMessage">
                        <i class='bx bx-send'></i>
                    </button>
                </div>
            `;

            // Validate new elements after recreation
            const newMessageInput = document.querySelector('#messageInput');
            const newSendButton = document.querySelector('#sendMessage');
            const messagesContainer = document.querySelector('.messages-container');

            if (!newMessageInput || !newSendButton || !messagesContainer) {
                throw new Error('Failed to initialize chat interface');
            }

            // Load chat history
            await this.loadMessages(userId);

            // Enable input
            newMessageInput.disabled = false;
            newSendButton.disabled = false;
            newMessageInput.focus();

            // Update active chat user
            this.currentChatUser = userId;

            // Mark messages as read
            await this.markMessagesAsRead(userId);

        } catch (error) {
            console.error('Error opening chat:', error);
            if (document.querySelector('.chat-main')) {
                document.querySelector('.chat-main').innerHTML = `
                    <div class="chat-error">
                        <i class='bx bx-error-circle'></i>
                        <p>Failed to open chat: ${error.message}</p>
                    </div>
                `;
            }
        }
    }

    async loadMessages(userId) {
        try {
            const response = await fetch(`${BASE_URL}/api/chat/messages.php?user_id=${userId}`);
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.error || 'Failed to load messages');
            }

            const container = document.querySelector('.messages-container');
            container.innerHTML = data.messages.map(msg => this.createMessageElement(msg)).join('');
            container.scrollTop = container.scrollHeight;

        } catch (error) {
            console.error('Error loading messages:', error);
            throw error;
        }
    }

    createMessageElement(message) {
        const isMine = message.sender_id === currentUser.id;
        return `
            <div class="message ${isMine ? 'sent' : 'received'}">
                <div class="message-content">
                    <p>${this.escapeHtml(message.message)}</p>
                    <span class="message-time">${this.formatTime(message.created_at)}</span>
                </div>
            </div>
        `;
    }

    async markMessagesAsRead(userId) {
        try {
            await fetch(`${BASE_URL}/api/chat/mark-read.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    sender_id: userId
                })
            });
        } catch (error) {
            console.error('Error marking messages as read:', error);
        }
    }

    // ...rest of the ChatManager methods...
}
