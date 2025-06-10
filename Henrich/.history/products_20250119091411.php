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

