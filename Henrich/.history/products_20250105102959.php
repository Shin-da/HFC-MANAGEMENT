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
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px 10px 0 0;
        }
        .product-item p {
            margin-top: 10px;
            font-size: 16px;
        }
        .product-item .product-name {
            font-weight: bold;
        }
        .product-item .product-price {
            color: #666;
        }
        @media (max-width: 768px) {
            .product-item {
                flex-basis: 100%;
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
        </div>
        <?php } ?>
    </div>
</body>
</html>

