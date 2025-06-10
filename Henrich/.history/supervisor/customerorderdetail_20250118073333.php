
<?php

</html>
<!DOCTYPE html>
<html>

<head>
    <title>Order Details</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/customer-pages.css">
</head>

<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>

        <div class="order-detail-card animate-fade-in">
            <div class="order-detail-header">
                <a href="javascript:history.back()" class="btn-action">
                    <i class="bx bx-arrow-back"></i> Back
                </a>
                <h2>Order Details</h2>
                <div class="header-actions">
                    <button class="btn-action" onclick="printInvoice()">
                        <i class="bx bx-printer"></i> Print Invoice
                    </button>
                </div>
            </div>

            <div class="order-detail-body">
                <div class="order-info-grid">
                    <div class="info-group">
                        <h4>Order Information</h4>
                        <p>Order ID: #<?= $hid ?></p>
                        <p>Date: <?= $orderdate ?></p>
                        <p>Time: <?= $timeoforder ?></p>
                        <p>Status: <span class="status-pill status-<?= strtolower($status) ?>"><?= $status ?></span></p>
                    </div>
                    <div class="info-group">
                        <h4>Customer Details</h4>
                        <p>Name: <?= $customername ?></p>
                        <p>Address: <?= $customeraddress ?></p>
                        <p>Phone: <?= $customerphonenumber ?></p>
                    </div>
                    <div class="info-group">
                        <h4>Order Summary</h4>
                        <p>Total Amount: ₱<?= number_format($ordertotal, 2) ?></p>
                        <p>Order Type: <?= $ordertype ?></p>
                        <p>Salesperson: <?= $salesperson ?></p>
                    </div>
                </div>

                <!-- Enhanced Status Updates -->
                <div class="action-group">
                    <?php if ($status !== 'Completed' && $status !== 'Cancelled'): ?>
                        <button class="btn-action status-processing" onclick="updateOrderStatus('Processing')">
                            <i class="bx bx-time"></i> Process Order
                        </button>
                        <button class="btn-action status-completed" onclick="updateOrderStatus('Completed')">
                            <i class="bx bx-check"></i> Complete Order
                        </button>
                        <button class="btn-action status-cancelled" onclick="updateOrderStatus('Cancelled')">
                            <i class="bx bx-x"></i> Cancel Order
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Rest of the existing order details code -->
                /* ...existing order details code... */
            </div>
        </div>
    </section>

    <script>
        function updateOrderStatus(status) {
            Swal.fire({
                title: 'Update Order Status',
                text: `Are you sure you want to mark this order as ${status}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelector(`form[action*="updateStatus"][value="${status}"]`).submit();
                }
            });
        }
    </script>
</body>

</html>