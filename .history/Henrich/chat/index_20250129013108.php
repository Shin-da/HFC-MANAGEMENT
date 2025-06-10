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
        </div>

        <div id="onlineUsers" class="users-list active">
            <div class="loading">Loading users...</div>
        </div>
    </div>

    <div class="chat-main" id="chatMain">
        <div class="chat-empty-state">
            <i class='bx bx-message-square-dots'></i>
            <p>Select a user to start chatting</p>
        </div>
    </div>
</div>

<script>
// Debug logging
console.log('Chat page initialized');

// Initialize WebSocket connection
const ws = new WebSocket(`ws://${window.location.hostname}:8080`);
ws.onopen = () => console.log('WebSocket connected');
ws.onerror = (error) => console.error('WebSocket error:', error);

// Function to update online users
function updateOnlineUsers() {
    fetch('/api/users/online.php')
        .then(response => response.json())
        .then(data => {
            console.log('Online users data:', data); // Debug log

            const onlineUsersContainer = document.getElementById('onlineUsers');
            const onlineCount = document.getElementById('onlineCount');

            if (data.success && Array.isArray(data.users)) {
                onlineCount.textContent = data.users.length;
                
                if (data.users.length === 0) {
                    onlineUsersContainer.innerHTML = '<div class="no-users">No users online</div>';
                    return;
                }

                onlineUsersContainer.innerHTML = data.users.map(user => `
                    <div class="user-item" data-user-id="${user.user_id}">
                        <div class="user-avatar">
                            <i class='bx bxs-user-circle'></i>
                            <span class="online-indicator active"></span>
                        </div>
                        <div class="user-info">
                            <div class="user-name-role">
                                <span class="user-name">${user.username}</span>
                                <span class="user-role ${user.role}">${user.role}</span>
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                console.error('Failed to fetch online users:', data.error);
                onlineUsersContainer.innerHTML = '<div class="error">Failed to load users</div>';
            }
        })
        .catch(error => {
            console.error('Error fetching online users:', error);
        });
}

// Update online users immediately and every 30 seconds
updateOnlineUsers();
setInterval(updateOnlineUsers, 30000);
</script>

<?php
$content = ob_get_clean();
Page::render($content);
?>
