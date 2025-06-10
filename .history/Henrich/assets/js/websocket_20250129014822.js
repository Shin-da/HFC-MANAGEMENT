class WebSocketClient {
    constructor(userId) {
        this.userId = userId;
        this.callbacks = {};
        this.connect();
    }

    connect() {
        this.ws = new WebSocket(`ws://${window.location.hostname}:8080`);
        
        this.ws.onopen = () => {
            console.log('Connected to WebSocket');
            this.register();
        };

        this.ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            if (this.callbacks[data.type]) {
                this.callbacks[data.type](data);
            }
        };

        this.ws.onclose = () => {
            console.log('Disconnected from WebSocket');
            setTimeout(() => this.connect(), 3000);
        };
    }

    register() {
        this.send({
            type: 'register',
            userId: this.userId
        });
    }

    send(data) {
        if (this.ws.readyState === WebSocket.OPEN) {
            this.ws.send(JSON.stringify(data));
        }
    }

    on(type, callback) {
        this.callbacks[type] = callback;
    }
}
