<?php
require_once '../includes/config.php';  // Include config first
require_once '../includes/session.php';
require_once '../includes/Page.php';

// Verify user has appropriate role
$allowed_roles = ['supervisor', 'ceo', 'admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Chat Dashboard');
Page::addStyle('../assets/css/chat.css');

ob_start();
?>

<div class="chat-dashboard">
    <div class="chat-sidebar">
        <div class="chat-header">
            <h2>Online Users</h2>
            <div class="online-counter">
                <span class="dot"></span>
                <span id="onlineCount">0</span> online
            </div>
        </div>

        <div class="chat-search">
            <input type="text" id="userSearch" placeholder="Search users...">
            <button class="filter-btn active" data-role="all">All</button>
            <button class="filter-btn" data-role="admin">Admins</button>
            <button class="filter-btn" data-role="supervisor">Supervisors</button>
            <button class="filter-btn" data-role="ceo">CEOs</button>
        </div>

        <div class="chat-search">
            <input type="text" id="userSearch" placeholder="Search users...">
        </div>

        <div id="onlineUsers" class="users-list active">
            <!-- Online users will be loaded here -->
            <div class="loading-indicator">Loading users...</div>
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Debug log
    console.log('Chat page initialized');
    
    // Initialize filters
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            filterUsers(btn.dataset.role);
        });
    });

    // Initialize search
    const searchInput = document.getElementById('userSearch');
    searchInput?.addEventListener('input', (e) => {
        filterUsers(document.querySelector('.filter-btn.active').dataset.role, e.target.value);
    });
});

function filterUsers(role = 'all', search = '') {
    const userElements = document.querySelectorAll('.user-item');
    userElements.forEach(userEl => {
        const userRole = userEl.querySelector('.user-role').textContent.toLowerCase();
        const userName = userEl.querySelector('.user-name').textContent.toLowerCase();
        const shouldShowRole = role === 'all' || userRole === role;
        const shouldShowSearch = !search || userName.includes(search.toLowerCase());
        userEl.style.display = shouldShowRole && shouldShowSearch ? 'flex' : 'none';
    });
}
</script>

<?php
$content = ob_get_clean();
Page::render($content);
?>
