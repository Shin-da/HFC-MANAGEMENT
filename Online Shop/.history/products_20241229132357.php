<?php
require "./database/dbconnect.php";
require "header.php";
require "./session/session.php";

if (isset($_SESSION['accountid'])) {
    $accountId = $_SESSION['accountid'];
    $query = "SELECT * FROM customeraccount WHERE accountid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $accountId);
    $stmt->execute();
    $result = $stmt->get_result();
    $customerAccount = $result->fetch_assoc();
}
$products = array();
$categories = array();

$query = "SELECT * FROM productlist WHERE 1=1";
$params = [];
$types = '';

if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category = $_GET['category'];
    $query .= " AND productcategory = ?";
    $params[] = $category;
    $types .= 's';
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = '%' . $_GET['search'] . '%';
    $query .= " AND (productname LIKE ? OR productcode LIKE ?)";
    $params[] = $search;
    $params[] = $search;
    $types .= 'ss';
}

$stmt = $conn->prepare($query);
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);

foreach ($products as $product) {
    if (!array_key_exists($product['productcategory'], $categories)) {
        $categories[$product['productcategory']] = array();
    }
    $categories[$product['productcategory']][] = $product;
}
?>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f9;
        margin: 0;
        padding: 0;
    }

    .panel {
        height: calc(100vh - 70px);
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        margin-top: 70px;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow-x: hidden;
        overflow-y: auto;
    }

    .container {
        display: block;
        padding: 20px;
        overflow-y: auto;
    }

    h1 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
        font-size: 2rem;
    }

    .search-form {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .search-form input[type="text"],
    .search-form select {
        padding: 15px;
        margin-right: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        width: 200px;
        font-size: 1.2rem;
    }

    .search-form button {
        padding: 15px 20px;
        background-color: #007bff;
        border: none;
        border-radius: 4px;
        color: white;
        cursor: pointer;
        transition: background-color 0.3s;
        font-size: 1.2rem;
    }

    .search-form button:hover {
        background-color: #0056b3;
    }

    .product-item {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 15px;
        transition: transform 0.2s;
        margin-bottom: 20px;
        display: flex;
        flex-direction: row;
        align-items: center;
    }

    .product-item:hover {
        transform: scale(1.05);
    }

    .product-image {
        width: 100px;
        height: 100px;
        border-radius: 8px;
        margin-right: 10px;
    }

    .product-details {
        text-align: left;
        margin-top: 10px;
        display: inline-block;
        flex: 1;
    }

    .product-details h2 {
        margin: 10px 0;
        color: #007BFF;
        font-size: 1.5rem;
    }

    .product-details p {
        margin: 5px 0;
        color: #555;
        font-size: 1.2rem;
    }

    .category-header {
        font-size: 2rem;
        margin-bottom: 10px;
        text-align: center;
        color: #333;
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
        margin-top: 20px;
    }
    @media only screen and (max-width: 600px) {
        .panel {
            width: 100%;
            margin: 0;
            padding: 10px;
        }

        .container {
            padding: 10px;
        }

        .search-form {
            flex-direction: column;
            align-items: center;
        }

        .search-form input[type="text"],
        .search-form select {
            width: 100%;
            margin-bottom: 10px;
        }

        .search-form button {
            width: 100%;
        }

        .product-item {
            flex-direction: column;
        }

        .product-image {
            width: 100%;
            height: 150px;
        }

        .product-details {
            margin-top: 10px;
        }
    }
</style>

<body>
    <?php include "navbar.php"; ?>
    <div class="panel">
        <div class="container">
            <h1>Products</h1>
            <form action="products.php" method="get" class="search-form">
                <input type="text" name="search" placeholder="Search products" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" />
                <button type="submit">Search</button>
                <select name="category" id="category" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php 
                    $allCategoriesQuery = "SELECT DISTINCT productcategory FROM productlist";
                    $allCategoriesResult = $conn->query($allCategoriesQuery);
                    while ($row = $allCategoriesResult->fetch_assoc()) { 
                        $category = $row['productcategory'];
                    ?>
                        <option value="<?php echo $category; ?>" <?php echo isset($_GET['category']) && $_GET['category'] == $category ? 'selected' : ''; ?>><?php echo $category; ?></option>
                    <?php } ?>
                </select>
            </form>
            <?php if (empty($products)) { ?>
                <p style="text-align:center; color:red; font-size:24px;">No products found.</p>
            <?php } else { ?>
                <?php foreach ($categories as $category => $categoryProducts) { ?>
                    <div class="category-header"><?php echo $category; ?></div>
                    <ul>
                    <?php foreach ($categoryProducts as $product) { ?>
                        <li class="product-item">
                            <a href="productdetails.php?productcode=<?php echo $product['productcode']; ?>">
                                <?php if (empty($product['productimage'])) { ?>
                                    <img src="placeholder-image.png" class="product-image" />
                                <?php } else { ?>
                                    <img src="./resources/images/<?php echo $product['productimage']; ?>" class="product-image" />
                                <?php } ?>
                                <div class="product-details">
                                    <h2><?php echo $product['productname']; ?></h2>
                                    <p>Product Code: <?php echo $product['productcode']; ?></p>
                                    <p>Weight: <?php echo $product['productweight']; ?></p>
                                    <p>Price: &#8369;<?php echo $product['productprice']; ?></p>
                                    <p>Pieces per Box: <?php echo $product['piecesperbox']; ?></p>
                                </div>
                            </a>
                        </li>
                    <?php } ?>
                    </ul>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</body>


<!-- <script src="app.js"></script> -->
</html>
 

