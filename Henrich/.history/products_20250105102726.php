/*************  ✨ Codeium Command 🌟  *************/
<?php
require 'database/dbconnect.php';

// Fetch product information from the productlist table
$sql = "SELECT productcode, productname, productweight, productprice, productcategory FROM productlist";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Products</title>
    <style>
        .table-responsive {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="../resources/css/style.css">
    <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
</head>
<body>
    <div class="container">
        <h1>Our Products</h1>
        <p>Learn more about our products below:</p>
        <div class="table-responsive">
            <table class="table">
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
                                    <td>{$row['productweight']} kg</td>
                                    <td>&#8369; {$row['productprice']}</td>
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
    </div>
</body>
</html>


/******  759cee58-1572-4cf3-af4e-60ef970073bf  *******/