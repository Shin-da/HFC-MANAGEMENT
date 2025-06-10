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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
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
            text-align: center;
        }

        .carousel {
            width: 90%;
            margin: 0 auto;
        }

        .carousel-item {
            margin: 20px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
        }

        .carousel-item h3 {
            font-size: 1.2em;
            margin-top: 10px;
        }

        .carousel-item p {
            font-size: 1em;
            margin-top: 10px;
        }

        .carousel-item img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
        }

        @media only screen and (max-width: 600px) {
            .carousel-item {
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
            <div class="carousel">
                <?php foreach ($categories as $category => $products) : ?>
                    <?php foreach ($products as $product) : ?>
                        <div class="carousel-item">
                            <img src="resources/images/<?php echo $product['productcode'] ?>.jpg" alt="<?php echo $product['productname'] ?>">
                            <h3><?php echo $product['productname'] ?></h3>
                            <p><?php echo $product['productweight'] ?>g - <?php echo $product['productprice'] ?> PHP</p>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
    <footer>
        <p>&copy; 2020 Henrich Food Corporation</p>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.carousel').slick({
                slidesToShow: 3,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 2000,
                arrows: true,
                dots: true,
                responsive: [
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                ]
            });
        });
    </script>
</body>
</html>

