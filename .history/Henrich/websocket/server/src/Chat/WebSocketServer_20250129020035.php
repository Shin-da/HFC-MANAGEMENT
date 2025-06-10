<?php
namespace Chat;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use SplObjectStorage;

class WebSocketServer implements MessageComponentInterface
{
    protected $clients;
    protected $users = [];

    public function __construct() {
        $this->clients = new SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg);
        
        switch($data->type ?? '') {
            case 'auth':
                $this->authenticateUser($from, $data);
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
        if (isset($this->users[$conn->resourceId])) {
            unset($this->users[$conn->resourceId]);
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    protected function authenticateUser(ConnectionInterface $conn, $data) {
        if (isset($data->userId)) {
            $this->users[$conn->resourceId] = [
                'userId' => $data->userId,
                'role' => $data->role ?? 'user'
            ];
            echo "User {$data->userId} authenticated\n";
        }
    }

    protected function broadcastMessage(ConnectionInterface $from, $data) {
        $sender = $this->users[$from->resourceId] ?? null;
        if (!$sender || !isset($data->receiverId, $data->message)) {
            return;
        }

        $message = [
            'type' => 'message',
            'senderId' => $sender['userId'],
            'message' => $data->message,
            'timestamp' => time()
        ];

        foreach ($this->clients as $client) {
            if (isset($this->users[$client->resourceId]) && 
                $this->users[$client->resourceId]['userId'] == $data->receiverId) {
                $client->send(json_encode($message));
                break;
            }
        }
    }

    protected function broadcastTyping(ConnectionInterface $from, $data) {
        $sender = $this->users[$from->resourceId] ?? null;
        if (!$sender || !isset($data->receiverId)) {
            return;
        }

        $message = [
            'type' => 'typing',
            'senderId' => $sender['userId'],
            'isTyping' => $data->isTyping ?? true
        ];

        foreach ($this->clients as $client) {
            if (isset($this->users[$client->resourceId]) && 
                $this->users[$client->resourceId]['userId'] == $data->receiverId) {
                $client->send(json_encode($message));
                break;
            }
        }
    }
}
