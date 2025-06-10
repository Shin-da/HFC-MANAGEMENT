/*************  ✨ Codeium Command 🌟  *************/
<?php
// Get recent stock movements
$query = "SELECT 
    p.productname,
    sm.totalpieces,
    sm.dateencoded,
    sm.stockmovementtype,
    sm.stockmovementdate,
    u.username as recorded_by
FROM stockmovement sm
JOIN products p ON sm.productcode = p.productcode
JOIN user u ON sm.ibdid = u.uid
ORDER BY sm.dateencoded DESC
ORDER BY sm.stockmovementdate DESC
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
                    <td><?= $row['totalpieces'] ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($row['dateencoded'])) ?></td>
                    <td><?= ucfirst($row['stockmovementtype']) ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($row['stockmovementdate'])) ?></td>
                    <td><?= htmlspecialchars($row['recorded_by']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center">No recent stock movements</td>
                <td colspan="5" class="text-center">No recent stock movements</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

/******  00ff3b77-9a8a-4771-b6e0-a4f896465b18  *******/