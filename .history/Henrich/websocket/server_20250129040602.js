const WebSocket = require('ws');
const http = require('http');

const server = http.createServer();
const wss = new WebSocket.Server({ server });

const clients = new Map();

wss.on('connection', (ws) => {
    console.log('New client connected');

    ws.on('message', (message) => {
        try {
            const data = JSON.parse(message);
            
            if (data.type === 'auth') {
                clients.set(data.userId, ws);
                ws.userId = data.userId;
                ws.send(JSON.stringify({
                    type: 'auth',
                    status: 'success'
                }));
            }
        } catch (error) {
            console.error('Error processing message:', error);
        }
    });

    ws.on('close', () => {
        if (ws.userId) {
            clients.delete(ws.userId);
        }
        console.log('Client disconnected');
    });
});

server.listen(8080, () => {
    console.log('WebSocket server running on port 8080');
});
