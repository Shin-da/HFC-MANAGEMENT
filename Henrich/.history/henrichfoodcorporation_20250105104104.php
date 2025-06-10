<?php
require 'database/dbconnect.php';

// Fetch product information from the productlist table
$sql = "SELECT productcode, productname, productweight, productprice, productcategory FROM productlist";
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 1em;
            text-align: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 10;
        }

        nav {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 20px;
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            margin-top: 60px;
        }

        .hero h1 {
            font-size: 2em;
        }

        .hero p {
            font-size: 1.2em;
        }

        .products {
            padding: 2em;
        }

        .products > h2 {
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        .products > .carousel {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
        }

        .products > .carousel > .carousel-inner {
            display: flex;
            flex-wrap: nowrap;
            width: 100%;
            overflow-x: scroll;
            -webkit-overflow-scrolling: touch;
            padding: 0 20px;
        }

        .products > .carousel > .carousel-inner > .carousel-item {
            width: 25%;
            margin: 0 10px;
        }

        .products > .carousel > .carousel-inner > .carousel-item > img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
        }

        .products > .carousel > .carousel-inner > .carousel-item > h3 {
            font-size: 1.2em;
            margin-top: 10px;
        }

        .products > .carousel > .carousel-inner > .carousel-item > p {
            font-size: 1em;
            margin-top: 10px;
        }

        .products > .carousel > .carousel-control-prev,
        .products > .carousel > .carousel-control-next {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 40px;
            margin: auto;
            background-color: #fff;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1;
        }

        .products > .carousel > .carousel-control-prev {
            left: 0;
        }

        .products > .carousel > .carousel-control-next {
            right: 0;
        }

        .products > .carousel > .carousel-control-prev > .fa,
        .products > .carousel > .carousel-control-next > .fa {
            font-size: 2em;
        }

        .products > .carousel > .carousel-control-prev:hover,
        .products > .carousel > .carousel-control-next:hover {
            background-color: #333;
        }

        @media only screen and (max-width: 600px) {
            .products > .carousel > .carousel-inner > .carousel-item {
                width: 100%;
            }
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
                <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
                <li><a href="#"><i class="fa fa-info-circle"></i> About</a></li>
                <li><a href="#"><i class="fa fa-envelope"></i> Contact</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="hero">
            <h1>Henrich Food Corporation</h1>
            <p>Welcome to our website! We're a food truck company that serves delicious food to the people of Metro Manila.</p>
        </section>
        <section class="products">
            <?php foreach ($categories as $category => $products) : ?>
                <h2><?php echo $category ?></h2>
                <div class="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($products as $product) : ?>
                            <div class="carousel-item">
                                <img src="resources/images/<?php echo $product['productcode'] ?>.jpg" alt="<?php echo $product['productname'] ?>">
                                <h3><?php echo $product['productname'] ?></h3>
                                <p><?php echo $product['productweight'] ?>g - <?php echo $product['productprice'] ?> PHP</p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a class="carousel-control-prev" href="#<?php echo $category ?>"><i class="fa fa-angle-left"></i></a>
                    <a class="carousel-control-next" href="#<?php echo $category ?>"><i class="fa fa-angle-right"></i></a>
                </div>
            <?php endforeach; ?>
        </section>
    </main>
    <footer>
        <p>&copy; 2020 Henrich Food Corporation</p>
    </footer>
</body>

</html>



/*************  ✨ Codeium Command ⭐  *************/
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const carousels = document.querySelectorAll(".carousel");
            carousels.forEach(carousel => {
                const category = carousel.closest(".products").querySelector("h2").textContent;
                const items = carousel.querySelectorAll(".carousel-item");
                const controls = carousel.querySelectorAll(".carousel-control-prev, .carousel-control-next");
                const activeItem = carousel.querySelector(".carousel-item.active");
                const activeIndex = Array.from(items).indexOf(activeItem);
                const nextItem = items[(activeIndex + 1) % items.length];
                const prevItem = items[(activeIndex - 1 + items.length) % items.length];
                controls.forEach(control => {
                    control.addEventListener("click", function() {
                        nextItem.classList.toggle("active");
                        prevItem.classList.toggle("active");
                    });
                });
            });
        });
    </script>
/******  e6184b93-b68c-4024-869c-6da81d682517  *******/