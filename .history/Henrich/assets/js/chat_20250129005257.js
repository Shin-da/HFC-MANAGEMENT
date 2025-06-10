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
            
            if (data.success) {
                this.updateOnlineUsers(data.users);
            }
        } catch (error) {
            console.error('Error updating online status:', error);
        }
    }

    updateOnlineUsers(users) {
        const onlineUsersContainer = document.getElementById('onlineUsers');
        const onlineCount = document.getElementById('onlineCount');
        
        if (!onlineUsersContainer || !onlineCount) return;

        onlineCount.textContent = users.length;
        
        onlineUsersContainer.innerHTML = users.map(user => `
            <div class="user-item" data-user-id="${user.user_id}">
                <div class="user-avatar">
                    <i class='bx bxs-user-circle'></i>
                    <span class="online-indicator"></span>
                </div>
                <div class="user-info">
                    <span class="user-name">${user.username}</span>
                    <span class="user-role">${user.role}</span>
                </div>
            </div>
        `).join('');

        // Add click handlers