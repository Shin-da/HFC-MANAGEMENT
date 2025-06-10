<?php
namespace Chat;

class MessageHandler {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }