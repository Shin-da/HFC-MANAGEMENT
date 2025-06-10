<?php
require '../session/session.php'; // Add this line to validate session
require '../database/dbconnect.php';

// Check if user is logged in
if (!isset($_SESSION['uid'])) {
    header("Location: ../login/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: products.php');
    exit();
}

$productId = $_GET['id'];
$sql = "SELECT * FROM products WHERE productcode = ? AND productstatus = 'Active'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $productId);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header('Location: products.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require '../reusable/header.php'; ?>
    <style>
        .product-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            display: flex;
            gap: 40px;
            background-color: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            flex: 1;
            max-width: 500px;
        }

        .product-image img {
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .product-details {
            flex: 1;
            padding: 20px;
            color: var(--text-primary);
        }

        .product-details h1 {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 2.5em;
        }

        .product-details p {
            margin: 15px 0;
            font-size: 1.1em;
            padding: 8px 0;
            border-bottom: 1px solid var(--border);
        }

        .category {
            color: var(--secondary);
        }

        .price {
            color: var(--accent);
            font-size: 1.5em;
            font-weight: 600;
        }

        .back-button {
            margin: 20px;
            padding: 12px 24px;
            background-color: var(--primary);
            color: var(--light);
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            transition: var(--tran-03);
        }

        .back-button:hover {
            background-color: var(--secondary);
            transform: translateX(-5px);
        }
    </style>
</head>

<body>
    <?php include '../reusable/sidebar.php'; ?>
    <div class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>
        <div class="product-container">
            <a href="products.php" class="back-button">← Back to Products</a>
            <div class="product-image">
                <img src="<?php echo $product['productimage'] ? 'uploads/products/' . $product['productimage'] : 'https://picsum.photos/500/500'; ?>"
                    alt="<?php echo $product['productname']; ?>">
            </div>
            <div class="product-details">
                <h1><?php echo $product['productname']; ?></h1>
                <p class="category">Category: <?php echo $product['productcategory']; ?></p>
                <p class="weight">Weight: <?php echo $product['productweight']; ?> g</p>
                <p class="price">Price: &#8369; <?php echo number_format($product['unit_price'], 2); ?></p>
                <p class="pieces">Pieces per box: <?php echo $product['piecesperbox']; ?></p>
            </div>
        </div>
    </div>
</body>

</html>