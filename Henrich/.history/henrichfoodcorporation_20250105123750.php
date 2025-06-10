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
            background-color: #f5f5f5;
            color: #333;
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
            justify-content: center;
        }

        nav li {
            margin-right: 20px;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: #ff6347;
        }

        .hero {
            background-image: linear-gradient(to bottom, #333, #444);
            color: #fff;
            padding: 3em 1em;
            text-align: center;
        }

        .hero h1 {
            font-size: 2.5em;
        }

        .hero p {
            font-size: 1.2em;
            margin-bottom: 1em;
        }

        .shop-now {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ff6347;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .shop-now:hover {
            background-color: #ff4500;
        }

        .product-section {
            padding: 2em 1em;
            text-align: center;
            background-color: #fff;
        }

        .products {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .products>div {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .products>div:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .products>div>img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .products>div:hover>img {
            transform: scale(1.1);
        }

        footer {
            background-color: #333;
            color: #fff;
            padding: 1em;
            text-align: center;
        }

        .see-more {
            background-color: #ff6347;
            padding: 10px 20px;
            border-radius: 5px;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
            margin-top: 20px;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .see-more:hover {
            background-color: #ff4500;
        }

        @media (max-width: 768px) {
            nav ul {
                flex-direction: column;
                align-items: center;
            }

            .products>div {
                width: 100%;
                margin: 10px 0;
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
            <div class="products">
                <?php foreach ($categories as $categoryName => $products) : ?>
                    <div class="category">
                        <h2 class="category-header"><?php echo $categoryName; ?></h2>
                        <div class="carousel" id="<?php echo $categoryName; ?>">
                            <div class="carousel-inner">
                                <?php foreach (array_slice($products, 0, 4) as $index => $product) : ?>
                                    <div class="carousel-item <?php echo $index === 0 ? 'active' : '' ?>">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <img src="./resources/images/<?php echo $product['productimage'] ?>" alt="<?php echo $product['productname'] ?>">
                                                <h3><?php echo $product['productname'] ?></h3>
                                                <p><?php echo $product['productweight'] ?>g - <?php echo $product['productprice'] ?> PHP</p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php foreach (array_slice($products, 4) as $product) : ?>
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
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <a class="see-more" onclick="toggleSeeMore()">See More</a>
        </section>
    </main>
    <footer>
        <p>&copy; 2023 Henrich Food Corporation</p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            var carousels = document.querySelectorAll('.carousel');
            carousels.forEach(function(carousel) {
                var carouselItems = carousel.querySelectorAll('.carousel-item');
                var currentIndex = 0;

                function slideCarousel() {
                    carouselItems.forEach(function(item, index) {
                        item.classList.remove('active');
                        if (index === currentIndex) {
                            item.classList.add('active');
                        }
                    });

                    currentIndex = (currentIndex + 1) % carouselItems.length;

                    setTimeout(slideCarousel, 3000); // slide every 3 seconds
                }

                carouselItems[0].classList.add('active');

                slideCarousel();
            });
        });

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

