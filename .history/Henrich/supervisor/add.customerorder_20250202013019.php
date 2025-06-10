<tbody>
    <?php
    $productcodesql = "SELECT productcode FROM products";
    $result = $conn->query($productcodesql);
    $productcodes = array();
    while ($row = $result->fetch_assoc()) {
        $productcodes[] = $row['productcode'];
    }
    for ($i = 0; $i < 1; $i++):
    ?>
    <script>
        document.getElementById("productcode<?= $i ?>").addEventListener("change", function() {
            // ... existing script ...
        });
    </script>
    <?php endfor; ?>
</tbody>
