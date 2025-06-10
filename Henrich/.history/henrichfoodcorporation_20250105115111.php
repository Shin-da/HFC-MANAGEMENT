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

        .carousel-inner {
            display: block;
            position: relative;
            width: 100%;
            height: 100%;
        }

        .carousel-item {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: none;
        }

        .carousel-item.active {
            display: block;
        }

        .carousel-item img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }

        .product-image {
            width: 100%;
            height: 150px;
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

        .products {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            background-color: #f2f2f2;
            flex-direction: row;
            padding: 2em;
        }

        .products>h2 {
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        .products>div {
            background-color: #fff;
            margin: 20px;
            padding: 20px;
            width: 33.33%;
            height: 500px;

        }

        .products>div>h3 {
            font-size: 1.2em;
            margin-top: 10px;
        }

        .products>div>p {
            font-size: 1em;
            margin-top: 10px;
        }

        .products>div>img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            transition: all 0.3s ease-in-out;
        }

        .products>div:hover>img {
            transform: scale(1.1);
        }

        .shop-now {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #333;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .shop-now:hover {
            background-color: #555;
        }

        img {
            max-width: 400px;
        }

        footer {
            background-color: #333;
            color: #fff;
            padding: 1em;
            text-align: center;
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
            <div class="products">
                <?php foreach ($categories as $categoryName => $products) : ?>
                    <div class="category">
                        <h2 class="category-header"><?php echo $categoryName; ?></h2>
                        <div class="carousel" id="<?php echo $categoryName; ?>">
                            <div class="carousel-inner">
                                <?php foreach (array_unique($products, SORT_REGULAR) as $index => $product) : ?>
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
                            </div>
                            <!-- <a class="carousel-control-prev" href="#<?php echo $categoryName; ?>" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#<?php echo $categoryName; ?>" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a> -->
                        </div>
                    </div>
                <?php endforeach; ?>
        </section>
        </section>
    </main>
    <footer>
        <p>&copy; 2020 Henrich Food Corporation</p>
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
    </script>
</body>

</html>