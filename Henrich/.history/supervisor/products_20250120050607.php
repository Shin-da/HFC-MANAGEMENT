<?php
// session_start();
require '../includes/session.php';
require '../includes/config.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

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
        .main-panel {
            display: flex;
            gap: 20px;
            padding: 20px;
            background-color: var(--panel-bg);
            margin-top: 60px; /* Adjust based on navbar height */
        }

        .filter-sidebar {
            width: 250px;
            background-color: var(--card-bg);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 80px; /* Adjust based on navbar height + padding */
            height: fit-content;
            flex-shrink: 0;
        }

        .products-container {
            flex-grow: 1;
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Update existing styles */
        .product {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            padding: 10px 0;
        }

        .product-item {
            margin: 0; /* Remove margin since we're using grid gap */
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .main-panel {
                flex-direction: column;
            }

            .filter-sidebar {
                width: 100%;
                position: relative;
                top: 0;
                margin-bottom: 20px;
            }
        }

        .product-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .product-name {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 1.1em;
            margin: 10px 0;
        }

        .product-weight {
            color: var(--text-secondary);
            font-size: 0.9em;
            margin: 5px 0;
        }

        .product-price {
            color: var(--accent);
            font-weight: 600;
            font-size: 1.2em;
            margin-top: auto;
            padding-top: 10px;
            border-top: 1px solid var(--border);
        }

        #searchInput {
            width: 100%;
            padding: 10px;
            border: 2px solid var(--border);
            border-radius: 8px;
            margin-bottom: 20px;
            transition: var(--tran-03);
        }

        #searchInput:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(56, 90, 65, 0.1);
        }

        #categorySelect {
            width: 100%;
            padding: 10px;
            border: 2px solid var(--border);
            border-radius: 8px;
            background-color: var(--card-bg);
            color: var(--text-primary);
            cursor: pointer;
            transition: var(--tran-03);
        }

        #categorySelect:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(56, 90, 65, 0.1);
        }

        .category-header {
            color: var(--primary);
            padding: 15px 0;
            margin: 20px 0;
            border-bottom: 2px solid var(--border);
            font-size: 1.5em;
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                position: relative;
                top: 0;
            }
        }
    </style>
</head>

<body>
    <?php include '../reusable/sidebar.php'; ?>
    <?php include '../reusable/navbar.html'; ?>
    <section class="panel ">
        
        <div class="main-panel">
            <div class="filter-sidebar">
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

            <div class="products-container">
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

        <?php include '../reusable/footer.php'; ?>
    </section>

    <script>
        function filterProducts() {
            // Add your filtering logic here
        }
    </script>
</body>

</html>
