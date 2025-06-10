<?php
require ./';

// Fetch product information from the productlist table
$sql = "SELECT productcode, productname, productweight, productprice, productcategory FROM productlist";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Products</title>
    <link rel="stylesheet" type="text/css" href="../resources/css/style.css">
</head>
<body>
    <div class="container">
        <h1>About Products</h1>
        <p>Learn more about our products below:</p>
        <table>
            <thead>
                <tr>
                    <th>Product Code</th>
                    <th>Product Name</th>
                    <th>Product Weight</th>
                    <th>Product Price</th>
                    <th>Product Category</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['productcode']}</td>
                                <td>{$row['productname']}</td>
                                <td>{$row['productweight']}</td>
                                <td>{$row['productprice']}</td>
                                <td>{$row['productcategory']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No products found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

