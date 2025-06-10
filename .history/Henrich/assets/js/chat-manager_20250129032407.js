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
        this.bindEvents();
        this.loadRecentChats();
        this.startOnlineTracking();
        this.initializeTabs();
        this.setupUnloadHandler();
    }

    // Add all the other methods from the previous ChatManager implementation
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
