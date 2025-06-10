// Customer Management Functions

// Show Add Customer Modal
function showAddCustomerModal() {
    $('#customerModalLabel').text('Add New Customer');
    $('#customerForm')[0].reset();
    $('#accountId').val('');
    $('#password').prop('required', true);
    $('#customerModal').modal('show');
}

// Edit Customer
function editCustomer(accountId) {
    $('#customerModalLabel').text('Edit Customer');
    $('#password').prop('required', false);
    
    // Fetch customer data
    fetch(`../admin/api/get-customer.php?accountId=${accountId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const customer = data.customer;
                $('#accountId').val(customer.accountid);
                $('#customerName').val(customer.customername);
                $('#customerAddress').val(customer.customeraddress);
                $('#customerPhone').val(customer.customerphonenumber);
                $('#customerEmail').val(customer.useremail);
                $('#username').val(customer.username);
                $('#status').val(customer.status);
                $('#customerModal').modal('show');
            } else {
                showNotification('Error', data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error', 'Failed to fetch customer data', 'error');
        });
}

// Save Customer
function saveCustomer() {
    const formData = new FormData();
    formData.append('accountId', $('#accountId').val());
    formData.append('customerName', $('#customerName').val());
    formData.append('customerAddress', $('#customerAddress').val());
    formData.append('customerPhone', $('#customerPhone').val());
    formData.append('customerEmail', $('#customerEmail').val());
    formData.append('username', $('#username').val());
    formData.append('password', $('#password').val());
    formData.append('status', $('#status').val());
    
    const profilePicture = $('#profilePicture')[0].files[0];
    if (profilePicture) {
        formData.append('profilePicture', profilePicture);
    }

    const url = $('#accountId').val() ? '../admin/api/update-customer.php' : '../admin/api/add-customer.php';

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showNotification('Success', data.message, 'success');
            $('#customerModal').modal('hide');
            location.reload();
        } else {
            showNotification('Error', data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Error', 'Failed to save customer', 'error');
    });
}

// Delete Customer
function deleteCustomer(accountId) {
    if (confirm('Are you sure you want to delete this customer?')) {
        fetch('../admin/api/delete-customer.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ accountId: accountId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showNotification('Success', data.message, 'success');
                location.reload();
            } else {
                showNotification('Error', data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error', 'Failed to delete customer', 'error');
        });
    }
}

// Export Customers
function exportCustomers() {
    window.location.href = '../admin/api/export-customers.php';
}

// Search and Filter
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    let searchTimeout;

    function performSearch() {
        const searchTerm = searchInput.value;
        const status = statusFilter.value;
        
        fetch(`../admin/api/search-customers.php?search=${encodeURIComponent(searchTerm)}&status=${status}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    updateTable(data.customers);
                } else {
                    showNotification('Error', data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Error', 'Failed to search customers', 'error');
            });
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 500);
    });

    statusFilter.addEventListener('change', performSearch);
});

// Update Table with Search Results
function updateTable(customers) {
    const tbody = document.getElementById('customerTableBody');
    tbody.innerHTML = '';

    if (customers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="11">No results found</td></tr>';
        return;
    }

    customers.forEach(customer => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><img src="${escapeHtml(customer.profilepicture)}" alt="Profile Picture" width="50" height="50"></td>
            <td>${escapeHtml(customer.accountid)}</td>
            <td>${escapeHtml(customer.customername)}</td>
            <td>${escapeHtml(customer.customeraddress)}</td>
            <td>${escapeHtml(customer.customerphonenumber)}</td>
            <td>${escapeHtml(customer.customerid)}</td>
            <td>${escapeHtml(customer.username)}</td>
            <td>••••••••</td>
            <td>${escapeHtml(customer.useremail)}</td>
            <td><span class="badge badge-${customer.status === 'active' ? 'success' : 'danger'}">${escapeHtml(customer.status)}</span></td>
            <td>
                <button class="btn btn-sm btn-info" onclick="editCustomer(${customer.accountid})"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-danger" onclick="deleteCustomer(${customer.accountid})"><i class="fas fa-trash"></i></button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Show Notification
function showNotification(title, message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show`;
    notification.innerHTML = `
        <strong>${title}</strong> ${message}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    
    document.querySelector('.container-fluid').insertBefore(notification, document.querySelector('.table-header'));
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Helper function to escape HTML
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
} 