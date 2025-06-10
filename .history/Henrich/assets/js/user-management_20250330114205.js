document.addEventListener('DOMContentLoaded', function() {
    initializeUserManagement();
    setupUserFilters();
});

const UserManagement = {
    init() {
        this.bindEvents();
        this.setupFilters();
    },

    bindEvents() {
        // Status toggle handling
        document.querySelectorAll('.status-toggle').forEach(toggle => {
            toggle.addEventListener('change', (e) => this.handleStatusToggle(e));
        });

        // Edit user handling
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleEditUser(e));
        });
    },

    async handleStatusToggle(e) {
        const userId = e.target.dataset.userId;
        const status = e.target.checked ? 'active' : 'inactive';

        try {
            await AdminCore.handleUserAction('update_user_status', {
                user_id: userId,
                status: status
            });

            // Update UI
            const userCard = e.target.closest('.user-card');
            const statusBadge = userCard.querySelector('.user-status');
            statusBadge.className = `user-status ${status}`;
            statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);

        } catch (error) {
            // Revert toggle if action failed
            e.target.checked = !e.target.checked;
        }
    },

    async handleDeleteUser(userId) {
        const confirmed = await AdminCore.confirmAction('Are you sure you want to delete this user?');

        if (confirmed) {
            try {
                await AdminCore.handleUserAction('delete_user', { user_id: userId });
                const userCard = document.querySelector(`.user-card[data-user-id="${userId}"]`);
                userCard.remove();
            } catch (error) {
                // Error handling is done in AdminCore
            }
        }
    },

    setupFilters() {
        const searchInput = document.querySelector('.search-input');
        const roleFilter = document.querySelector('.role-filter');
        const statusFilter = document.querySelector('.status-filter');

        if (searchInput) {
            searchInput.addEventListener('input', this.filterUsers.bind(this));
        }
        if (roleFilter) {
            roleFilter.addEventListener('change', this.filterUsers.bind(this));
        }
        if (statusFilter) {
            statusFilter.addEventListener('change', this.filterUsers.bind(this));
        }
    },

    filterUsers() {
        const searchTerm = document.querySelector('.search-input').value.toLowerCase();
        const role = document.querySelector('.role-filter').value;
        const status = document.querySelector('.status-filter').value;

        document.querySelectorAll('.user-card').forEach(card => {
            const userRole = card.dataset.role;
            const userStatus = card.querySelector('.user-status').classList.contains('active') ? 'active' : 'inactive';
            const userText = card.textContent.toLowerCase();

            const matchesSearch = searchTerm === '' || userText.includes(searchTerm);
            const matchesRole = role === 'all' || userRole === role;
            const matchesStatus = status === 'all' || userStatus === status;

            card.style.display = matchesSearch && matchesRole && matchesStatus ? '' : 'none';
        });
    }
};

function setupUserFilters() {
    const searchInput = document.querySelector('.search-input');
    const roleFilter = document.querySelector('.role-filter');
    const statusFilter = document.querySelector('.status-filter');

    const filterUsers = () => {
        const searchTerm = searchInput.value.toLowerCase();
        const role = roleFilter.value;
        const status = statusFilter.value;

        document.querySelectorAll('.user-card').forEach(card => {
            const userRole = card.dataset.role;
            const userStatus = card.querySelector('.user-status').classList.contains('active') ? 'active' : 'inactive';
            const userText = card.textContent.toLowerCase();

            const matchesSearch = searchTerm === '' || userText.includes(searchTerm);
            const matchesRole = role === 'all' || userRole === role;
            const matchesStatus = status === 'all' || userStatus === status;

            card.style.display = matchesSearch && matchesRole && matchesStatus ? '' : 'none';
        });
    };

    [searchInput, roleFilter, statusFilter].forEach(element => {
        element.addEventListener('change', filterUsers);
        element.addEventListener('keyup', filterUsers);
    });
}

// User Management Functions

// Show Add User Modal
function showAddUserModal() {
    $('#userForm')[0].reset();
    $('#userId').val('');
    $('.modal-title').text('Add New User');
    $('.password-group').show();
    $('#password').prop('required', true);
    $('#userModal').modal('show');
}

// Edit User
function editUser(userId) {
    // Fetch user data
    fetch(`./admin/api/get-user.php?id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const user = data.user;
                $('#userId').val(user.user_id);
                $('#username').val(user.username);
                $('#useremail').val(user.useremail);
                $('#firstName').val(user.first_name);
                $('#lastName').val(user.last_name);
                $('#role').val(user.role);
                $('#department').val(user.department);
                $('#status').val(user.status);
                
                $('.modal-title').text('Edit User');
                $('.password-group').hide();
                $('#password').prop('required', false);
                $('#userModal').modal('show');
            } else {
                showNotification('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Failed to fetch user data');
        });
}

// Save User
function saveUser() {
    const formData = new FormData($('#userForm')[0]);
    const userId = $('#userId').val();
    const url = userId ? './admin/api/update-user.php' : './admin/api/add-user.php';

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            $('#userModal').modal('hide');
            showNotification('success', data.message);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showNotification('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Failed to save user');
    });
}

// Reset Password
function resetPassword(userId) {
    if (confirm('Are you sure you want to reset this user\'s password?')) {
        fetch('./admin/api/reset-password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ userId: userId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showNotification('success', data.message);
            } else {
                showNotification('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Failed to reset password');
        });
    }
}

// Delete User
function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        fetch('./admin/api/delete-user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ userId: userId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showNotification('success', data.message);
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showNotification('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Failed to delete user');
        });
    }
}

// Show Notification
function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show`;
    notification.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;
    
    const container = document.querySelector('.content-body');
    container.insertBefore(notification, container.firstChild);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
