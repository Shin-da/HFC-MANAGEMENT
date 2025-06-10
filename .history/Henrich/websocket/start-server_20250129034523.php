<?php
require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/WebSocketServer.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use HFC\WebSocket\WebSocketServer;

try {
    echo "Starting WebSocket server...\n";
    
    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new WebSocketServer()
            )
        ),
        8080,
        '0.0.0.0'  // Allow connections from any IP
    );

    echo "WebSocket server running on 0.0.0.0:8080\n";
    echo "Press Ctrl+C to stop\n";
    
    $server->run();
} catch (Exception $e) {
    echo "Error starting server: " . $e->getMessage() . "\n";
    exit(1);
}
