<?php
require_once '../includes/config.php';
require_once '../vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

$webSocketServer = new WebSocketServer($pdo);
$server = IoServer::factory(
    new HttpServer(
        new WsServer($webSocketServer)
    ),
    8080
);

echo "WebSocket server started on port 8080\n";
$server->run();
