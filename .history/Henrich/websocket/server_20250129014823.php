<?php
require dirname(__DIR__) . '/vendor/autoload.php';
require 'ChatWebSocket.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatWebSocket()
        )
    ),
    8080
);

echo "WebSocket server started on port 8080\n";
$server->run();
