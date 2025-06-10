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
        this.useFallback = false;
        this.pollingInterval = null;
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
        // Remove existing event listeners if any
        document.body.removeEventListener('click', this.handleSendClick);
        document.body.removeEventListener('keypress', this.handleEnterPress);

        // Add event listeners using event delegation
        document.body.addEventListener('click', e => {
            if (e.target.id === 'sendMessage' || e.target.closest('#sendMessage')) {
                this.sendMessage();
            }
        });

        document.body.addEventListener('keypress', e => {
            if (e.key === 'Enter' && e.target.id === 'messageInput') {
                e.preventDefault();
                this.sendMessage();
            }
        });
    }

    async sendMessage() {
        const messageInput = document.querySelector('#messageInput');
        if (!messageInput || !messageInput.value.trim() || !this.currentChatUser) {
            return;
        }

        const message = messageInput.value.trim();
        
        try {
            // Log request details
            console.log('Sending message:', {
                to: this.currentChatUser,
                message: message,
                url: this.getApiUrl('api/chat/send.php')
            });

            const response = await fetch(this.getApiUrl('api/chat/send.php'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    receiver_id: this.currentChatUser,
                    message: message
                })
            });

            // Log raw response
            const responseText = await response.text();
            console.log('Raw server response:', responseText);

            // Try to parse JSON
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (e) {
                console.error('JSON Parse Error:', e);
                throw new Error('Invalid server response');
            }

            if (!data.success) {
                throw new Error(data.error || 'Failed to send message');
            }

            // Message sent successfully
            messageInput.value = '';
            
            this.addMessageToUI({
                id: data.message_id,
                message: message,
                sender_id: currentUser.id,
                created_at: data.timestamp
            });

            this.scrollToBottom(true);

        } catch (error) {
            console.error('Send Message Error:', error);
            this.showError(`Failed to send message: ${error.message}`);
        }
    }

    showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'message-error';
        errorDiv.textContent = message;
        document.querySelector('.messages-container')?.appendChild(errorDiv);
        setTimeout(() => errorDiv.remove(), 3000);
    }

    showTypingIndicator(isTyping) {
        const container = document.querySelector('.messages-container');
        const existingIndicator = document.querySelector('.typing-indicator');
        
        if (isTyping && !existingIndicator) {
            container.insertAdjacentHTML('beforeend', `
                <div class="typing-indicator">
                    <span>Typing</span>
                    <div class="typing-dots">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            `);
        } else if (!isTyping && existingIndicator) {
            existingIndicator.remove();
        }
    }

    scrollToBottom(smooth = false) {
        const container = document.querySelector('.messages-container');
        if (!container) return;

        container.scrollTo({
            top: container.scrollHeight,
            behavior: smooth ? 'smooth' : 'auto'
        });
    }

    initializeScrollButton() {
        const container = document.querySelector('.messages-container');
        const button = document.createElement('button');
        button.className = 'scroll-bottom';
        button.innerHTML = '<i class="bx bx-chevron-down"></i>';
        
        container.parentElement.appendChild(button);

        button.addEventListener('click', () => this.scrollToBottom(true));

        container.addEventListener('scroll', () => {
            const shouldShow = container.scrollHeight - container.scrollTop > container.clientHeight + 100;
            button.classList.toggle('visible', shouldShow);
        });
    }

    addMessageToUI(message) {
        const container = document.querySelector('.messages-container');
        if (!container) return;

        const messageElement = this.createMessageElement(message);
        container.insertAdjacentHTML('beforeend', messageElement);
        container.scrollTop = container.scrollHeight;
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
            // Fix double slash in URL
            const url = `${BASE_URL.replace(/\/+$/, '')}/api/users/online.php`;
            const response = await fetch(url);
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
                this.useFallback = false;
                this.authenticateWebSocket();
            };

            this.socket.onclose = (event) => {
                console.log('WebSocket disconnected:', event.code, event.reason);
                this.isConnecting = false;
                if (WS_CONFIG.fallbackToPolling && !this.useFallback) {
                    this.initializePolling();
                } else {
                    this.handleWebSocketReconnection();
                }
            };

            this.socket.onerror = (error) => {
                console.error('WebSocket error:', error);
                this.isConnecting = false;
                if (WS_CONFIG.fallbackToPolling && !this.useFallback) {
                    this.initializePolling();
                }
            };

        } catch (error) {
            console.error('WebSocket initialization error:', error);
            this.isConnecting = false;
            if (WS_CONFIG.fallbackToPolling) {
                this.initializePolling();
            } else {
                this.handleWebSocketReconnection();
            }
        }
    }

    initializePolling() {
        this.useFallback = true;
        console.log('Falling back to polling');
        
        // Clear any existing polling interval
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
        }

        // Start polling
        this.pollMessages();
        this.pollingInterval = setInterval(() => {
            this.pollMessages();
        }, WS_CONFIG.pollingInterval);
    }

    async pollMessages() {
        if (this.currentChatUser) {
            await this.loadMessages(this.currentChatUser);
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
            // Fix double slash in URL
            const url = `${BASE_URL.replace(/\/+$/, '')}/api/chat/recent.php`;
            const response = await fetch(url);
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
            const response = await fetch(this.getApiUrl(`api/users/info.php?user_id=${userId}`));
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

            // Initialize scroll button
            this.initializeScrollButton();
            
            // Add input handlers for typing indicator
            const messageInput = document.querySelector('#messageInput');
            let typingTimeout;
            
            messageInput?.addEventListener('input', () => {
                if (this.socket?.readyState === WebSocket.OPEN) {
                    this.socket.send(JSON.stringify({
                        type: 'typing',
                        receiver_id: userId,
                        isTyping: true
                    }));
                    
                    clearTimeout(typingTimeout);
                    typingTimeout = setTimeout(() => {
                        this.socket.send(JSON.stringify({
                            type: 'typing',
                            receiver_id: userId,
                            isTyping: false
                        }));
                    }, 1000);
                }
            });

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
            const response = await fetch(this.getApiUrl(`api/chat/messages.php?user_id=${userId}`));
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
            await fetch(this.getApiUrl('api/chat/mark-read.php'), {
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

    initializeTabs() {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const usersList = document.getElementById('onlineUsers');
        const recentList = document.getElementById('recentChats');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons and lists
                tabButtons.forEach(btn => btn.classList.remove('active'));
                usersList.classList.remove('active');
                recentList.classList.remove('active');

                // Add active class to clicked button and corresponding list
                button.classList.add('active');
                const tabName = button.dataset.tab;
                if (tabName === 'online') {
                    usersList.classList.add('active');
                    this.updateOnlineStatus();
                } else if (tabName === 'recent') {
                    recentList.classList.add('active');
                    this.loadRecentChats();
                }
            });
        });
    }

    // Add helper method for URL construction
    getApiUrl(endpoint) {
        return `${BASE_URL.replace(/\/+$/, '')}/${endpoint.replace(/^\/+/, '')}`;
    }

    // ...rest of the ChatManager methods...
}
