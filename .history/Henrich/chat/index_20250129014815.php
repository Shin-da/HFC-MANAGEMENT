<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/Page.php';

// Check if user has appropriate role
$allowed_roles = ['supervisor', 'ceo', 'admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Chat Dashboard');
Page::addScript('../assets/js/websocket.js');
Page::addScript('../assets/js/chat.js');
Page::addStyle('../assets/css/chat.css');

ob_start();
?>

<div class="chat-container">
    <div class="chat-sidebar">
        <div class="chat-search">
            <input type="text" id="userSearch" placeholder="Search users...">
        </div>
        
        <div class="chat-users">
            <h3>Online Users</h3>
            <div id="onlineUsers" class="users-list"></div>
            
            <h3>Recent Chats</h3>
            <div id="recentChats" class="users-list"></div>
        </div>
    </div>

    <div class="chat-main">
        <div id="chatHeader" class="chat-header">
            <div class="user-info">
                <span class="user-status"></span>
                <span class="user-name"></span>
            </div>
        </div>

        <div id="messagesContainer" class="messages-container">
            <div class="chat-welcome">
                <i class='bx bx-message-square-dots'></i>
                <p>Select a user to start chatting</p>
            </div>
        </div>

        <div class="chat-input-area">
            <input type="text" id="messageInput" placeholder="Type a message..." disabled>
            <button id="sendMessage" disabled>
                <i class='bx bx-send'></i>
            </button>
        </div>
    </div>
</div>

<script>
// Initialize chat with user info
const currentUser = {
    id: <?php echo $_SESSION['user_id']; ?>,
    name: "<?php echo htmlspecialchars($_SESSION['username']); ?>",
    role: "<?php echo htmlspecialchars($_SESSION['role']); ?>"
};
</script>

<?php
$content = ob_get_clean();
Page::render($content);
?>
