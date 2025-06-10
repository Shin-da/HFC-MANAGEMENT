/**
 * Rejection History Management JavaScript
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
    const dateFilter = document.getElementById('dateFilter');
    const departmentFilter = document.getElementById('departmentFilter');
    
    if (dateFilter) {
        dateFilter.addEventListener('change', function() {
            performSearch();
        });
    }
    
    if (departmentFilter) {
        departmentFilter.addEventListener('change', function() {
            performSearch();
        });
    }
    
    // Initialize export button
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            const requestId = this.getAttribute('data-id');
            if (requestId) {
                exportToPdf(requestId);
            }
        });
    }
});

/**
 * Perform search based on filters
 */
function performSearch() {
    const searchValue = document.getElementById('searchInput').value;
    const dateValue = document.getElementById('dateFilter').value;
    const departmentValue = document.getElementById('departmentFilter').value;
    
    let url = 'rejection-history.php?';
    
    if (searchValue) {
        url += 'search=' + encodeURIComponent(searchValue) + '&';
    }
    
    if (dateValue) {
        url += 'date=' + encodeURIComponent(dateValue) + '&';
    }
    
    if (departmentValue) {
        url += 'department=' + encodeURIComponent(departmentValue) + '&';
    }
    
    // Remove trailing & and redirect
    window.location.href = url.endsWith('&') ? url.slice(0, -1) : url;
}

/**
 * View rejection details
 */
function viewRejection(requestId) {
    const modal = $('#rejectionModal');
    const modalBody = document.getElementById('rejectionModalBody');
    const exportBtn = document.getElementById('exportBtn');
    
    if (modalBody) {
        modalBody.innerHTML = 'Loading request details...';
    }
    
    if (exportBtn) {
        exportBtn.setAttribute('data-id', requestId);
    }
    
    // Fetch rejection details
    fetch('../admin/api/get-request.php?id=' + requestId)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Check if request was found
            if (data.success === true || data.status === 'success') {
                const request = data.request;
                
                // Get processor name if available
                let processorName = 'Unknown';
                
                // Format the rejection details with all available information
                let html = `
                    <div class="rejection-details">
                        <div class="alert alert-danger mb-4">
                            <strong>This account request was rejected.</strong>
                        </div>
                        
                        <h5>Applicant Information</h5>
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
                        
                        <h5>Request Information</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Requested On:</strong> ${formatDate(request.request_date)}
                            </div>
                            <div class="col-md-6">
                                <strong>Rejected On:</strong> ${formatDate(request.processed_date)}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <strong>Reason for Request:</strong>
                                <p>${request.reason}</p>
                            </div>
                        </div>
                        
                        <h5>Rejection Information</h5>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <strong>Rejection Reason:</strong>
                                <p class="text-danger">${request.rejection_reason || 'No specific reason provided'}</p>
                            </div>
                        </div>
                    </div>
                `;
                
                if (modalBody) {
                    modalBody.innerHTML = html;
                }
            } else {
                if (modalBody) {
                    modalBody.innerHTML = `<div class="alert alert-danger">${data.error || data.message || 'An error occurred while fetching request details.'}</div>`;
                }
            }
        })
        .catch(error => {
            console.error('Error fetching rejection details:', error);
            if (modalBody) {
                modalBody.innerHTML = '<div class="alert alert-danger">Failed to fetch rejection details. Please try again.</div>';
            }
        });
    
    modal.modal('show');
}

/**
 * Export rejection details to PDF
 */
function exportRejection(requestId) {
    // First get the request details
    fetch('../admin/api/get-request.php?id=' + requestId)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success === true || data.status === 'success') {
                // Generate PDF using window.print() for simplicity
                // In a real implementation, you might want to use a library like jsPDF or use a server-side solution
                const request = data.request;
                
                // Create a temporary div for printing
                const printDiv = document.createElement('div');
                printDiv.innerHTML = `
                    <div style="padding: 20px; font-family: Arial, sans-serif;">
                        <h2 style="text-align: center;">Account Request Rejection Report</h2>
                        <h3 style="text-align: center;">HFC Management System</h3>
                        <hr>
                        
                        <h4>Applicant Information</h4>
                        <p><strong>Name:</strong> ${request.firstname} ${request.lastname}</p>
                        <p><strong>Email:</strong> ${request.email}</p>
                        <p><strong>Department:</strong> ${request.department}</p>
                        <p><strong>Position:</strong> ${request.position}</p>
                        
                        <h4>Request Information</h4>
                        <p><strong>Request ID:</strong> ${request.request_id}</p>
                        <p><strong>Requested On:</strong> ${formatDate(request.request_date)}</p>
                        <p><strong>Rejected On:</strong> ${formatDate(request.processed_date)}</p>
                        
                        <h4>Reason for Request</h4>
                        <p>${request.reason}</p>
                        
                        <h4 style="color: #dc3545;">Rejection Information</h4>
                        <p><strong>Rejection Reason:</strong></p>
                        <p style="color: #dc3545;">${request.rejection_reason || 'No specific reason provided'}</p>
                        
                        <hr>
                        <p style="text-align: center; font-size: 12px;">Generated on ${new Date().toLocaleString()}</p>
                    </div>
                `;
                
                // Append to body
                document.body.appendChild(printDiv);
                
                // Hide the rest of the page
                const mainContent = document.querySelector('.container-fluid');
                if (mainContent) {
                    mainContent.style.display = 'none';
                }
                
                // Print
                window.print();
                
                // Remove the print div and restore the main content
                document.body.removeChild(printDiv);
                if (mainContent) {
                    mainContent.style.display = '';
                }
                
            } else {
                alert(data.error || data.message || 'Failed to fetch rejection details for export.');
            }
        })
        .catch(error => {
            console.error('Error exporting rejection details:', error);
            alert('An error occurred while trying to export the rejection details. Please try again.');
        });
}

/**
 * Format date for display
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