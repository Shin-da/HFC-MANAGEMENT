<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/Chat/WebSocketServer.php';
require __DIR__ . '/config/websocket.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Chat\WebSocketServer;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new WebSocketServer()
        )
    ),
    WS_PORT,
    WS_HOST
);

echo "WebSocket server started on " . WS_HOST . ":" . WS_PORT . "\n";
$server->run();
