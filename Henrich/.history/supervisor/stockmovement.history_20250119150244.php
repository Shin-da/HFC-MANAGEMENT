<?php
// Get recent stock movements
$query = "SELECT 
    p.productname,
    sm.totalpieces,
    sm.dateencoded,
    u.username as recorded_by
FROM stockmovement sm
JOIN products p ON sm.productcode = p.productcode
JOIN user u ON sm.ibdid = u.uid
ORDER BY sm.dateencoded DESC
LIMIT 10";

$result = $conn->query($query);
?>

<table class="table">
    <thead>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Date</th>
            <th>Recorded By</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['productname']) ?></td>
                    <td><?= $row['totalpieces'] ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($row['dateencoded'])) ?></td>
                    <td><?= htmlspecialchars($row['recorded_by']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center">No recent stock movements</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

