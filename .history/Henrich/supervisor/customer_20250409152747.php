<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/Page.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
Page::setCurrentPage($current_page);

error_log("customer.php: Script started");

try {
    // Get total records count from orders table
    $total_records_query = "SELECT COUNT(DISTINCT customername) as count FROM orders";
    $total_records = $conn->query($total_records_query)->fetch_assoc()['count'];

    // Get today's active customers count
    $today_active_query = "SELECT COUNT(DISTINCT customername) as count FROM orders WHERE DATE(orderdate) = CURDATE()";
    $today_active = $conn->query($today_active_query)->fetch_assoc()['count'];

    // Update the table display query to use orders table
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $sql = "SELECT DISTINCT 
                customername,
                customeraddress,
                customerphonenumber,
                customer_id
            FROM orders 
            ORDER BY customer_id DESC 
            LIMIT ? OFFSET ?";
            
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        throw new Exception("Query execution failed: " . $stmt->error);
    }

} catch (Exception $e) {
    error_log("Error in customer.php: " . $e->getMessage());
    $total_records = 0;
    $today_active = 0;
    $result = null;
}

// Set page title and styles
Page::setTitle('Customer Management');
Page::addStyle('../assets/css/sales-master.css');
Page::addStyle('https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css');
Page::addScript('https://cdn.jsdelivr.net/npm/sweetalert2@11');

ob_start();
?>

<div class="overview sales-theme">
    <div class="dashboard-header sales-page-header">
        <h1>Customer Management</h1>
        <div class="stats-container">
            <div class="stat-card">
                <h3>Total Customers</h3>
                <p class="stat-number"><?php echo $total_records; ?></p>
            </div>
            <div class="stat-card">
                <h3>Active Today</h3>
                <p class="stat-number">
                    <?php 
                        echo $today_active;
                    ?>
                </p>
            </div>
        </div>
        
        <div class="filters-container">
            <input type="text" id="searchCustomer" class="search-box" placeholder="Search customers...">
            <div class="action-buttons">
                <button class="action-btn export-btn" onclick="exportToExcel()">
                    <i class="bi bi-download"></i> Export
                </button>
                <button class="action-btn sync-btn" onclick="syncCustomers()">
                    <i class="bi bi-arrow-repeat"></i> Sync Customers
                </button>
            </div>
        </div>
    </div>

    <div class="table-container">
        <h1>Customer Information</h1>
        <?php if ($result && $result->num_rows > 0): ?>
            <table class="table" id="customerTable">
                <thead>
                    <tr>
                        <th onclick="sortTable(0)">Customer Name ↕</th>
                        <th onclick="sortTable(1)">Customer Address ↕</th>
                        <th onclick="sortTable(2)">Customer Phone ↕</th>
                        <th>Customer ID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['customername'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['customeraddress'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['customerphonenumber'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['customer_id'] ?? ''); ?></td>
                            <td>
                                <button onclick='viewCustomer(<?php echo $row["customer_id"]; ?>)'>View</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div class="pagination">
                <?php
                $total_pages = ceil($total_records / $limit);
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo "<button onclick='changePage($i)'" . ($page == $i ? " class='active'" : "") . ">$i</button>";
                }
                ?>
            </div>
        <?php else: ?>
            <div class="no-data">
                <p>No customers found. Please check the database connection or add some customers.</p>
            </div>
        <?php endif; ?>
    </div>
    <div id="loading-spinner" class="loading-spinner"></div>
</div>

<script>
    function showAlert(message, type = 'success') {
        Swal.fire({
            text: message,
            icon: type,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    async function syncCustomers() {
        try {
            // Show loading
            Swal.fire({
                title: 'Synchronizing Customers',
                text: 'Please wait...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            const response = await fetch('sync_customers.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            
            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: `Successfully synchronized ${data.affected} customers`,
                    timer: 2000,
                    showConfirmButton: false
                });
                window.location.reload();
            } else {
                throw new Error(data.message || 'Error synchronizing customers');
            }
        } catch (error) {
            console.error('Sync error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: error.message,
                confirmButtonText: 'OK'
            });
        }
    }

    function searchCustomers() {
        const input = document.getElementById('searchCustomer');
        const filter = input.value.toUpperCase();
        const table = document.getElementById('customerTable');
        const tr = table.getElementsByTagName('tr');

        for (let i = 1; i < tr.length; i++) {
            const td = tr[i].getElementsByTagName('td');
            let textValue = '';
            for (let j = 0; j < td.length - 1; j++) {
                textValue += td[j].textContent || td[j].innerText;
            }
            tr[i].style.display = textValue.toUpperCase().indexOf(filter) > -1 ? '' : 'none';
        }
    }

    async function viewCustomer(customerId) {
        try {
            Swal.fire({
                title: 'Loading...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const response = await fetch(`get_customer_details.php?id=${customerId}`);
            const data = await response.json();
            
            if (data.success) {
                const customer = data.customer;
                Swal.fire({
                    title: 'Customer Details',
                    html: `
                        <div class="customer-details-grid">
                            <div class="detail-item">
                                <label>Name:</label>
                                <span>${customer.customername}</span>
                            </div>
                            <div class="detail-item">
                                <label>Address:</label>
                                <span>${customer.customeraddress}</span>
                            </div>
                            <div class="detail-item">
                                <label>Phone:</label>
                                <span>${customer.customerphonenumber}</span>
                            </div>
                            <div class="detail-item">
                                <label>Total Orders:</label>
                                <span>${data.orderCount}</span>
                            </div>
                        </div>
                    `,
                    width: '600px',
                    confirmButtonColor: '#4CAF50',
                    confirmButtonText: 'Close'
                });
            } else {
                throw new Error(data.message || 'Failed to load customer details');
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: error.message,
                confirmButtonColor: '#f44336'
            });
        }
    }

    // Remove duplicate syncCustomers function
    // Remove old Toastify code

    // Close modal functions are no longer needed as we're using SweetAlert2

    // Initialize search with debouncing
    const searchInput = document.getElementById('searchCustomer');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(searchCustomers, 300);
        });
    }
</script>

<?php
$content = ob_get_clean();
Page::render($content);
?>


<style>
    /* Customer Management Page Styles */
.overview {
    padding: 2rem;
}

/* Dashboard Header */
.dashboard-header {
    margin-bottom: 2rem;
}

.dashboard-header h1 {
    font-size: 1.8rem;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
}

/* Stats Container */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--surface);
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.stat-card h3 {
    font-size: 1rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary);
}

/* Filters Container */
.filters-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.search-box {
    flex: 1;
    max-width: 400px;
    padding: 0.75rem;
    border: 1px solid var(--border);
    border-radius: 6px;
    font-size: 1rem;
    background: var(--surface);
    color: var(--text-primary);
}

.action-buttons {
    display: flex;
    gap: 1rem;
}

.action-btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: background-color 0.2s;
}

.export-btn {
    background: var(--success);
    color: white;
}

.sync-btn {
    background: var(--primary);
    color: white;
}

.action-btn:hover {
    opacity: 0.9;
}

/* Table Container */
.table-container {
    background: var(--surface);
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.table-container h1 {
    font-size: 1.4rem;
    margin-bottom: 1.5rem;
    color: var(--text-primary);
}

/* Table Styles */
.table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1.5rem;
}

.table th {
    background: var(--surface-variant);
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: var(--text-primary);
    cursor: pointer;
}

.table th:hover {
    background: var(--surface-variant-hover);
}

.table td {
    padding: 1rem;
    border-bottom: 1px solid var(--border);
    color: var(--text-primary);
}

.table tr:hover {
    background: var(--surface-hover);
}

/* Table Actions */
.table button {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    background: var(--primary);
    color: white;
    cursor: pointer;
    transition: background-color 0.2s;
}

.table button:hover {
    background: var(--primary-dark);
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 1.5rem;
}

.pagination button {
    padding: 0.5rem 1rem;
    border: 1px solid var(--border);
    background: var(--surface);
    color: var(--text-primary);
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
}

.pagination button:hover {
    background: var(--surface-variant);
}

.pagination button.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

/* Loading Spinner */
.loading-spinner {
    display: none;
    width: 40px;
    height: 40px;
    border: 4px solid var(--surface-variant);
    border-top: 4px solid var(--primary);
    border-radius: 50%;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}

/* Customer Details Modal Styles */
.customer-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.detail-item {
    padding: 1rem;
    background: var(--surface-variant);
    border-radius: 6px;
}

.detail-item label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--text-secondary);
}

.detail-item span {
    color: var(--text-primary);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .overview {
        padding: 1rem;
    }

    .filters-container {
        flex-direction: column;
    }

    .search-box {
        max-width: 100%;
    }

    .table-container {
        overflow-x: auto;
    }

    .table {
        min-width: 600px;
    }
}

</style>