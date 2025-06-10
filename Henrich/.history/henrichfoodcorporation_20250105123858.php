<?php
require 'database/dbconnect.php';

// Fetch product information from the productlist table
$sql = "SELECT DISTINCT productcode, productname, productweight, productprice, productcategory, productimage FROM productlist";
$result = $conn->query($sql);
$categories = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (!isset($categories[$row['productcategory']])) {
            $categories[$row['productcategory']] = array();
        }
        $categories[$row['productcategory']][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Henrich Food Corporation</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 1em;
            text-align: center;
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: space-around;
        }

        nav li {
            margin-right: 20px;
        }

        nav a {
            color: #fff;
            text-decoration: none;
        }

        .hero {
            background-image: linear-gradient(to bottom, #333, #444);
            color: #fff;
            padding: 2em;
            text-align: center;
        }

        .hero h1 {
            font-size: 2em;
        }

        .hero p {
            font-size: 1.2em;
        }

        .product-section {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 2em;
        }

        .product-section.expanded {
            max-height: none;
        }

        .carousel {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            width: 100%;
        }

        .carousel-inner {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            width: 100%;
        }

        .carousel-item {
            flex-basis: 33.33%;
            margin: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
        }

        .carousel-item img {
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 10px;
        }

        .product-info {
            padding: 10px;
        }

        .product-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .product-price {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        footer {
            background-color: #333;
            color: #fff;
            padding: 1em;
            text-align: center;
        }

        .see-more {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #fff;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .see-more:hover {
            background-color: #555;
        }

        @media (max-width: 768px) {
            .carousel-item {
                flex-basis: 50%;
            }
        }

        @media (max-width: 480px) {
            .carousel-item {
                flex-basis: 100%;
            }
        }
    </style>
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="hero">
            <h1>Henrich Food Corporation</h1>
            <p>Welcome to our website! We're a food truck company that serves delicious food to the people of Metro Manila.</p>
            <a href="../Online Shop/app.php" class="shop-now">Shop Now</a>
        </section>
        <section class="product-section">
            <h2>Our Products</h2>
            <div class="carousel">
                <?php foreach ($categories as $categoryName => $products) : ?>
                    <div class="carousel-inner">
                        <?php foreach ($products as $product) : ?>
                            <div class="carousel-item">
                                <div class="row">
                                    <div class="col-md-4">
                                        <img src="./resources/images/<?php echo $product['productimage'] ?>" alt="<?php echo $product['productname'] ?>">
                                        <h3><?php echo $product['productname'] ?></h3>
                                        <p><?php echo $product['productweight'] ?>g - <?php echo $product['productprice'] ?> PHP</p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <a class="see-more" onclick="toggleSeeMore()">See More</a>
        </section>
    </main>
    <footer>
        <p>&copy; 2020 Henrich Food Corporation</p>
    </footer>
    <script>
        function toggleSeeMore() {
            var productSection = document.querySelector('.product-section');
            var seeMoreButton = productSection.querySelector('.see-more');
            var isExpanded = seeMoreButton.textContent === 'See Less';
            
            if (isExpanded) {
                productSection.classList.remove('expanded'); /* remove expanded class */
            } else {
                productSection.classList.add('expanded'); /* add expanded class */
            }
  
            seeMoreButton.textContent = isExpanded ? 'See More' : 'See Less';
        }
    </script>
</body>

</html>

