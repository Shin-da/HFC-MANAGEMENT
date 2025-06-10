document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });

    // Form validation enhancement
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                highlightInvalidFields(form);
            }
        });
    });

    // Dynamic status updates
    const statusToggles = document.querySelectorAll('.status-toggle');
    statusToggles.forEach(toggle => {
        toggle.addEventListener('change', async function() {
            const userId = this.dataset.userId;
            const status = this.checked ? 'active' : 'inactive';
            try {
                const response = await updateUserStatus(userId, status);
                showNotification(response.message, 'success');
            } catch (error) {
                showNotification('Error updating status', 'error');
                this.checked = !this.checked;
            }
        });
    });

    // Initialize select2 for all select elements
    initializeSelect2('select');

    // Initialize DataTables
    const tables = document.querySelectorAll('.data-table');
    tables.forEach(table => {
        initializeDataTable(`#${table.id}`);
    });

    // Form validations
    validateForm('addUserForm', {
        confirmSubmit: true,
        confirmTitle: 'Add New User?',
        confirmText: 'Please confirm user creation'
    });
});

function highlightInvalidFields(form) {
    const invalidFields = form.querySelectorAll(':invalid');
    invalidFields.forEach(field => {

async function updateUserStatus(userId, status) {
    const response = await fetch('/admin/update-status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ userId, status })
    });
    return response.json();
}
