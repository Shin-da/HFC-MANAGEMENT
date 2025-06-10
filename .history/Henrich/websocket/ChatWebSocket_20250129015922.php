<?php
require 'vendor/autoload.php';
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ChatWebSocket implements MessageComponentInterface {
    protected $clients;
    protected $users = [];

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg);
        
        switch($data->type) {
            case 'register':
                $this->users[$from->resourceId] = $data->userId;
                break;
                
            case 'message':
                $this->broadcastMessage($from, $data);
                break;
                
            case 'typing':
                $this->broadcastTyping($from, $data);
                break;
        }
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

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        unset($this->users[$conn->resourceId]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}
