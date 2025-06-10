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
});

function highlightInvalidFields(form) {
    const invalidFields = form.querySelectorAll(':invalid');
    invalidFields.forEach(field => {
        field.classList.add('field-error');
        field.addEventListener('input', function() {
            if (this.validity.valid) {
                this.classList.remove('field-error');
            }
        });
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

async function updateUserStatus(userId, status) {