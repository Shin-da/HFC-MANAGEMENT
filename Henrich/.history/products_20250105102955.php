/*************  ✨ Codeium Command 🌟  *************/
<?php
require 'database/dbconnect.php';

// Fetch product information from the productlist table
$sql = "SELECT productcode, productname, productweight, productprice, productcategory FROM productlist";
$result = $conn->query($sql);
$categories = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[$row['productcategory']][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        p {
            text-align: center;
            color: #666;
        }
        .category {
            margin-bottom: 20px;
        .table-responsive {
            overflow-x: auto;
        }
        .category-header {
            text-align: center;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .product {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .product-item {
            flex-basis: 30%;
            margin: 10px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
        }
        .product-item img {
        table {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px 10px 0 0;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .product-item p {
            margin-top: 10px;
            font-size: 16px;
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .product-item .product-name {
            font-weight: bold;
        th {
            background-color: #343a40;
            color: white;
        }
        .product-item .product-price {
            color: #666;
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e9ecef;
        }
        @media (max-width: 768px) {
            .product-item {
                flex-basis: 100%;
            th, td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Our Products</h1>
        <p>Learn more about our products below:</p>
        <?php foreach ($categories as $categoryName => $products) { ?>
        <div class="category">
            <h2 class="category-header"><?php echo $categoryName; ?></h2>
            <div class="product">
                <?php foreach ($products as $product) { ?>
                <div class="product-item">
                    <img src="https://picsum.photos/200/300" alt="<?php echo $product['productname']; ?>">
                    <p class="product-name"><?php echo $product['productname']; ?></p>
                    <p class="product-price">&#8369; <?php echo $product['productprice']; ?></p>
                </div>
                <?php } ?>
            </div>
        <div class="table-responsive">
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
        <?php } ?>
    </div>
</body>
</html>


/******  024e976e-4ae9-4cde-a060-4f967914efb9  *******/