class ChatManager {
    constructor() {
        this.socket = null;
        this.messageContainer = document.querySelector('.chat-messages');
        this.init();
    }

    init() {
        this.initializeWebSocket();
        this.bindEvents();
        this.loadRecentChats();
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