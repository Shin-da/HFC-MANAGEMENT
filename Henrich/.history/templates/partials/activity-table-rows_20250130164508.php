<?php
$activities = Page::get('activities');
if ($activities && $activities->num_rows > 0):
    while ($row = $activities->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['batchid']) ?></td>
            <td><?= htmlspecialchars($row['dateofarrival']) ?></td>
            <td><?= htmlspecialchars($row['dateencoded']) ?></td>
            <td><?= htmlspecialchars($row['encoder']) ?></td>
            <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
            <td><?= htmlspecialchars($row['totalNumberOfBoxes']) ?> boxes</td>
            <td><?= number_format($row['overalltotalweight'], 2) ?> kg</td>
        </tr>
    <?php endwhile;
else: ?>
    <tr><td colspan="7">No records found</td></tr>
<?php endif; ?>
