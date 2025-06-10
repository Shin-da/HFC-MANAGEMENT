<?php
namespace HFC\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Exception;

class WebSocketServer implements MessageComponentInterface {
    protected $clients;
    protected $users = [];

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        echo "WebSocket server initialized\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg);
        if (!$data) return;

        switch ($data->type ?? '') {
            case 'auth':
                $this->users[$from->resourceId] = $data->userId;
                echo "User {$data->userId} authenticated\n";
                break;
            
            case 'message':
                $this->broadcastMessage($from, $data);
                break;

            case 'typing':
                $this->broadcastTyping($from, $data);
                break;
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        unset($this->users[$conn->resourceId]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }

    protected function broadcastMessage($from, $data) {
        foreach ($this->clients as $client) {
            if ($this->users[$client->resourceId] == $data->receiverId) {
                $client->send(json_encode([
                    'type' => 'message',
                    'senderId' => $this->users[$from->resourceId],
                    'message' => $data->message,
                    'timestamp' => time()
                ]));
            }
        }
    }

    protected function broadcastTyping($from, $data) {
        foreach ($this->clients as $client) {
            if ($this->users[$client->resourceId] == $data->receiverId) {
                $client->send(json_encode([
                    'type' => 'typing',
                    'senderId' => $this->users[$from->resourceId],
                    'isTyping' => $data->isTyping ?? true
                ]));
            }
        }
    }
}
