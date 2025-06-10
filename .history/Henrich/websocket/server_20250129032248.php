<?php
require_once '../includes/config.php';
require_once '../vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;

class WebSocketServer implements MessageComponentInterface {
    protected $clients;
    protected $pdo;

    public function __construct($pdo) {
        $this->clients = new \SplObjectStorage;
        $this->pdo = $pdo;
    }

    public function onOpen(\Ratchet\ConnectionInterface $conn) {
        $this->clients->attach($conn);
    }

    public function onMessage(\Ratchet\ConnectionInterface $from, $msg) {
        foreach ($this->clients as $client) {
            $client->send($msg);
        }
    }

    public function onClose(\Ratchet\ConnectionInterface $conn) {
        $this->clients->detach($conn);
    }

    public function onError(\Ratchet\ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }
}

$webSocketServer = new WebSocketServer($pdo);
$server = IoServer::factory(
    new HttpServer(
        new WsServer($webSocketServer)
    ),
    8080
);

echo "WebSocket server started on port 8080\n";
$server->run();
