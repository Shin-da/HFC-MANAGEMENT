<?php
require 'database/dbconnect.php';

// Fetch product information from the productlist table
$sql = "SELECT * FROM productlist";
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
        .product-item {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px;
            border-radius: 8px;
            transition: transform 0.2s;
            cursor: pointer;
        }
        .product-item:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .product {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
                    <?php foreach ($products as $product) { ?>
                    <div class="product-item" data-category="<?php echo $product['productcategory']; ?>">
                        <img src="https://picsum.photos/200/300" alt="<?php echo $product['productname']; ?>">
                        <p class="product-name"><?php echo $product['productname']; ?></p>
                        <p class="product-price">&#8369; <?php echo $product['productprice']; ?></p>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>

