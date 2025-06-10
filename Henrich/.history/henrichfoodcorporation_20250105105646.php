/*************  ✨ Codeium Command 🌟  *************/
<?php
require 'database/dbconnect.php';

// Fetch product information from the productlist table
$sql = "SELECT productcode, productname, productweight, productprice, productcategory, productimage FROM productlist";
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

        .products {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 2em;
        }

        .product {
        .products > h2 {
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        .products > div {
            margin: 20px;
            width: calc(33.33% - 40px);
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
            animation: fadeIn 1s ease-in-out;
            transition: all 0.3s ease-in-out;
        }

        .product.show {
            opacity: 1;
            transform: translateY(0);
        .products > div > h3 {
            font-size: 1.2em;
            margin-top: 10px;
        }

        .product h3 {
            font-size: 1.2em;
        .products > div > p {
            font-size: 1em;
            margin-top: 10px;
        }

        .product p {
            font-size: 1em;
            margin-top: 10px;
        .products > div > img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
        }

        .product img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
        footer {
            background-color: #333;
            color: #fff;
            padding: 1em;
            text-align: center;
        }
    </style>
</head>

        footer {
            background-color: #333;
            color: #fff;
            padding: 1em;
            text-align: center;
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
        </section>
        <section class="products">
            <?php foreach ($categories as $category => $products) : ?>
                <h2><?php echo $category ?></h2>
                <div class="carousel slide" data-ride="carousel" data-interval="3000">
                    <div class="carousel-inner">
                        <?php foreach ($products as $index => $product) : ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : '' ?>">
                                <img src="./resources/images/<?php echo $product['productimage'] ?>" alt="<?php echo $product['productname'] ?>">
                                <h3><?php echo $product['productname'] ?></h3>
                                <p><?php echo $product['productweight'] ?>g - <?php echo $product['productprice'] ?> PHP</p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a class="carousel-control-prev" href="#<?php echo $category ?>" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#<?php echo $category ?>" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            <?php endforeach; ?>
        </section>
    </main>
    <footer>
        <p>&copy; 2020 Henrich Food Corporation</p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $('.carousel').carousel({
                interval: 3000,
                pause: false,
                ride: true
            });
        });
    </script>
    <style>
        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }
    </style>
</head>
</body>

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
        </section>
        <section class="products">
            <?php foreach ($categories as $category => $products) : ?>
                <h2><?php echo $category ?></h2>
                <?php foreach ($products as $product) : ?>
                    <div class="product">
                        <img src="./resources/images/<?php echo $product['productimage'] ?>" alt="<?php echo $product['productname'] ?>">
                        <h3><?php echo $product['productname'] ?></h3>
                        <p><?php echo $product['productweight'] ?>g - <?php echo $product['productprice'] ?> PHP</p>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </section>
    </main>
    <footer>
        <p>&copy; 2020 Henrich Food Corporation</p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            let products = $('.product');
            let index = 0;
</html>

            function showProduct() {
                products.eq(index).addClass('show');
                index = (index + 1) % products.length;
                setTimeout(showProduct, 3000);
            }

            showProduct();
        });
    </script>
</body>
</html>

/******  7d7946f1-6198-412b-bf9c-689bc7db013e  *******/