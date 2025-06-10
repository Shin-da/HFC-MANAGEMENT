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

// Search and filter functionality
const filterUsers = (searchTerm) => {
    const cards = document.querySelectorAll('.user-card');
    const term = searchTerm.toLowerCase();

    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(term) ? '' : 'none';
    });
};

const filterByRole = (role) => {
    const cards = document.querySelectorAll('.user-card');
    cards.forEach(card => {
        if (role === 'all' || card.dataset.role === role) {
            card.style.display = '';
        } else {