<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

if (!isset($_GET['hid'])) {
    header('Location: customerorder.php');
    exit();
}

$orderId = $_GET['hid'];
$orderQuery = $conn->query("SELECT * FROM customerorder WHERE hid = $orderId");
$order = $orderQuery->fetch_assoc();

if (!$order) {
    header('Location: customerorder.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Details #<?= $orderId ?></title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" href="../resources/css/customer-pages.css">
    <style>
        .order-detail-wrapper {
            padding: 2rem;
            background: var(--operation-surface);
        }

        .detail-header {
            background: var(--card-bg);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(54, 48, 45, 0.1);
            border-left: 4px solid var(--operation-primary);
        }

        .detail-header h2 {
            color: var(--operation-primary);
            margin-bottom: 0.5rem;
        }

        .text-secondary {
            color: var(--operation-secondary);
            font-size: 0.875rem;
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
            color: var(--status-pending);
        }
        .status-indicator.processing { 
            background: rgba(223, 92, 54, 0.1);
            color: var(--status-processing);
        }
        .status-indicator.completed { 
            background: rgba(166, 171, 138, 0.1);
            color: var(--status-completed);
        }
        .status-indicator.cancelled { 
            background: rgba(106, 54, 43, 0.1);
            color: var(--status-cancelled);
        }

        .info-card {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(54, 48, 45, 0.1);
            border-left: 4px solid var(--operation-border);
        }

        .info-card h3 {
            color: var(--operation-primary);
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
            border-bottom: 1px solid var(--operation-border);
        }

        .info-label {
            color: var(--operation-secondary);
            font-size: 0.875rem;
        }

        .info-value {
            color: var(--text-primary);
            font-weight: 500;
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
            color: var(--operation-primary);
            border: 1px solid var(--operation-border);
        }

        .detail-btn:hover {
            background: var(--operation-primary);
            color: var(--light);
            border-color: var(--operation-primary);
            transform: translateY(-2px);
        }

        .detail-btn.danger {
            color: var(--status-cancelled);
            border-color: var(--status-cancelled);
        }

        .detail-btn.danger:hover {
            background: var(--status-cancelled);
            color: var(--light);
        }

        .detail-btn.success {
            color: var(--status-completed);
            border-color: var(--status-completed);
        }

        .detail-btn.success:hover {
            background: var(--status-completed);
            color: var(--light);
        }
    </style>
</head>

<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="panel sales-theme">
        <?php include '../reusable/navbarNoSearch.html'; ?>

        <div class="order-detail-wrapper">
            <div class="detail-header">
                <h2>Order #<?= $orderId ?></h2>
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
                    <button class="detail-btn" onclick="updateStatus('Processing')">
                        <i class='bx bx-play-circle'></i> Start Processing
                    </button>
                <?php endif; ?>
                <?php if ($order['status'] == 'Processing'): ?>
                    <button class="detail-btn" onclick="updateStatus('Completed')">
                        <i class='bx bx-check-circle'></i> Mark as Completed
                    </button>
                <?php endif; ?>
                <?php if ($order['status'] != 'Cancelled' && $order['status'] != 'Completed'): ?>
                    <button class="detail-btn" onclick="updateStatus('Cancelled')">
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

            <!-- Add more order details as needed -->
        </div>

        <?php include_once("../reusable/footer.php"); ?>
    </section>

    <script>
        function updateStatus(newStatus) {
            // Add your status update logic here
            console.log('Updating status to:', newStatus);
        }

        function printOrder() {
            window.print();
        }
    </script>
</body>
</html>