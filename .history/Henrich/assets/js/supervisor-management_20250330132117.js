// Supervisor Management JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize table search and filter
    AdminCore.initTableSearch('searchInput', 'supervisorTableBody');
    AdminCore.initStatusFilter('statusFilter', 'supervisorTableBody', 7); // 7 is the status column index
    
    // Initialize file preview for profile picture
    AdminCore.initFilePreview('profilePicture', 'profilePreview');
});

// Show add supervisor modal
function showAddSupervisorModal() {
    document.getElementById('supervisorForm').reset();
    document.getElementById('supervisorId').value = '';
    document.getElementById('supervisorModalLabel').textContent = 'Add New Supervisor';
    $('#supervisorModal').modal('show');
}

// Edit supervisor
async function editSupervisor(userId) {
    try {
        const response = await fetch(`../admin/api/get-supervisor.php?id=${userId}`);
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message);
        }
        
        const supervisor = data.supervisor;
        
        // Fill form fields
        document.getElementById('supervisorId').value = supervisor.user_id;
        document.getElementById('username').value = supervisor.username;
        document.getElementById('email').value = supervisor.useremail;
        document.getElementById('firstName').value = supervisor.first_name;
        document.getElementById('lastName').value = supervisor.last_name;
        document.getElementById('department').value = supervisor.department;
        document.getElementById('status').value = supervisor.status;
        
        // Clear password field
        document.getElementById('password').value = '';
        
        // Update modal title
        document.getElementById('supervisorModalLabel').textContent = 'Edit Supervisor';
        
        // Show modal
        $('#supervisorModal').modal('show');
    } catch (error) {
        AdminCore.handleAjaxError(error);
    }
}

// Save supervisor
async function saveSupervisor() {
    // Validate form
    const isValid = AdminCore.validateForm('supervisorForm', {
        username: { required: true },
        email: { required: true, email: true },
        firstName: { required: true },
        lastName: { required: true },
        department: { required: true }
    });
    
    if (!isValid) return;
    
    try {
        const form = document.getElementById('supervisorForm');
        const formData = new FormData(form);
        
        // Add user ID if editing
        const userId = document.getElementById('supervisorId').value;
        if (userId) {
            formData.append('user_id', userId);
            formData.append('action', 'update');
        } else {
            formData.append('action', 'add');
        }
        
        const response = await fetch('../admin/api/save-supervisor.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message);
        }
        
        // Show success message
        AdminCore.showToast(result.message, 'success');
        
        // Close modal and refresh table
        $('#supervisorModal').modal('hide');
        location.reload();
    } catch (error) {
        AdminCore.handleAjaxError(error);
    }
}

// Reset password
async function resetPassword(userId) {
    try {
        const result = await AdminCore.confirmAction(
            'Reset Password',
            'Are you sure you want to reset this supervisor\'s password?',
            'warning'
        );
        
        if (!result.isConfirmed) return;
        
        const response = await fetch('../admin/api/reset-password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ user_id: userId })
        });
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message);
        }
        
        AdminCore.showToast('Password has been reset successfully', 'success');
    } catch (error) {
        AdminCore.handleAjaxError(error);
    }
}

// Delete supervisor
async function deleteSupervisor(userId) {
    try {
        const result = await AdminCore.confirmAction(
            'Delete Supervisor',
            'Are you sure you want to delete this supervisor? This action cannot be undone.',
            'warning'
        );
        
        if (!result.isConfirmed) return;
        
        const response = await fetch('../admin/api/delete-supervisor.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ user_id: userId })
        });
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message);
        }
        
        AdminCore.showToast('Supervisor has been deleted successfully', 'success');
        location.reload();
    } catch (error) {
        AdminCore.handleAjaxError(error);
    }
} 