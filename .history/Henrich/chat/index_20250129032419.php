<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once '../includes/config.php';
    require_once '../includes/session.php';
    require_once '../includes/Page.php';

    // Log session data for debugging
    error_log("Session data in chat/index.php: " . print_r($_SESSION, true));

    // Verify user has appropriate role
    $allowed_roles = ['supervisor', 'ceo', 'admin'];
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
        error_log("Access denied for role: " . ($_SESSION['role'] ?? 'none'));
        header('Location: ' . BASE_URL . 'login/login.php');
        exit();
    }

    // Initialize page
    Page::setTitle('Chat Dashboard');
    Page::addStyle('../assets/css/chat.css');
    Page::addScript('../assets/js/websocket.js', ['defer' => true]);
    Page::addScript('../assets/js/chat.js', ['defer' => true]);

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

<!-- Load scripts in correct order -->
<script>
    const BASE_URL = '<?php echo BASE_URL; ?>';
    const WS_CONFIG = {
        host: '127.0.0.1',
        port: 8080,
        secure: false
    };
</script>
<script src="../assets/js/chat-manager.js"></script>
<script src="../assets/js/websocket.js"></script>
<script src="../assets/js/heartbeat.js"></script>
<script>
// Initialize chat with user info
const currentUser = {
    id: <?php echo $_SESSION['user_id'] ?? 'null'; ?>,
    name: "<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>",
    role: "<?php echo htmlspecialchars($_SESSION['role'] ?? ''); ?>"
};

// Initialize chat after DOM is fully loaded
document.addEventListener('DOMContentLoaded', () => {
    try {
        window.chatManager = new ChatManager();
        console.log('Chat manager initialized');
        window.heartbeatMonitor = new HeartbeatMonitor();
    } catch (error) {
        console.error('Error initializing chat:', error);
    }
});

// Initialize user search functionality
document.getElementById('userSearch').addEventListener('input', debounce(async function(e) {
        return;
    }

    try {
        const response = await fetch(`${BASE_URL}/api/users/search.php?query=${encodeURIComponent(searchQuery)}`);
        const data = await response.json();
        
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

} catch (Exception $e) {
    error_log("Error in chat/index.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    // Clean output buffer if anything was buffered
    if (ob_get_level()) ob_end_clean();
    
    // Return proper error response
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server error occurred',
        'message' => 'Please try again later'
    ]);
}
?>
