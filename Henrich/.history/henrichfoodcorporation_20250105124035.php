/*************  ✨ Codeium Command 🌟  *************/
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
    padding: 2em;
    text-align: center;
    max-height: 80vh; /* changed to 80vh */
    overflow-y: hidden; /* changed to hidden */
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
        }

        nav li {
            margin: 0 15px;
            transition: transform 0.3s ease;
        .products>h2 {
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        nav a {
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
            position: relative;
            overflow: hidden;
            transition: color 0.3s ease;
            border-radius: 5px;
        }

        nav a:after {
            content: '';
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

        .see-more {
            background-color: #fff;
            padding: 1em;
            width: 100%;
            text-align: center;
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 2px;
            background-color: #fff;
            transform: scaleX(0);
            transition: transform 0.3s ease;
            left: 50%;
            bottom: 0px;
            transform: translateX(-50%);
            display: block;
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            cursor: pointer;
        }
        .see-more a {
            width: 22rem;
          
        }

        nav a:hover:after {
            transform: scaleX(1);
        .see-more:hover {
            background-color: #555;
        }
    </style>
</head>

        .hero {
            background-image: linear-gradient(to bottom, #333, #444);
            color: #fff;
            padding: 4em;
            text-align: center;
            transition: background-image 0.3s ease;
        }
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

        .hero h1 {
            font-size: 3em;
            margin-bottom: 0.5em;
            transition: transform 0.3s ease;
        }
                function slideCarousel() {
                    carouselItems.forEach(function(item, index) {
                        item.classList.remove('active');
                        if (index === currentIndex) {
                            item.classList.add('active');
                        }
                    });

        .hero p {
            font-size: 1.5em;
            transition: transform 0.3s ease;
        }
                    currentIndex = (currentIndex + 1) % carouselItems.length;

        .hero:hover h1,
        .hero:hover p {
            transform: scale(1.05);
        }
                    setTimeout(slideCarousel, 3000); // slide every 3 seconds
                }

        .product-section {
            padding: 2em;
            text-align: center;
            max-height: 80vh;
            overflow-y: hidden;
            transition: all 0.3s ease;
            position: relative;
        }
                carouselItems[0].classList.add('active');

        .product-section.expanded {
            max-height: none;
            overflow-y: auto;
        }
                slideCarousel();
            });
        });

        .carousel-inner {
            display: block;
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
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
</html>

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

        .products>div {
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

        .products>div:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
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
            background-color: #555;
        }

        footer {
            background-color: #333;
            color: #fff;
            padding: 1em;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .see-more {
            background-color: #fff;
            padding: 1em;
            width: 100%;
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
            margin-top: 20px;
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
                                                <img src="./resources/images/<?php echo $product['productimage'] ?>" alt="<?php echo $product['productname'] ?>" class="product-image">
                                                <div class="product-info">
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
                                                <img src="./resources/images/<?php echo $product['productimage'] ?>" alt="<?php echo $product['productname'] ?>" class="product-image">
                                                <div class="product-info">
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

/******  273ceecc-efe9-436e-a3cc-e21537f442a5  *******/