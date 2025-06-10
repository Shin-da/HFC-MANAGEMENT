<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/Page.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
Page::setCurrentPage($current_page);

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

$orderItems = $conn->query("SELECT * FROM orderlog WHERE orderid = '$orderId'");

// Set page title and styles
Page::setTitle("Order Details #{$orderId}");
Page::setBodyClass('supervisor-body');

Page::addStyle('../assets/css/customer-order.css');
Page::addStyle('../assets/css/table.css');
Page::addScript('https://cdn.jsdelivr.net/npm/sweetalert2@11');
Page::addStyle('../assets/css/orderdetail.css');
Page::addScript('../assets/js/orderdetail.js');
// Add print stylesheet
Page::addStyle('../assets/css/print.css', 'print');
Page::addScript('https://unpkg.com/@popperjs/core@2');
Page::addScript('https://unpkg.com/tippy.js@6');

ob_start();
?>

<div class="order-detail-wrapper">
    <div class="company-header print-only" style="display: none;">
        <h1>HFC MANAGEMENT</h1>
        <p>123 Business Street, Business District</p>
        <p>Phone: (123) 456-7890 | Email: contact@hfcmanagement.com</p>
        <img src="../assets/images/qr-code.png" alt="Order QR" class="qr-code">
    </div>
    <div class="detail-header">
        <div class="header-content">
            <h2>Order Details</h2>
            <p class="text-secondary">Order #<?= $orderId ?> | Reference ID: #<?= $order['hid'] ?></p>
            <p class="text-secondary"><?= date('F j, Y, g:i A', strtotime($order['orderdate'] . ' ' . $order['timeoforder'])) ?></p>
            <div class="status-indicator <?= strtolower($order['status']) ?>">
                <?php
                $icon = '';
                switch ($order['status']) {
                    case 'Pending':
                        $icon = 'bx-time';
                        break;
                    case 'Processing':
                        $icon = 'bx-hourglass';
                        break;
                    case 'Completed':
                        $icon = 'bx-check';
                        break;
                    case 'Cancelled':
                        $icon = 'bx-x';
                        break;
                }
                ?>
                <i class='bx <?= $icon ?>'></i>
                <?= $order['status'] ?>
            </div>
        </div>
    </div>

    <div class="action-bar">
        <a href="customerorder.php" class="detail-btn" data-tippy-content="Return to orders list">
            <i class='bx bx-arrow-back'></i> Back to Orders
        </a>
        <?php if ($order['status'] == 'Pending'): ?>
            <button class="detail-btn primary" data-tippy-content="Begin processing this order" id="processBtn">
                <i class='bx bx-play-circle'></i> Start Processing
            </button>
        <?php endif; ?>
        <?php if ($order['status'] == 'Processing'): ?>
            <button class="detail-btn success" data-tippy-content="Mark order as completed" id="completeBtn">
                <i class='bx bx-check-circle'></i> Mark as Completed
            </button>
        <?php endif; ?>
        <?php if ($order['status'] != 'Cancelled' && $order['status'] != 'Completed'): ?>
            <button class="detail-btn danger" data-tippy-content="Cancel this order" id="cancelBtn">
                <i class='bx bx-x-circle'></i> Cancel Order
            </button>
        <?php endif; ?>
        <button class="detail-btn" data-tippy-content="Print order details" id="printBtn">
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
                        $itemTotal = $item['unit_price'] * $item['quantity'];
                        $totalAmount += $itemTotal;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($item['productcode']) ?></td>
                        <td><?= htmlspecialchars($item['productname']) ?></td>
                        <td><?= htmlspecialchars($item['productweight']) ?> kg</td>
                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                        <td>₱<?= number_format($item['unit_price'], 2) ?></td>
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
    <div class="print-footer print-only" style="display: none;">
        <p>This document was generated on <?= date('Y-m-d H:i:s') ?></p>
        <p>For questions, please contact our customer service at support@hfcmanagement.com</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        tippy('[data-tippy-content]');

        // Add loading state utility
        const setLoading = (button, loading) => {
            if (loading) {
                button.disabled = true;
                button.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Processing...';
            } else {
                button.disabled = false;
                button.innerHTML = button.dataset.originalHtml;
            }
        };

        // Store original button HTML
        document.querySelectorAll('.detail-btn').forEach(btn => {
            btn.dataset.originalHtml = btn.innerHTML;
        });

        function updateOrderStatus(orderId, newStatus, button) {
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
                    setLoading(button, true);
                    fetch('process/update_order_status.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ orderId, status: newStatus })
                    })
                    .then(response => response.json())
                    .then(data => {
                        setLoading(button, false);
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
                        setLoading(button, false);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: error.message || 'Failed to update order status'
                        });
                    });
                }
            });
        }

        // Event Listeners
        document.getElementById('processBtn')?.addEventListener('click', function() {
            updateOrderStatus('<?= $order['orderid'] ?>', 'Processing', this);
        });

        document.getElementById('completeBtn')?.addEventListener('click', function() {
            updateOrderStatus('<?= $order['orderid'] ?>', 'Completed', this);
        });

        document.getElementById('cancelBtn')?.addEventListener('click', function() {
            updateOrderStatus('<?= $order['orderid'] ?>', 'Cancelled', this);
        });

        document.getElementById('printBtn')?.addEventListener('click', printOrder);
    });

    function printOrder() {
        // Show print-only elements
        document.querySelectorAll('.print-only').forEach(el => {
            el.style.display = 'block';
            el.style.visibility = 'visible';
        });
        
        const Toast = Swal.mixin({
            toast: true,
<?php
$content = ob_get_clean();
Page::render($content);
?>