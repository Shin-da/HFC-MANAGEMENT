/**
 * Account Requests Management JavaScript
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize search functionality
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    
    if (searchButton) {
        searchButton.addEventListener('click', function() {
            performSearch();
        });
    }
    
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }
    
    // Initialize filter change handlers
    const statusFilter = document.getElementById('statusFilter');
    const departmentFilter = document.getElementById('departmentFilter');
    
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            performSearch();
        });
    }
    
    if (departmentFilter) {
        departmentFilter.addEventListener('change', function() {
            performSearch();
        });
    }
    
    // Handle modal buttons
    const approveRequestBtn = document.getElementById('approveRequestBtn');
    const rejectRequestBtn = document.getElementById('rejectRequestBtn');
    
    if (approveRequestBtn) {
        approveRequestBtn.addEventListener('click', function() {
            const requestId = this.getAttribute('data-id');
            if (requestId) {
                approveRequest(requestId);
            }
        });
    }
    
    if (rejectRequestBtn) {
        rejectRequestBtn.addEventListener('click', function() {
            const requestId = this.getAttribute('data-id');
            if (requestId) {
                rejectRequest(requestId);
            }
        });
    }
});

/**
 * Perform search based on filters
 */
function performSearch() {
    const searchValue = document.getElementById('searchInput').value;
    const statusValue = document.getElementById('statusFilter').value;
    const departmentValue = document.getElementById('departmentFilter').value;
    
    let url = 'manage-account-requests.php?';
    
    if (searchValue) {
        url += 'search=' + encodeURIComponent(searchValue) + '&';
    }
    
    if (statusValue) {
        url += 'status=' + encodeURIComponent(statusValue) + '&';
    }
    
    if (departmentValue) {
        url += 'department=' + encodeURIComponent(departmentValue) + '&';
    }
    
    // Remove trailing & and redirect
    window.location.href = url.endsWith('&') ? url.slice(0, -1) : url;
}

/**
 * View request details
 */
function viewRequest(requestId) {
    const modal = $('#requestModal');
    const modalBody = document.getElementById('requestModalBody');
    const approveBtn = document.getElementById('approveRequestBtn');
    const rejectBtn = document.getElementById('rejectRequestBtn');
    
    if (modalBody) {
        modalBody.innerHTML = 'Loading request details...';
    }
    
    if (approveBtn) {
        approveBtn.setAttribute('data-id', requestId);
    }
    
    if (rejectBtn) {
        rejectBtn.setAttribute('data-id', requestId);
    }
    
    // Fetch request details
    fetch('../admin/api/get-request.php?id=' + requestId)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // API returns either {success: true/false} or {status: 'success'/'error'}
            // Handle both formats for backward compatibility
            if (data.success === true || data.status === 'success') {
                const request = data.request;
                
                // Format the request details
                let html = `
                    <div class="request-details">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Name:</strong> ${request.firstname} ${request.lastname}
                            </div>
                            <div class="col-md-6">
                                <strong>Email:</strong> ${request.email}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Department:</strong> ${request.department}
                            </div>
                            <div class="col-md-6">
                                <strong>Position:</strong> ${request.position}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Status:</strong> <span class="badge badge-${getStatusClass(request.status)}">${request.status}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Requested On:</strong> ${formatDate(request.request_date)}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <strong>Reason for Request:</strong>
                                <p>${request.reason}</p>
                            </div>
                        </div>
                    </div>
                `;
                
                if (modalBody) {
                    modalBody.innerHTML = html;
                }
                
                // Update button visibility based on status
                if (request.status === 'pending') {
                    approveBtn.style.display = 'inline-block';
                    rejectBtn.style.display = 'inline-block';
                } else {
                    approveBtn.style.display = 'none';
                    rejectBtn.style.display = 'none';
                }
            } else {
                if (modalBody) {
                    modalBody.innerHTML = `<div class="alert alert-danger">${data.error || data.message || 'An error occurred while fetching request details.'}</div>`;
                }
            }
        })
        .catch(error => {
            console.error('Error fetching request details:', error);
            if (modalBody) {
                modalBody.innerHTML = '<div class="alert alert-danger">Failed to fetch request details. Please try again.</div>';
            }
        });
    
    modal.modal('show');
}

/**
 * Approve account request
 */
function approveRequest(requestId) {
    if (confirm('Are you sure you want to approve this account request?')) {
        fetch('../admin/api/approve-new-account.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'request_id=' + requestId
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // API returns either {success: true/false} or {status: 'success'/'error'}
            // Handle both formats for backward compatibility
            if (data.success === true || data.status === 'success') {
                alert('Account request approved successfully!');
                $('#requestModal').modal('hide');
                window.location.reload();
            } else {
                alert(data.error || data.message || 'Failed to approve account request.');
            }
        })
        .catch(error => {
            console.error('Error approving request:', error);
            alert('An error occurred while approving the request. Please try again.');
        });
    }
}

/**
 * Reject account request
 */
function rejectRequest(requestId) {
    const reason = prompt('Please provide a reason for rejecting this request:');
    
    if (reason !== null) {
        fetch('../admin/api/reject-new-account.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'request_id=' + requestId + '&reason=' + encodeURIComponent(reason)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // API returns either {success: true/false} or {status: 'success'/'error'}
            // Handle both formats for backward compatibility
            if (data.success === true || data.status === 'success') {
                alert('Account request rejected successfully!');
                $('#requestModal').modal('hide');
                window.location.reload();
            } else {
                alert(data.error || data.message || 'Failed to reject account request.');
            }
        })
        .catch(error => {
            console.error('Error rejecting request:', error);
            alert('An error occurred while rejecting the request. Please try again.');
        });
    }
}

/**
 * Helper function to get status class
 */
function getStatusClass(status) {
    switch (status) {
        case 'pending':
            return 'warning';
        case 'approved':
            return 'success';
        case 'rejected':
            return 'danger';
        default:
            return 'secondary';
    }
}

/**
 * Helper function to format date
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
