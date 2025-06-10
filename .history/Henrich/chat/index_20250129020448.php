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
        <div class="chat-header">
            <h2>Chats</h2>
            <div class="online-counter">
                <span class="dot"></span>
                <span id="onlineCount">0</span> online
            </div>
        </div>

        <div class="chat-search">
            <div class="search-box">
                <i class='bx bx-search'></i>
                <input type="text" id="userSearch" placeholder="Search users...">
            </div>
        </div>

        <div class="chat-tabs">
            <button class="tab-btn active" data-tab="online">Online</button>
            <button class="tab-btn" data-tab="recent">Recent</button>
        </div>

        <div id="onlineUsers" class="users-list active">
            <div class="loading">Loading users...</div>
        </div>

        <div id="recentChats" class="users-list">
            <!-- Recent chats will load here -->
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

// Debug logging for initialization
console.log('Initializing chat with user:', {
    id: <?php echo $_SESSION['user_id']; ?>,
    role: '<?php echo $_SESSION['role']; ?>'
});

// Initialize chat after DOM is fully loaded
document.addEventListener('DOMContentLoaded', () => {
    window.chatManager = new ChatManager();
    console.log('Chat manager initialized');
});

// Initialize user search functionality
document.getElementById('userSearch').addEventListener('input', debounce(async function(e) {
    const searchQuery = e.target.value.trim();
        if (data.success) {
            window.chatManager.updateUsersList(data.users);
        }
    } catch (error) {
        console.error('Search error:', error);
    }
}, 300));

// Debounce helper function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>

<?php
$content = ob_get_clean();
Page::render($content);
?>
