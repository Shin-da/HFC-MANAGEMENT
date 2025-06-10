<?php
$productData = retrieveProductData($productCode);
echo json_encode($productData);
exit;
?>