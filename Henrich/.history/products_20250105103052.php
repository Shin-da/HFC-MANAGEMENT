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
            display: flex;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .sidebar {
            width: 25%;
            padding: 10px;
        }
        .main-content {
            width: 75%;
            padding: 10px;
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
            .container {
                flex-direction: column;
            }
            .sidebar, .main-content {
                width: 100%;
            }
        }
    </style>
    <script>
        function filterProducts() {
            var input = document.getElementById('searchInput').value.toLowerCase();
            var category = document.getElementById('categorySelect').value;
            var products = document.querySelectorAll('.product-item');
            products.forEach(function(product) {
                var name = product.querySelector('.product-name').textContent.toLowerCase();
                var productCategory = product.getAttribute('data-category');
                if (name.includes(input) && (category === 'all' || category === productCategory)) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Filter Products</h2>
            <input type="text" id="searchInput" onkeyup="filterProducts()" placeholder="Search for products...">
            <h3>Categories</h3>
            <select id="categorySelect" onchange="filterProducts()">
                <option value="all">All</option>
                <?php foreach (array_keys($categories) as $categoryName) { ?>
                    <option value="<?php echo $categoryName; ?>"><?php echo $categoryName; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="main-content">
            <h1>Our Products</h1>
            <p>Learn more about our products below:</p>
            <?php foreach ($categories as $categoryName => $products) { ?>
            <div class="category">
                <h2 class="category-header"><?php echo $categoryName; ?></h2>
                <div class="product">
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

