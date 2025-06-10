<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Mekeni</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
</head>

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
    .tabs [class*=" tab"] [type="radio"]:checked+div {
        opacity: 1;
    }

    .tabs [class^="tab"] [type="radio"]+div,
    .tabs [class*=" tab"] [type="radio"]+div {
        display: block;
        opacity: 0;
        padding: 2rem 0;
        width: 90%;
        -webkit-transition: opacity 0.3s ease-in-out;
        -moz-transition: opacity 0.3s ease-in-out;
        -o-transition: opacity 0.3s ease-in-out;
        transition: opacity 0.3s ease-in-out;
    }

    .tabs .tab-2 {
        width: 50%;
    }

    .tabs .tab-2 [type="radio"]+div {
        width: 200%;
        margin-left: 200%;
    }

    .tabs .tab-2 [type="radio"]:checked+div {
        margin-left: 0;
    }

    .tabs .tab-2:last-child [type="radio"]+div {
        margin-left: 100%;
    }

    .tabs .tab-2:last-child [type="radio"]:checked+div {
        margin-left: -100%;
    }
</style>

<body>
    <?php include '../reusable/sidebar.php';   // Sidebar   
    ?>

    <!-- === Orders === -->
    <section class=" panel">
        <?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR         
        ?>

        <div class="container-fluid"> <!-- Stock Management -->
            <div class="table-header" style="justify-content: center">
                <div class="title" style="color:var(--accent-color-dark)">
                    <span>
                       <img src="../resources/images/Mekeni-Ph-Logo.svg" alt="" style="width: 100px; height: 100px;">
                    </span>
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