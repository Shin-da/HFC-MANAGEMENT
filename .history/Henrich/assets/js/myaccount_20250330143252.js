// Profile Picture Update
function updateProfilePicture(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilePreview').src = e.target.result;
            uploadProfilePicture(input.files[0]);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function uploadProfilePicture(file) {
    const formData = new FormData();
    formData.append('profile_picture', file);

    fetch('admin/api/update-profile-picture.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Profile picture updated successfully');
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Failed to update profile picture');
    });
}

// Profile Update Form
document.getElementById('updateProfileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('admin/api/update-profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Profile updated successfully');
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Failed to update profile');
    });
});

// Password Change Form
document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    if (formData.get('new_password') !== formData.get('confirm_password')) {
        showAlert('error', 'New passwords do not match');
        return;
    }
    
    fetch('admin/api/change-password.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Password changed successfully');
            this.reset();
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Failed to change password');
    });
});

// Two-Factor Authentication
document.getElementById('twoFactorSwitch').addEventListener('change', function(e) {
    if (this.checked) {
        setupTwoFactor();
    } else {
        disableTwoFactor();
    }
});

function setupTwoFactor() {
    fetch('admin/api/setup-two-factor.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('qrCode').src = data.qr_code;
                document.getElementById('secretKey').textContent = data.secret_key;
                $('#twoFactorModal').modal('show');
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Failed to setup two-factor authentication');
        });
}

function verifyTwoFactor() {
    const code = document.getElementById('verificationCode').value;
    
    fetch('admin/api/verify-two-factor.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ code })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Two-factor authentication enabled successfully');
            $('#twoFactorModal').modal('hide');
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Failed to verify two-factor code');
    });
}

function disableTwoFactor() {
    if (confirm('Are you sure you want to disable two-factor authentication?')) {
        fetch('admin/api/disable-two-factor.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Two-factor authentication disabled successfully');
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Failed to disable two-factor authentication');
            });
    }
}

// Email Notifications
document.getElementById('emailNotificationsSwitch').addEventListener('change', function(e) {
    const enabled = this.checked;
    
    fetch('admin/api/update-email-notifications.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ enabled })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Email notifications settings updated successfully');
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Failed to update email notifications settings');
    });
});

// Helper Functions
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;
    
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => alertDiv.remove(), 5000);
} 