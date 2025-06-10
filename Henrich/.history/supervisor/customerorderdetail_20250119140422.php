<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

if (!isset($_GET['orderid'])) {
    header('Location: customerorder.php');
    exit();
}

$orderId = $_GET['orderid'];
$orderQuery = $conn->query("SELECT * FROM customerorder WHERE orderid = '$orderId'");
$order = $orderQuery->fetch_assoc();

if (!$order) {
    header('Location: customerorder.php');
    exit();
}

// After retrieving the order details, get the order items
$orderItems = $conn->query("SELECT * FROM orderlog WHERE orderid = '$orderId'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Details #<?= $orderId ?></title>
    <?php require '../reusable/header.php'; ?>
    <!-- Add SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <style>
        .order-detail-wrapper {
            padding: 2rem;
            background: var(--surface);
        }

        .detail-header {
            background: var(--card-bg);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(54, 48, 45, 0.1);
            border-left: 4px solid var(--accent);
        }

        .detail-header h2 {
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            margin-top: 1rem;
        }

        .status-indicator.pending { 
            background: rgba(222, 154, 69, 0.1);
            color: var(--warning);
        }
        .status-indicator.processing { 
            background: rgba(223, 92, 54, 0.1);
            color: var(--accent);
        }
        .status-indicator.completed { 
            background: rgba(166, 171, 138, 0.1);
            color: var (--success);
        }
        .status-indicator.cancelled { 
            background: rgba(106, 54, 43, 0.1);
            color: var(--dark);
        }

        .order-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-card {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(54, 48, 45, 0.1);
        }

        .info-card h3 {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border);
        }

        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-label {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .info-value {
            color: var(--text-primary);
            font-weight: 500;
        }

        .action-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .detail-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border-radius: 6px;
            font-weight: 500;
            transition: var(--tran-03);
            background: var(--card-bg);
            color: var(--text-primary);
            border: 1px solid var(--border);
        }

        .detail-btn:hover {
            background: var(--accent);
            color: var (--light);
            border-color: var(--accent);
            transform: translateY(-2px);
        }

        .detail-btn i {
            font-size: 1.25rem;
        }

        @media (max-width: 768px) {
            .order-detail-wrapper {
                padding: 1rem;
            }
            
            .action-bar {
                flex-direction: column;
            }
            
            .detail-btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Add new styles for order items */
        .order-items {
            margin-top: 2rem;
            background: var(--card-bg);
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(54, 48, 45, 0.1);
        }

        .order-items table {
            width: 100%;
            border-collapse: collapse;
        }

        .order-items th, .order-items td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .order-items th {
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        /* Add to your existing styles */
        .total-row {
            background-color: var(--surface);
            font-weight: bold;
        }
        
        .order-items table td {
            vertical-align: middle;
        }
        
        .order-items table tr:hover {
            background-color: var(--surface);
        }
    </style>
</head>

<body>
    <?php include '../reusable/sidebar.php'; ?>
    <?php include '../reusable/navbar.html'; ?>
    <section class="panel sales-theme">

        <div class="order-detail-wrapper">
            <div class="detail-header">
                <h2>Order #<?= $orderId ?></h2>
                <p class="text-secondary">Reference ID: #<?= $order['hid'] ?></p>
                <p class="text-secondary"><?= date('F j, Y, g:i A', strtotime($order['orderdate'] . ' ' . $order['timeoforder'])) ?></p>
                <div class="status-indicator <?= strtolower($order['status']) ?>">
                    <i class='bx bx-radio-circle-marked'></i>
                    <?= $order['status'] ?>
                </div>
            </div>

            <div class="action-bar">
                <a href="customerorder.php" class="detail-btn">
                    <i class='bx bx-arrow-back'></i> Back to Orders
                </a>
                <?php if ($order['status'] == 'Pending'): ?>
                    <button class="detail-btn" onclick="updateOrderStatus('<?= $order['orderid'] ?>', 'Processing')">
                        <i class='bx bx-play-circle'></i> Start Processing
                    </button>
                <?php endif; ?>
                <?php if ($order['status'] == 'Processing'): ?>
                    <button class="detail-btn" onclick="updateOrderStatus('<?= $order['orderid'] ?>', 'Completed')">
                        <i class='bx bx-check-circle'></i> Mark as Completed
                    </button>
                <?php endif; ?>
                <?php if ($order['status'] != 'Cancelled' && $order['status'] != 'Completed'): ?>
                    <button class="detail-btn" onclick="updateOrderStatus('<?= $order['orderid'] ?>', 'Cancelled')">
                        <i class='bx bx-x-circle'></i> Cancel Order
                    </button>
                <?php endif; ?>
                <button class="detail-btn" onclick="printOrder()">
                    <i class='bx bx-printer'></i> Print Order
                </button>
            </div>

            <div class="order-info-grid">
                <div class="info-card">
                    <h3>Order Information</h3>
                    <div class="info-row">
                        <span class="info-label">Order Type</span>
                        <span class="info-value"><?= $order['ordertype'] ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Total Amount</span>
                        <span class="info-value">₱<?= number_format($order['ordertotal'], 2) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Payment Method</span>
                        <span class="info-value"><?= $order['paymentmethod'] ?? 'N/A' ?></span>
                    </div>
                </div>

                <div class="info-card">
                    <h3>Status Timeline</h3>
                    <div class="info-row">
                        <span class="info-label">Order Date</span>
                        <span class="info-value"><?= date('M j, Y', strtotime($order['orderdate'])) ?></span>
                    </div>
                    <?php if ($order['datecompleted']): ?>
                    <div class="info-row">
                        <span class="info-label">Completed Date</span>
                        <span class="info-value"><?= date('M j, Y', strtotime($order['datecompleted'])) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="order-items">
                <h3>Order Items</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>Weight</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $totalAmount = 0;
                        if ($orderItems && $orderItems->num_rows > 0) {
                            while ($item = $orderItems->fetch_assoc()) {
                                $itemTotal = $item['productprice'] * $item['quantity'];
                                $totalAmount += $itemTotal;
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($item['productcode']) ?></td>
                                <td><?= htmlspecialchars($item['productname']) ?></td>
                                <td><?= htmlspecialchars($item['productweight']) ?> kg</td>
                                <td><?= htmlspecialchars($item['quantity']) ?></td>
                                <td>₱<?= number_format($item['productprice'], 2) ?></td>
                                <td>₱<?= number_format($itemTotal, 2) ?></td>
                            </tr>
                        <?php
                            }
                        }
                        ?>
                        <tr class="total-row">
                            <td colspan="5" style="text-align: right;"><strong>Total Amount:</strong></td>
                            <td><strong>₱<?= number_format($totalAmount, 2) ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Add more order details as needed -->
        </div>

        <?php include_once("../reusable/footer.php"); ?>
    </section>

    <script>
        function updateOrderStatus(orderId, newStatus) {
            Swal.fire({
                title: `Update Order Status`,
                text: `Are you sure you want to change the order status to ${newStatus}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, update it!',
                cancelButtonText: 'No, cancel',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('process/update_order_status.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ orderId, status: newStatus })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Status Updated!',
                                text: `Order status has been updated to ${newStatus}`,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => location.reload());
                        } else {
                            throw new Error(data.message || 'Update failed');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: error.message || 'Failed to update order status'
                        });
                    });
                }
            });
        }

        // Update print function to show toast
        function printOrder() {
            window.print();
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            Toast.fire({
                icon: 'success',
                title: 'Printing order details'
            });
        }
    </script>
</body>
</html>