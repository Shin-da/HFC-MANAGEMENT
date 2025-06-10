<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';

// Initialize variables
$hid = isset($_GET['hid']) ? mysqli_real_escape_string($conn, $_GET['hid']) : '';
$orderdate = $timeoforder = $status = $customername = $customeraddress = '';
$customerphonenumber = $ordertype = $salesperson = $ordertotal = '';

// Fetch order details
if ($hid) {
    $sql = "SELECT * FROM customerorder WHERE hid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $hid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        $orderdate = htmlspecialchars($order['orderdate']);
        $timeoforder = htmlspecialchars($order['timeoforder']);
        $status = htmlspecialchars($order['status']);
        $customername = htmlspecialchars($order['customername']);
        $customeraddress = htmlspecialchars($order['customeraddress']);
        $customerphonenumber = htmlspecialchars($order['customerphonenumber']);
        $ordertype = htmlspecialchars($order['ordertype']);
        $salesperson = htmlspecialchars($order['salesperson']);
        $ordertotal = htmlspecialchars($order['ordertotal']);
    } else {
        die("Order not found");
    }
    $stmt->close();
}

// Update order status - modify this section
if (isset($_POST['updateStatus'])) {
    $update_hid = mysqli_real_escape_string($conn, $_POST['hid']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $update_sql = "UPDATE customerorder SET status = ?, datecompleted = CASE WHEN ? = 'Completed' THEN NOW() ELSE datecompleted END WHERE hid = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sss", $new_status, $new_status, $update_hid);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Order status has been updated!";
        header("Location: customerorderdetail.php?hid=" . $update_hid);
        exit;
    } else {
        $_SESSION['error_message'] = "Error updating status: " . $conn->error;
    }
    $stmt->close();
}

// Delete and archive order
if (isset($_POST['deleteOrder']) && isset($_POST['hid'])) {
    $delete_hid = mysqli_real_escape_string($conn, $_POST['hid']);
    
    // Start transaction
    $conn->begin_transaction();
    try {
        // Insert into archive
        $archive_sql = "INSERT INTO archivedorder SELECT * FROM customerorder WHERE hid = ?";
        $stmt = $conn->prepare($archive_sql);
        $stmt->bind_param("s", $delete_hid);
        $stmt->execute();
        
        // Delete from customerorder
        $delete_sql = "DELETE FROM customerorder WHERE hid = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("s", $delete_hid);
        $stmt->execute();
        
        $conn->commit();
        echo "<script>
            alert('Order has been archived successfully!');
            window.location.href = 'customerorder.php';
        </script>";
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Details</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/customer-pages.css">
    <!-- Add SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>
        
        <?php if ($hid && isset($order)): ?>
            <div class="order-detail-card animate-fade-in">
                <!-- Order Header -->
                <div class="order-detail-header">
                    <a href="javascript:history.back()" class="btn-action">
                        <i class="bx bx-arrow-back"></i> Back
                    </a>
                    <h2>Order Details #<?php echo $hid; ?></h2>
                    <div class="header-actions">
                        <button class="btn-action" onclick="printInvoice()">
                            <i class="bx bx-printer"></i> Print Invoice
                        </button>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="order-detail-body">
                    <div class="order-info-grid">
                        <!-- Order Information -->
                        <div class="info-group">
                            <h4>Order Information</h4>
                            <p>Order ID: #<?php echo $hid; ?></p>
                            <p>Date: <?php echo $orderdate; ?></p>
                            <p>Time: <?php echo $timeoforder; ?></p>
                            <p>Status: <span class="status-pill status-<?php echo strtolower($status); ?>"><?php echo $status; ?></span></p>
                        </div>
                        
                        <!-- Customer Information -->
                        <div class="info-group">
                            <h4>Customer Details</h4>
                            <p>Name: <?php echo $customername; ?></p>
                            <p>Address: <?php echo $customeraddress; ?></p>
                            <p>Phone: <?php echo $customerphonenumber; ?></p>
                        </div>
                        
                        <!-- Order Summary -->
                        <div class="info-group">
                            <h4>Order Summary</h4>
                            <p>Total Amount: ₱<?php echo number_format($ordertotal, 2); ?></p>
                            <p>Order Type: <?php echo $ordertype; ?></p>
                            <p>Salesperson: <?php echo $salesperson; ?></p>
                        </div>
                    </div>

                    <!-- Replace the Status Update Forms section with this -->
                    <?php if ($status !== 'Completed' && $status !== 'Cancelled'): ?>
                        <div class="action-group">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="updateStatus" value="1">
                                <input type="hidden" name="hid" value="<?php echo htmlspecialchars($hid); ?>">
                                <input type="hidden" name="status" value="Processing">
                                <button type="button" class="btn-action status-processing" onclick="updateOrderStatus(this.form, 'Processing')">
                                    <i class="bx bx-time"></i> Process Order
                                </button>
                            </form>

                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="updateStatus" value="1">
                                <input type="hidden" name="hid" value="<?php echo htmlspecialchars($hid); ?>">
                                <input type="hidden" name="status" value="Completed">
                                <button type="button" class="btn-action status-completed" onclick="updateOrderStatus(this.form, 'Completed')">
                                    <i class="bx bx-check"></i> Complete Order
                                </button>
                            </form>

                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="updateStatus" value="1">
                                <input type="hidden" name="hid" value="<?php echo htmlspecialchars($hid); ?>">
                                <input type="hidden" name="status" value="Cancelled">
                                <button type="button" class="btn-action status-cancelled" onclick="updateOrderStatus(this.form, 'Cancelled')">
                                    <i class="bx bx-x"></i> Cancel Order
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="error-message">
                <h2>Error: Order not found</h2>
                <a href="customerorder.php" class="btn-action">Return to Orders</a>
            </div>
        <?php endif; ?>
        <?php include_once("../reusable/footer.php"); ?>
    </section>

    <!-- Add this near the top of the body section -->
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
                    form.submit();
                }
            });
        }

        function printInvoice() {
            window.print();
        }
    </script>
</body>
</html>