<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require "./database/dbconnect.php";
require "./session/session.php";

// Check if user is logged in as admin (you can adjust this check based on your system)
if (!isset($_SESSION['accountid'])) {
    echo "Please log in first";
    exit;
}

$accountId = $_SESSION['accountid'];

// Get user data
$userQuery = $conn->prepare("SELECT * FROM customeraccount WHERE accountid = ?");
$userQuery->bind_param("i", $accountId);
$userQuery->execute();
$userData = $userQuery->get_result()->fetch_assoc();

// For security, limit access
if (!$userData) {
    echo "User not found";
    exit;
}

// Helper function to highlight text differences
function highlightDifferences($str1, $str2) {
    if ($str1 == $str2) {
        return "<span style='color: green'>{$str1}</span>";
    } else {
        return "<span style='color: red'>{$str1} ≠ {$str2}</span>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Debug Info</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        h1, h2, h3 {
            color: #385a41;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .highlight {
            background-color: #ffecb3;
        }
        .debug-section {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .order-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .order-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            background-color: white;
        }
        .mismatch {
            background-color: #ffecb3;
        }
    </style>
</head>
<body>
    <h1>Order System Debug Information</h1>
    
    <div class="debug-section">
        <h2>Current User Information</h2>
        <p><strong>Account ID:</strong> <?php echo $accountId; ?></p>
        <p><strong>Username:</strong> <?php echo $userData['username'] ?? 'N/A'; ?></p>
        <p><strong>Email:</strong> <?php echo $userData['useremail'] ?? 'N/A'; ?></p>
        <p><strong>Phone:</strong> <?php echo $userData['phone'] ?? 'N/A'; ?></p>
    </div>
    
    <div class="debug-section">
        <h2>Recent Orders (Last 10)</h2>
        <p>These are the most recent orders in the database:</p>
        
        <?php
        $recentOrders = $conn->query("SELECT * FROM customerorder ORDER BY orderdate DESC, timeoforder DESC LIMIT 10");
        
        if ($recentOrders && $recentOrders->num_rows > 0):
        ?>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Customer ID</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Name Match?</th>
                    <th>ID Match?</th>
                </tr>
                <?php 
                while($order = $recentOrders->fetch_assoc()): 
                    $nameMatch = strtolower($order['customername']) == strtolower($userData['username'] ?? '');
                    $idMatch = $order['customerid'] == $accountId;
                    $rowClass = ($nameMatch || $idMatch) ? 'highlight' : '';
                ?>
                    <tr class="<?php echo $rowClass; ?>">
                        <td><?php echo $order['orderid']; ?></td>
                        <td><?php echo highlightDifferences($order['customername'], $userData['username'] ?? 'N/A'); ?></td>
                        <td><?php echo $order['customerid'] ? highlightDifferences($order['customerid'], $accountId) : 'NULL'; ?></td>
                        <td><?php echo $order['orderdate']; ?></td>
                        <td><?php echo $order['status']; ?></td>
                        <td><?php echo $nameMatch ? 'Yes' : 'No'; ?></td>
                        <td><?php echo $idMatch ? 'Yes' : 'No'; ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No orders found in the database.</p>
        <?php endif; ?>
    </div>
    
    <div class="debug-section">
        <h2>Orders That Should Be Visible To You</h2>
        <p>These orders match your account in some way:</p>
        
        <?php
        // Multi-approach query similar to the one in orderhistory.php
        $query = "SELECT o.* FROM customerorder o 
                  WHERE LOWER(o.customername) = LOWER(?) 
                  OR o.customerid = ? 
                  OR o.customername LIKE CONCAT('%', ?, '%')
                  OR EXISTS (
                      SELECT 1 FROM customeraccount ca 
                      WHERE ca.accountid = ? 
                      AND LOWER(o.customername) = LOWER(ca.username)
                  )
                  ORDER BY o.orderdate DESC, o.timeoforder DESC";
                  
        $stmt = $conn->prepare($query);
        $username = $userData['username'] ?? '';
        $stmt->bind_param("sisi", $username, $accountId, $username, $accountId);
        $stmt->execute();
        $matchingOrders = $stmt->get_result();
        
        if ($matchingOrders && $matchingOrders->num_rows > 0):
        ?>
            <div class="order-list">
                <?php while($order = $matchingOrders->fetch_assoc()): ?>
                    <div class="order-card">
                        <h3>Order #<?php echo $order['orderid']; ?></h3>
                        <p><strong>Date:</strong> <?php echo $order['orderdate']; ?></p>
                        <p><strong>Status:</strong> <?php echo $order['status']; ?></p>
                        <p><strong>Customer:</strong> <?php echo $order['customername']; ?></p>
                        <p><strong>Total:</strong> ₱<?php echo number_format($order['ordertotal'], 2); ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No orders found that match your account.</p>
        <?php endif; ?>
    </div>
    
    <div class="debug-section">
        <h2>Order-Customer Relationship Analysis</h2>
        <p>This section helps identify why orders might not be associated with your account:</p>
        
        <?php
        // Get all orders to check for potential matches
        $allOrders = $conn->query("SELECT * FROM customerorder ORDER BY orderdate DESC LIMIT 20");
        
        echo "<h3>Checking 20 most recent orders for potential relationships:</h3>";
        echo "<table>";
        echo "<tr><th>Order ID</th><th>Customer Name</th><th>CustomerID</th><th>Customer Phone</th><th>Potential Issues</th></tr>";
        
        if ($allOrders && $allOrders->num_rows > 0) {
            while($order = $allOrders->fetch_assoc()) {
                $issues = [];
                $rowClass = '';
                
                // Check for NULL customerid
                if ($order['customerid'] === null) {
                    $issues[] = "Customer ID is NULL";
                    $rowClass = 'mismatch';
                }
                
                // Check for exact username match (case-insensitive)
                if (strtolower($order['customername']) != strtolower($userData['username'])) {
                    $issues[] = "Name doesn't match username";
                    $rowClass = 'mismatch';
                }
                
                // Check for phone number match if available
                $userPhone = $userData['phone'] ?? '';
                if ($userPhone && $order['customerphonenumber'] != $userPhone) {
                    $issues[] = "Phone numbers don't match";
                }
                
                echo "<tr class='$rowClass'>";
                echo "<td>" . $order['orderid'] . "</td>";
                echo "<td>" . $order['customername'] . "</td>";
                echo "<td>" . ($order['customerid'] === null ? 'NULL' : $order['customerid']) . "</td>";
                echo "<td>" . $order['customerphonenumber'] . "</td>";
                echo "<td>" . (empty($issues) ? 'No issues' : implode(", ", $issues)) . "</td>";
                echo "</tr>";
            }
        }
        echo "</table>";
        ?>
    </div>
    
    <div class="debug-section">
        <h2>Solution Suggestions</h2>
        <p>Based on the analysis, here are some possible solutions:</p>
        
        <ol>
            <li>Update all orders with NULL customerid to have your account ID (<?php echo $accountId; ?>)</li>
            <li>Make sure the customer name in orders matches your username (<?php echo $userData['username'] ?? 'N/A'; ?>)</li>
            <li>Update the query in orderhistory.php to ignore case sensitivity when matching names</li>
            <li>Add phone number matching to the order query if customer IDs don't match</li>
        </ol>
        
        <p>The most important fix is likely the customer ID association.</p>
    </div>
</body>
</html><?php
// Close db connection
if (isset($conn)) {
    $conn->close();
}
?> 