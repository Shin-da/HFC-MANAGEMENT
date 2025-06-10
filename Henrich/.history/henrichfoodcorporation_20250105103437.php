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

        .products > h2 {
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        .products > ul {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .products > ul > li {
        .products li {
            margin: 20px;
            width: calc(33.33% - 40px);
        }

        .products > ul > li > h3 {
            font-size: 1.2em;
            margin-top: 10px;
        }

        .products > ul > li > p {
            font-size: 1em;
            margin-top: 10px;
        }

        .products > ul > li > img {
        .products img {
            width: 150px;
            height: 150px;
            object-fit: cover;
        }

        .products h3 {
            font-size: 1.2em;
            margin-top: 10px;
        }

        .products p {
            font-size: 1em;
            margin-top: 10px;
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
        </section>
        <section class="products">
            <?php foreach ($categories as $category => $products) : ?>
                <h2><?php echo $category ?></h2>
                <ul>
            <h2>Our Products</h2>
            <ul>
                <?php foreach ($categories as $category => $products) : ?>
                    <?php foreach ($products as $product) : ?>
                        <li>
                            <img src="resources/images/<?php echo $product['productcode'] ?>.jpg" alt="<?php echo $product['productname'] ?>">
                            <h3><?php echo $product['productname'] ?></h3>
                            <p><?php echo $product['productweight'] ?>g - <?php echo $product['productprice'] ?> PHP</p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </section>
    </main>
    <footer>
        <p>&copy; 2020 Henrich Food Corporation</p>
    </footer>
</body>

</html>


/******  4fb953a1-6528-4b6a-9ce2-2999740a6c27  *******/