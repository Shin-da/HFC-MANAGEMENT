<?php
include "header.php";
include "./database/dbconnect.php";
include "./session/session.php";

if (isset($_SESSION['accountid'])) {
    $accountId = $_SESSION['accountid'];
    $query = "SELECT * FROM customeraccount WHERE accountid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $accountId);
    $stmt->execute();
    $result = $stmt->get_result();
    $customerAccount = $result->fetch_assoc();
}

$product = null;
$orderDescription = null;
if (isset($_GET['productcode'])) {
    $productCode = $_GET['productcode'];
    $query = "SELECT p.*, pl.productimage FROM productlist p JOIN productlist pl ON p.productcode = pl.productcode WHERE p.productcode = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $productCode);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
}
?>
<style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    .panel {
        height: calc(100vh - 70px);
        width: 100%;
        max-width: 1000px;
        padding: 30px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .container {
        padding: 20px;
    }

    h1 {
        text-align: center;
        color: #444;
        margin-bottom: 30px;
        font-size: 28px;
    }

    .product-details {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
        background-color: #f9f9f9;
    }

    .product-image {
        width: 100%;
        max-width: 300px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .product-info {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .product-info p {
        margin-bottom: 10px;
        color: #555;
        font-size: 18px;
    }

    form {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    form label {
        margin-bottom: 10px;
        font-size: 16px;
    }

    form button {
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #007bff;
        border: none;
        border-radius: 5px;
        color: white;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    form button:hover {
        background-color: #0056b3;
    }

    .back-icon {
        position: fixed;
        top: 20px;
        left: 20px;
        font-size: 24px;
        color: #444;
        text-decoration: none;
        font-weight: bold;
        transition: color 0.3s ease-in-out;
        cursor: pointer;
        z-index: 1000;
    }

    .back-icon:hover {
        color: #007bff;
    }

    @media only screen and (max-width: 600px) {
        .panel {
            margin: 20px auto;
        }

        .product-details {
            flex-direction: column;
            align-items: flex-start;
        }

        .product-image {
            width: 100%;
        }

        form {
            width: 100%;
        }
    }
</style>

<body>
    <?php include "navbar.php"; ?>
    <div class="panel">
        <div class="container">
            <span class="back-icon" onclick="window.history.back()">&lt;</span>
            <?php if ($product): ?>
                <h1><?php echo htmlspecialchars($product['productname']); ?></h1>
                <div class="product-details">
                    <img src="<?php echo htmlspecialchars(!empty($product['productimage']) ? $product['productimage'] : 'placeholder-image.png'); ?>" alt="Product Image" class="product-image" onerror="this.src='placeholder-image.png'">
                    <div class="product-info">
                        <p>Product Code: <?php echo htmlspecialchars($product['productcode']); ?></p>
                        <p>Product Category: <?php echo htmlspecialchars($product['productcategory']); ?></p>
                        <p>Product Price: <?php echo htmlspecialchars($product['productprice']); ?></p>
                    </div>
                </div>
            <?php else: ?>
                <p style="display: flex; justify-content: center; align-items: center; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: red; font-size: 24px;">Product not found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

