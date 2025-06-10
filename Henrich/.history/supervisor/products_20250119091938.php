<?php
require '../database/dbconnect.php';

// Fetch product information from the products table
$sql = "SELECT productcode, productname, productweight, unit_price, productcategory, productimage, productstatus FROM products WHERE productstatus = 'Active'";
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
    <?php require '../reusable/header.php'; ?>
    
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
    <?php include '../reusable/navbarNoSearch.html'; ?>
    <?php include '../reusable/sidebar.php'; ?>

<div class="panel">
    
</div>
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
                    <div class="product-item" data-category="<?php echo $product['productcategory']; ?>" 
                         onclick="window.location.href='product-details.php?id=<?php echo $product['productcode']; ?>'">
                        <img src="<?php echo $product['productimage'] ? 'uploads/products/' . $product['productimage'] : 'https://picsum.photos/200/300'; ?>" 
                             alt="<?php echo $product['productname']; ?>">
                        <p class="product-name"><?php echo $product['productname']; ?></p>
                        <p class="product-weight"><?php echo $product['productweight']; ?> g</p>
                        <p class="product-price">&#8369; <?php echo number_format($product['unit_price'], 2); ?></p>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>

