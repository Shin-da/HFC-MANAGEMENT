<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

// Verify user has appropriate role
$allowed_roles = ['supervisor', 'ceo', 'admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Chat Dashboard - HFC Management');
Page::addStyle('../assets/css/chat.css');
Page::addScript('../assets/js/chat.js');

ob_start();
?>

<div class="chat-dashboard">
    <div class="chat-sidebar">
        <div class="chat-header">
            <h2>Conversations</h2>
            <button id="newChatBtn" class="btn-new-chat">
                <i class='bx bx-message-square-add'></i>
            </button>
        </div>
        
        <div class="chat-search">
            <input type="text" id="userSearch" placeholder="Search users...">
        </div>
        
        <div class="chat-users-list">
            <!-- Users will be loaded here dynamically -->
        </div>
    </div>
    
    <div class="chat-main">
        <div class="chat-empty-state">
            <i class='bx bx-message-square-detail'></i>
            <p>Select a conversation to start chatting</p>
        </div>
        
        <div class="chat-conversation" style="display: none;">
            <div class="chat-conversation-header">
                <div class="user-info">
                    <span class="user-name"></span>
                    <span class="user-status"></span>
                </div>
            </div>
            
            <div class="messages-container">
                <!-- Messages will be loaded here -->
            </div>
            
            <div class="chat-input-container">
                <input type="text" id="messageInput" placeholder="Type a message...">
                <button id="sendMessage">
                    <i class='bx bx-send'></i>
                </button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
