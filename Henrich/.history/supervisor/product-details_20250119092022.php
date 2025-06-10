<?php
require '

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['productname']; ?> - Details</title>
    <style>
        .product-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            gap: 30px;
        }
        .product-image {
            flex: 1;
            max-width: 500px;
        }
        .product-image img {
            width: 100%;
            border-radius: 8px;
        }
        .product-details {
            flex: 1;
            padding: 20px;
        }
        .back-button {
            margin: 20px;
            padding: 10px 20px;
            background-color: #333;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <a href="products.php" class="back-button">← Back to Products</a>
    
    <div class="product-container">
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
</body>
</html>
