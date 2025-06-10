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
        .sidebar {
            background-color: var(--sidebar-bg);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-right: 20px;
        }

        .product-item {
            background-color: var(--card-bg);
            border: 1px solid var(--border);
            padding: 15px;
            margin: 10px;
            border-radius: 8px;
            transition: var(--tran-04);
            cursor: pointer;
            width: 250px;
        }

        .product-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .product-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .product {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: flex-start;
        }

        .product-name {
            color: var(--text-primary);
            font-weight: 600;
            margin: 8px 0;
        }

        .product-weight {
            color: var(--text-secondary);
            font-size: 0.9em;
        }

        .product-price {
            color: var(--accent);
            font-weight: 600;
            font-size: 1.1em;
            margin-top: 8px;
        }

        .category-header {
            color: var(--primary);
            padding: 10px 0;
            margin: 20px 0;
            border-bottom: 2px solid var(--border);
        }

        #searchInput {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid var(--border);
            border-radius: 4px;
            background-color: var(--card-bg);
            color: var(--text-primary);
        }

        #categorySelect {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--border);
            border-radius: 4px;
            background-color: var(--card-bg);
            color: var(--text-primary);
        }
    </style>
</head>

<body>
    <?php include '../reusable/navbarNoSearch.html'; ?>

    <div class="panel">
        <?php include '../reusable/sidebar.php'; ?>

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
    </div>
</body>

</html>