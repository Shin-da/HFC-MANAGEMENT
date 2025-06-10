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

    // ...rest of the ChatManager methods...
}
