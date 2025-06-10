/*************  ✨ Codeium Command 🌟  *************/
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
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 2em;
        }

        .products > h2 {
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        .products > .carousel {
            position: relative;
        .products > ul {
            display: flex;
            flex-wrap: wrap;
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
        .products > ul > li {
            margin: 20px;
            width: calc(33.33% - 40px);
        }

        .products > .carousel > .carousel-inner > .carousel-item {
            width: 25%;
            margin: 0 10px;
        .products > ul > li > h3 {
            font-size: 1.2em;
            margin-top: 10px;
        }

        .products > .carousel > .carousel-inner > .carousel-item > img {
        .products > ul > li > p {
            font-size: 1em;
            margin-top: 10px;
        }

        .products > ul > li > img {
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
            .products > ul > li {
                width: 100%;
            }
        }

        .more {
            text-align: center;
            padding: 20px;
            font-size: 1.2em;
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
                <ul>
                    <?php foreach (array_slice($products, 0, 3) as $product) : ?>
                        <li>
                            <img src="resources/images/<?php echo $product['productcode'] ?>.jpg" alt="<?php echo $product['productname'] ?>">
                            <h3><?php echo $product['productname'] ?></h3>
                            <p><?php echo $product['productweight'] ?>g - <?php echo $product['productprice'] ?> PHP</p>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="more">
                    <?php if (count($products) > 3) : ?>
                        <a href="#">View more</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </section>
    </main>
    <footer>
        <p>&copy; 2020 Henrich Food Corporation</p>
    </footer>
</body>

</html>



/******  5f59f86a-a4c2-497d-a4f8-e47758103a69  *******/