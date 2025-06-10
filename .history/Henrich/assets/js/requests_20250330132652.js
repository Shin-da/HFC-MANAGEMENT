// Request Management JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchRequests();
            }
        });
    }
});

// Search requests
function searchRequests() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    window.location.href = `manage-requests.php?search=${encodeURIComponent(search)}&status=${status}`;
}

// Filter requests by status
function filterRequests() {
    const status = document.getElementById('statusFilter').value;
    const search = document.getElementById('searchInput').value;
    window.location.href = `manage-requests.php?status=${status}&search=${encodeURIComponent(search)}`;
}

// Toggle select all checkbox
function toggleSelectAll() {
    const checkboxes = document.getElementsByClassName('request-checkbox');
    const selectAll = document.getElementById('selectAll');
    
    for (let checkbox of checkboxes) {
        checkbox.checked = selectAll.checked;
    }
}

// View request details
function viewRequest(requestId) {
    fetch(`api/get-request.php?id=${requestId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayRequestDetails(data.request);
                $('#requestModal').modal('show');
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Failed to load request details');
        });
}

// Display request details in modal
function displayRequestDetails(request) {
    // User Information
    document.getElementById('userInfo').innerHTML = `
        <p><strong>Name:</strong> ${request.first_name} ${request.last_name}</p>
        <p><strong>Username:</strong> ${request.username}</p>
        <p><strong>Email:</strong> ${request.useremail}</p>
    `;

    // Request Information
    document.getElementById('requestInfo').innerHTML = `
        <p><strong>Type:</strong> ${request.request_type}</p>
        <p><strong>Status:</strong> <span class="badge badge-${getStatusBadgeClass(request.status)}">${request.status}</span></p>
        <p><strong>Date:</strong> ${formatDate(request.created_at)}</p>
    `;

    // Request Details
    let detailsHtml = '';
    switch(request.request_type) {
        case 'leave':
            detailsHtml = `
                <p><strong>Leave Type:</strong> ${request.leave_type}</p>
                <p><strong>Start Date:</strong> ${formatDate(request.start_date)}</p>
                <p><strong>End Date:</strong> ${formatDate(request.end_date)}</p>
                <p><strong>Reason:</strong> ${request.reason}</p>
            `;
            break;
        case 'overtime':
            detailsHtml = `
                <p><strong>Date:</strong> ${formatDate(request.date)}</p>
                <p><strong>Hours:</strong> ${request.hours}</p>
                <p><strong>Reason:</strong> ${request.reason}</p>
            `;
            break;
        case 'schedule_change':
            detailsHtml = `
                <p><strong>Current Schedule:</strong> ${request.current_schedule}</p>
                <p><strong>Requested Schedule:</strong> ${request.requested_schedule}</p>
                <p><strong>Reason:</strong> ${request.reason}</p>
            `;
            break;
        default:
            detailsHtml = `<p><strong>Details:</strong> ${request.details}</p>`;
    }
    document.getElementById('requestDetails').innerHTML = detailsHtml;
}

// Approve request
function approveRequest(requestId) {
    if (confirm('Are you sure you want to approve this request?')) {
        updateRequestStatus(requestId, 'approved');
    }
}

// Reject request
function rejectRequest(requestId) {
    if (confirm('Are you sure you want to reject this request?')) {
        updateRequestStatus(requestId, 'rejected');
    }
}

// Update request status
function updateRequestStatus(requestId, status) {
    fetch('api/update-request.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            request_id: requestId,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', `Request ${status} successfully`);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Failed to update request status');
    });
}

// Approve selected request
function approveSelectedRequest() {
    const selectedRequests = getSelectedRequests();
    if (selectedRequests.length === 0) {
        showAlert('warning', 'Please select at least one request');
        return;
    }

    if (confirm(`Are you sure you want to approve ${selectedRequests.length} request(s)?`)) {
        updateSelectedRequestsStatus(selectedRequests, 'approved');
    }
}

// Reject selected request
function rejectSelectedRequest() {
    const selectedRequests = getSelectedRequests();
    if (selectedRequests.length === 0) {
        showAlert('warning', 'Please select at least one request');
        return;
    }

    if (confirm(`Are you sure you want to reject ${selectedRequests.length} request(s)?`)) {
        updateSelectedRequestsStatus(selectedRequests, 'rejected');
    }
}

// Get selected request IDs
function getSelectedRequests() {
    const checkboxes = document.getElementsByClassName('request-checkbox');
    const selected = [];
    
    for (let checkbox of checkboxes) {
        if (checkbox.checked) {
            selected.push(checkbox.value);
        }
    }
    
    return selected;
}

// Update status for multiple requests
function updateSelectedRequestsStatus(requestIds, status) {
    fetch('api/update-requests.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            request_ids: requestIds,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', `${requestIds.length} request(s) ${status} successfully`);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Failed to update request statuses');
    });
}

// Helper functions
function getStatusBadgeClass(status) {
    switch(status) {
        case 'pending': return 'warning';
        case 'approved': return 'success';
        case 'rejected': return 'danger';
        default: return 'secondary';
    }
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;
    
    const container = document.querySelector('.card-body');
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => alertDiv.remove(), 5000);
} 