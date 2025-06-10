<?php
// Get recent stock movements
$query = "SELECT 
    p.productname,
    sm.totalpieces,
    sm.movementtype,
    sm.st,
    u.username as recorded_by
FROM stockmovement sm
JOIN productlist p ON sm.productcode = p.productcode
JOIN user u ON sm.id = id
ORDER BY sm.date_created DESC
LIMIT 10";

$result = $conn->query($query);
?>

<table class="table">
    <thead>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Type</th>
            <th>Date</th>
            <th>Recorded By</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['productname']) ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td><?= ucfirst($row['movement_type']) ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($row['date_created'])) ?></td>
                    <td><?= htmlspecialchars($row['recorded_by']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">No recent stock movements</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
