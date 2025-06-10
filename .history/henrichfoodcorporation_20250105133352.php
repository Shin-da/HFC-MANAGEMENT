<?php
require './Henrich/database/dbconnect.php';
include './Henrich/reusable/header.php';
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
            overflow-x: hidden;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 1em;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }

        nav li {
            margin: 0 15px;
            transition: transform 0.3s ease;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            position: relative;
            overflow: hidden;
            transition: color 0.3s ease;
        }

        nav a:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 2px;
            background-color: #fff;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        nav a:hover:after {
            transform: scaleX(1);
        }

        .hero {
            background-image: linear-gradient(to bottom, rgba(51,51,51,0.8), rgba(68,68,68,0.8)), url('./Henrich/resources/images/warehouse.png');
            background-size: cover;
            background-position: center;
            transition: background-image 0.3s ease;
            color: #fff;
            padding: 4em;
            text-align: center;
        }

        .hero h1 {
            font-size: 3em;
            margin-bottom: 0.5em;
            transition: transform 0.3s ease;
        }

        .hero p {
            font-size: 1.5em;
            transition: transform 0.3s ease;
        }

        .hero:hover h1,
        .hero:hover p {
            transform: scale(1.05);
        }

        .product-section {
            padding: 2em;
            text-align: center;
            max-height: 80vh;
            overflow-y: hidden;
            transition: all 0.3s ease;
            position: relative;
        }

        .product-section.expanded {
            max-height: none;
            overflow-y: auto;
        }

        .carousel-inner {
            display: block;
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .carousel-item {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: none;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        .carousel-item.active {
            display: block;
            opacity: 1;
        }

        .carousel-item img {
            width: 100%;
            height: auto;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .carousel-item img:hover {
            transform: scale(1.1);
        }

        .product-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .product-info {
            border: 1px solid var(--accent-color);
            padding: 10px;
        }

        .product-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            transition: color 0.3s ease;
        }

        .product-name:hover {
            color: #333;
        }

        .product-price {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }

        .products {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            background-color: #f2f2f2;
        }

        .products .category {
            background-color: #fff;
            margin: 20px;
            padding: 20px;
            width: 30%;
            min-width: 250px;
            height: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .products .category:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .products .category img {
            width: 100%;
            height: 260px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 10px;
            transition: transform 0.3s ease;
        }

        .shop-now {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .shop-now:hover {
            background-color: var(--accent-color);
        }

        footer {
            background-color: #333;
            color: #fff;
            padding: 1em;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .see-more {
            padding: 1em;
            width: 50%;
            margin-bottom: 20px;
            text-align: center;
            position: absolute;
            left: 50%;
            bottom: 0;
            transform: translateX(-50%);
            display: block;
            padding: 10px;
            background-color: #333;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .see-more:hover {
            background-color: #555;
            transform: translateX(-50%) scale(1.05);
        }

        @media (max-width: 768px) {
            .products>div {
                width: 90%;
            }

            .hero h1 {
                font-size: 2em;
            }

            .hero p {
                font-size: 1.2em;
            }

            nav ul {
                flex-direction: column;
            }

            nav li {
                margin: 10px 0;
            }
        }
    </style>

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
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
        <section class="hero" data-aos="fade-up">
            <h1>Henrich Food Corporation</h1>
            <p>Welcome to our website! We're a food truck company that serves delicious food to the people of Metro Manila.</p>
            <a href="../Online Shop/app.php" class="shop-now">Shop Now</a>
        </section>
        
        <section class="about" data-aos="fade-up">
            <h2>About Us</h2>

            
            
        </section>
        <section class="product-section" data-aos="fade-up">
            <h2>Our Products</h2>
            <div class="products">
                <?php foreach ($categories as $categoryName => $products) : ?>
                    <script>
                        console.log('Products:', '<?php echo $categoryName; ?>', <?php echo json_encode($products); ?>);
                    </script>
                    <div class="category" data-aos="zoom-in">
                        <h2 class="category-header"><?php echo $categoryName; ?></h2>
                        <div class="carousel" id="<?php echo $categoryName; ?>">
                            <div class="carousel-inner">
                                <?php foreach (array_slice($products, 0, 4) as $index => $product) : ?>
                                    <div class="carousel-item <?php echo $index === 0 ? 'active' : '' ?>">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <img src="./Henrich/resources/images/<?php echo $product['productimage'] ?>" alt="<?php echo $product['productname'] ?>" class="product-image" data-aos="zoom-in">
                                                <div class="product-info" data-aos="fade-up">
                                                    <h3 class="product-name"><?php echo $product['productname'] ?></h3>
                                                    <p><?php echo $product['productweight'] ?>g - <?php echo $product['productprice'] ?> PHP</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php foreach (array_slice($products, 4) as $product) : ?>
                                    <div class="carousel-item">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <img src="./Henrich/resources/images/<?php echo $product['productimage'] ?>" alt="<?php echo $product['productname'] ?>" class="product-image" data-aos="zoom-in">
                                                <div class="product-info" data-aos="fade-up">
                                                    <h3 class="product-name"><?php echo $product['productname'] ?></h3>
                                                    <p><?php echo $product['productweight'] ?>g - <?php echo $product['productprice'] ?> PHP</p>
                                                </div>
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

        <section class="contactus" data-aos="fade-up">
            <h2>Contact Us</h2>
            <p>For any inquiries or feedback, please contact us at <a href="mailto:K4AqM@example.com">K4AqM@example.com</a> or call us at <a href="tel:+1234567890">+123 456 7890</a>.</p>
        </section>
    </main>
    <footer>
        <p>&copy; 2020 Henrich Food Corporation</p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {
            AOS.init();
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

