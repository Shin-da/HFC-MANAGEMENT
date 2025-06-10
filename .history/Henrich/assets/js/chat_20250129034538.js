const WS_CONFIG = {
    host: '127.0.0.1',  // Always connect to localhost for WebSocket
    port: 8080,
    secure: false,
    reconnectAttempts: 3,
    reconnectDelay: 3000
};

const BASE_URL = '/HFC MANAGEMENT/Henrich';

class WebSocketClient {
    constructor() {
        this.connect();
    }

    connect() {
        try {
            const protocol = WS_CONFIG.secure ? 'wss' : 'ws';
            const wsUrl = `${protocol}://${WS_CONFIG.host}:${WS_CONFIG.port}`;
            
            console.log('Connecting to WebSocket:', wsUrl);
            this.socket = new WebSocket(wsUrl);

            this.socket.onopen = () => {
                console.log('WebSocket connected successfully');
                this.authenticate();
            };

            this.socket.onerror = (error) => {
                console.error('WebSocket error:', error);
            };

            this.socket.onclose = () => {
                console.log('WebSocket connection closed');
                this.reconnect();
            };

        } catch (error) {
            console.error('Failed to create WebSocket connection:', error);
            this.reconnect();
        }
    }

    reconnect() {
        if (this.reconnectAttempts < WS_CONFIG.reconnectAttempts) {
            this.reconnectAttempts++;
            console.log(`Reconnecting... Attempt ${this.reconnectAttempts}`);
            setTimeout(() => this.connect(), WS_CONFIG.reconnectDelay);
        }
    }

    authenticate() {
        if (this.socket.readyState === WebSocket.OPEN) {
            this.socket.send(JSON.stringify({
                type: 'auth',
                userId: currentUser.id,
                role: currentUser.role
            }));
        }
    }
}