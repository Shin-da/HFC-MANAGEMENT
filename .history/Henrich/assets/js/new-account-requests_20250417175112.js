// Placeholder functions for handling new account requests

function approveNewAccountRequest(requestId) {
    console.log("Attempting to approve request ID:", requestId);
    if (!confirm(`Are you sure you want to approve request #${requestId}? This will create a new user.`)) {
        return;
    }

    // AJAX call to the backend approval script
    fetch('./api/approve-new-account.php', { // Ensure this path is correct
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `request_id=${requestId}`
    })
    .then(response => {
        // Check if response is ok (status in the range 200-299)
        if (!response.ok) {
            // Try to parse error message if server sent JSON
            return response.json().then(errorData => {
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }).catch(() => {
                // Fallback if error response wasn't JSON
                throw new Error(`HTTP error! status: ${response.status}`);
            });
        }
        return response.json(); // Parse JSON response body
    })
    .then(data => {
        console.log("Approval response:", data);
        if (data.status === 'success') {
            alert(`Success: ${data.message}\n\nUsername: ${data.username}\nTemporary Password: ${data.temp_password}\n\nPlease provide this password to the user.`);
            // Optionally reload the page or remove the row from the table
            location.reload(); 
        } else {
             alert(`Error: ${data.message}`);
        }
    })
    .catch(error => {
        console.error('Error approving request:', error);
        alert(`An error occurred: ${error.message}`);
    });
}

function rejectNewAccountRequest(requestId) {
    console.log("Attempting to reject request ID:", requestId);
    if (!confirm(`Are you sure you want to reject request #${requestId}?`)) {
        return;
    }

    // AJAX call to the backend rejection script
    fetch('./api/reject-new-account.php', { 
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `request_id=${requestId}`
    })
    .then(response => {
        if (!response.ok) {
            // Try to parse error message if server sent JSON
            return response.json().then(errorData => {
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }).catch(() => {
                // Fallback if error response wasn't JSON
                throw new Error(`HTTP error! status: ${response.status}`);
            });
        }
        return response.json(); // Parse JSON response body
    })
    .then(data => {
        if (data.status === 'success') {
            alert('Request rejected successfully.');
            location.reload(); // Reload page to reflect changes
        } else {
            alert(`Error: ${data.message}`);
        }
    })
    .catch(error => {
        console.error('Error rejecting request:', error);
        alert(`An error occurred while rejecting the request: ${error.message}`);
    });
}

// Optional: Add event listeners for search/filter if needed
document.addEventListener('DOMContentLoaded', () => {
    const searchButton = document.getElementById('searchButton');
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');

    function performSearchAndFilter() {
        const searchTerm = searchInput.value;
        const status = statusFilter.value;
        // Construct URL with search parameters
        const url = new URL(window.location.href);
        url.searchParams.set('search', searchTerm);
        url.searchParams.set('status', status);
        url.searchParams.set('page', '1'); // Reset to page 1 on search/filter
        window.location.href = url.toString();
    }

    if (searchButton && searchInput) {
        searchButton.addEventListener('click', performSearchAndFilter);
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                performSearchAndFilter();
            }
        });
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', performSearchAndFilter);
    }
    
    // Set filter initial value based on URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    if (statusFilter && urlParams.has('status')) {
        statusFilter.value = urlParams.get('status');
    }
    if (searchInput && urlParams.has('search')) {
        searchInput.value = urlParams.get('search');
    }
}); 