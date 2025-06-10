<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';

// Fetch analytics data
$current_month = date('m');
$current_year = date('Y');

// Get top selling products
$top_products_query = "SELECT 
    p.productname,
    SUM(o.quantity) as total_sold,
    p.quantity as current_stock,
    p.reorderpoint
    FROM products p
    LEFT JOIN orderitems o ON p.productid = o.productid
    WHERE p.supplier = 'Mekeni'
    GROUP BY p.productid
    ORDER BY total_sold DESC
    LIMIT 5";
$top_products = $conn->query($top_products_query);

// Get products that need reordering
$reorder_query = "SELECT 
    productname,
    quantity,
    reorderpoint,
    ROUND(AVG(daily_sales), 2) as avg_daily_sales,
    ROUND(quantity / AVG(daily_sales), 0) as days_until_empty
    FROM (
        SELECT 
            p.productname,
            p.quantity,
            p.reorderpoint,
            DATE(o.orderdate) as sale_date,
            SUM(oi.quantity) as daily_sales
        FROM products p
        LEFT JOIN orderitems oi ON p.productid = oi.productid
        LEFT JOIN customerorder o ON oi.orderid = o.orderid
        WHERE p.supplier = 'Mekeni'
        GROUP BY p.productid, DATE(o.orderdate)
    ) as daily_stats
    GROUP BY productname
    HAVING quantity <= reorderpoint
    ORDER BY days_until_empty ASC";
$reorder_products = $conn->query($reorder_query);

// Get monthly sales trend
$trends_query = "SELECT 
    DATE_FORMAT(o.orderdate, '%Y-%m') as month,
    SUM(oi.quantity * oi.price) as total_sales
    FROM orderitems oi
    JOIN customerorder o ON oi.orderid = o.orderid
    JOIN products p ON oi.productid = p.productid
    WHERE p.supplier = 'Mekeni'
    GROUP BY DATE_FORMAT(o.orderdate, '%Y-%m')
    ORDER BY month DESC
    LIMIT 6";
$trends = $conn->query($trends_query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mekeni Analytics & Order Management</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
    <style>
        button:focus,
        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
        }

        .tabs {
            display: block;
            display: -webkit-flex;
            display: -moz-flex;
            display: flex;
            -webkit-flex-wrap: wrap;
            -moz-flex-wrap: wrap;
            flex-wrap: wrap;
            margin: 0;
            overflow: hidden;
        }

        .tabs [class^="tab"] label,
        .tabs [class*=" tab"] label {
            color: #191212;
            cursor: pointer;
            display: block;
            line-height: 1em;
            padding: 2rem 0;
            text-align: center;
        }

        .tabs [class^="tab"] [type="radio"],
        .tabs [class*=" tab"] [type="radio"] {
            border-bottom: 1px solid rgba(239, 237, 239, 0.5);
            cursor: pointer;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            display: block;
            width: 100%;
            -webkit-transition: all 0.3s ease-in-out;
            -moz-transition: all 0.3s ease-in-out;
            -o-transition: all 0.3s ease-in-out;
            transition: all 0.3s ease-in-out;
        }

        .tabs [class^="tab"] [type="radio"]:hover,
        .tabs [class^="tab"] [type="radio"]:focus,
        .tabs [class*=" tab"] [type="radio"]:hover,
        .tabs [class*=" tab"] [type="radio"]:focus {
            border-bottom: 1px solid var(--accent-color-dark);
        }

        .tabs [class^="tab"] [type="radio"]:checked,
        .tabs [class*=" tab"] [type="radio"]:checked {
            border-bottom: 2px solid var(--accent-color-dark);
        }

        .tabs [class^="tab"] [type="radio"]:checked+div,
                    <span style="font-size: 12px;"> </span>
                </div>
            </div>

        </div>

        <div class="container-fluid">
            <div class="tabs">
                <div class="tab-2">
                    <label for="tab2-1">Mekeni</label>
                    <input id="tab2-1" name="tabs-two" type="radio" checked="checked">
                   <div>
                        <h4>Mekeni</h4>
                        <p> In this section, you can see and manage the orders from the Mekeni</p>
                 
                        <h4>Suggested Products to Order </h4>
                        <p>Here, the system will suggest products to order based on the quantity available and the sales of the products</p>
              
                        <h4>Order to Mekeni</h4>
                        <p> You can add new orders from the Mekeni</p>
                    </div>
                </div>
                <div class="tab-2">
                    <label for="tab2-2"><i class="bx bx-history"></i>Order History</label>
                    <input id="tab2-2" name="tabs-two" type="radio">
                    <div>
                        <h4>Order History</h4>
                        <p>Quisque sit amet turpis leo. Maecenas sed dolor mi. Pellentesque varius elit in neque ornare commodo ac non tellus. Mauris id iaculis quam. Donec eu felis quam. Morbi tristique lorem eget
                            iaculis consectetur. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Aenean at tellus eget risus tempus ultrices. Nam condimentum nisi enim,
                            scelerisque faucibus lectus sodales at.</p>
                            <h4>Order History</h4>
                        <p>Quisque sit amet turpis leo. Maecenas sed dolor mi. Pellentesque varius elit in neque ornare commodo ac non tellus. Mauris id iaculis quam. Donec eu felis quam. Morbi tristique lorem eget
                            iaculis consectetur. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Aenean at tellus eget risus tempus ultrices. Nam condimentum nisi enim,
                            scelerisque faucibus lectus sodales at.</p>
                    </div>
                    
                </div>
            </div>
        </div>


    </section>




</body>
<?php require '../reusable/footer.php'; ?>

</html>