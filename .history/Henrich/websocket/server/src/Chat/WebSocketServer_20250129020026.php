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