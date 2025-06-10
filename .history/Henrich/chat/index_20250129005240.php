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
            <h2>Chats</h2>
            <div class="online-indicator">
                <span class="dot"></span>
                <span id="onlineCount">0</span> online
            </div>
        </div>
        
        <div class="user-tabs">
            <button class="tab-btn active" data-tab="online">Online</button>
            <button class="tab-btn" data-tab="recent">Recent</button>
        </div>

        <div class="chat-search">
            <input type="text" id="userSearch" placeholder="Search users...">
        </div>
        
        <div id="onlineUsers" class="users-list active">
            <!-- Online users will be loaded here -->
        </div>
        
        <div id="recentChats" class="users-list">
            <!-- Recent chats will be loaded here -->
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
