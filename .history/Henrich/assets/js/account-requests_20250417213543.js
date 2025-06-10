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

    // Initialize bulk action functionality
    initBulkActions();
});

/**
 * Initialize bulk action functionality
 */
function initBulkActions() {
    const masterCheckbox = document.getElementById('masterCheckbox');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const deselectAllBtn = document.getElementById('deselectAllBtn');
    const bulkApproveBtn = document.getElementById('bulkApproveBtn');
    const bulkRejectBtn = document.getElementById('bulkRejectBtn');
    const checkboxes = document.querySelectorAll('.request-checkbox');
    
    // Master checkbox toggles all eligible checkboxes
    if (masterCheckbox) {
        masterCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            checkboxes.forEach(checkbox => {
                if (!checkbox.disabled) {
                    checkbox.checked = isChecked;
                }
            });
            updateBulkButtons();
        });
    }
    
    // Select All button
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            checkboxes.forEach(checkbox => {
                if (!checkbox.disabled) {
                    checkbox.checked = true;
                }
            });
            if (masterCheckbox) masterCheckbox.checked = true;
            updateBulkButtons();
        });
    }
    
    // Deselect All button
    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            if (masterCheckbox) masterCheckbox.checked = false;
            updateBulkButtons();
        });
    }
    
    // Individual checkboxes update bulk buttons when changed
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkButtons);
    });
    
    // Bulk Approve button
    if (bulkApproveBtn) {
        bulkApproveBtn.addEventListener('click', function() {
            const selectedIds = getSelectedRequestIds();
            if (selectedIds.length > 0) {
                if (confirm(`Are you sure you want to approve ${selectedIds.length} account requests?`)) {
                    bulkApprove(selectedIds);
                }
            }
        });
    }
    
    // Bulk Reject button
    if (bulkRejectBtn) {
        bulkRejectBtn.addEventListener('click', function() {
            const selectedIds = getSelectedRequestIds();
            if (selectedIds.length > 0) {
                const reason = prompt(`Please provide a reason for rejecting ${selectedIds.length} account requests:`);
                if (reason !== null) {
                    bulkReject(selectedIds, reason);
                }
            }
        });
    }
    
    // Initial update of bulk buttons
    updateBulkButtons();
}

/**
 * Update bulk action buttons based on selected checkboxes
 */
function updateBulkButtons() {
    const bulkApproveBtn = document.getElementById('bulkApproveBtn');
    const bulkRejectBtn = document.getElementById('bulkRejectBtn');
    const selectedCount = getSelectedRequestIds().length;
    
    if (bulkApproveBtn) {
        bulkApproveBtn.disabled = selectedCount === 0;
        bulkApproveBtn.innerHTML = `<i class="fas fa-check"></i> Approve Selected${selectedCount > 0 ? ` (${selectedCount})` : ''}`;
    }
    
    if (bulkRejectBtn) {
        bulkRejectBtn.disabled = selectedCount === 0;
        bulkRejectBtn.innerHTML = `<i class="fas fa-times"></i> Reject Selected${selectedCount > 0 ? ` (${selectedCount})` : ''}`;
    }
}

/**
 * Get array of selected request IDs
 */
function getSelectedRequestIds() {
    const checkboxes = document.querySelectorAll('.request-checkbox:checked');
    return Array.from(checkboxes).map(checkbox => checkbox.getAttribute('data-id'));
}

/**
 * Bulk approve selected requests
 */
function bulkApprove(requestIds) {
    // Show loading indicator
    const loadingOverlay = createLoadingOverlay('Approving requests...');
    document.body.appendChild(loadingOverlay);
    
    // Process requests sequentially to avoid server overload
    const processNextRequest = (index) => {
        if (index >= requestIds.length) {
            // All requests processed, reload the page
            document.body.removeChild(loadingOverlay);
            alert(`Successfully processed ${requestIds.length} account requests.`);
            window.location.reload();
            return;
        }
        
        const requestId = requestIds[index];
        
        fetch('../admin/api/approve-new-account.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `request_id=${requestId}`
        })
        .then(response => response.json())
        .then(data => {
            if (!data.status || data.status === 'error') {
                console.error(`Error approving request ${requestId}:`, data.message || 'Unknown error');
            }
            // Process next request regardless of success/failure
            processNextRequest(index + 1);
        })
        .catch(error => {
            console.error(`Error approving request ${requestId}:`, error);
            // Continue with next request even on error
            processNextRequest(index + 1);
        });
    };
    
    // Start processing
    processNextRequest(0);
}

/**
 * Bulk reject selected requests
 */
function bulkReject(requestIds, reason) {
    // Show loading indicator
    const loadingOverlay = createLoadingOverlay('Rejecting requests...');
    document.body.appendChild(loadingOverlay);
    
    // Process requests sequentially to avoid server overload
    const processNextRequest = (index) => {
        if (index >= requestIds.length) {
            // All requests processed, reload the page
            document.body.removeChild(loadingOverlay);
            alert(`Successfully processed ${requestIds.length} account requests.`);
            window.location.reload();
            return;
        }
        
        const requestId = requestIds[index];
        
        fetch('../admin/api/reject-new-account.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `request_id=${requestId}&reason=${encodeURIComponent(reason)}`
        })
        .then(response => response.json())
        .then(data => {
            if (!data.status || data.status === 'error') {
                console.error(`Error rejecting request ${requestId}:`, data.message || 'Unknown error');
            }
            // Process next request regardless of success/failure
            processNextRequest(index + 1);
        })
        .catch(error => {
            console.error(`Error rejecting request ${requestId}:`, error);
            // Continue with next request even on error
            processNextRequest(index + 1);
        });
    };
    
    // Start processing
    processNextRequest(0);
}

/**
 * Create loading overlay
 */
function createLoadingOverlay(message) {
    const overlay = document.createElement('div');
    overlay.style.position = 'fixed';
    overlay.style.top = '0';
    overlay.style.left = '0';
    overlay.style.width = '100%';
    overlay.style.height = '100%';
    overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    overlay.style.display = 'flex';
    overlay.style.alignItems = 'center';
    overlay.style.justifyContent = 'center';
    overlay.style.zIndex = '9999';
    overlay.style.flexDirection = 'column';
    
    const spinner = document.createElement('div');
    spinner.style.width = '50px';
    spinner.style.height = '50px';
    spinner.style.border = '5px solid #f3f3f3';
    spinner.style.borderTop = '5px solid #3498db';
    spinner.style.borderRadius = '50%';
    spinner.style.animation = 'spin 1s linear infinite';
    
    const messageElement = document.createElement('div');
    messageElement.style.color = 'white';
    messageElement.style.marginTop = '15px';
    messageElement.style.fontWeight = 'bold';
    messageElement.textContent = message || 'Processing...';
    
    // Add keyframe animation
    const style = document.createElement('style');
    style.textContent = '@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }';
    document.head.appendChild(style);
    
    overlay.appendChild(spinner);
    overlay.appendChild(messageElement);
    
    return overlay;
}

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
                                <strong>Status:</strong> 
                                <div style="
                                    display: inline-block;
                                    padding: 5px 10px;
                                    background-color: ${
                                        request.status === 'pending' ? '#ffc107' : 
                                        request.status === 'approved' ? '#28a745' : 
                                        request.status === 'rejected' ? '#dc3545' : '#6c757d'
                                    };
                                    color: ${
                                        request.status === 'pending' ? '#000000' : '#ffffff'
                                    };
                                    font-weight: bold;
                                    border-radius: 4px;
                                    text-align: center;
                                    min-width: 80px;
                                ">${request.status ? request.status.toUpperCase() : 'UNKNOWN'}</div>
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
