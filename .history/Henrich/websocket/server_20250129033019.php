<?php
require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/WebSocketServer.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use HFC\WebSocket\WebSocketServer;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new WebSocketServer()
        )
    ),
    8080
);

echo "WebSocket server running on port 8080\n";
$server->run();
