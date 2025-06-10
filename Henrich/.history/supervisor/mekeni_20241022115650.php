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
                <div class="title" >
                    <span>
                        <h2>Mekeni </h2>
                    </span>
                    <span style="font-size: 12px;"> </span>
                </div>
            </div>

            <div class="table-header">
                


            </div>
        </div>

        <div class="container-fluid">
        <div class="tabs">
  <div class="tab-2">
    <label for="tab2-1">Mekeni</label>
    <input id="tab2-1" name="tabs-two" type="radio" checked="checked">
    <div>
      <h4>Mekeni</h4>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas consequat id velit quis vestibulum. Nam id orci eu urna mollis porttitor. Nunc nisi ante, gravida at velit eu, aliquet
        sodales dui. Sed laoreet condimentum nisi a egestas.<p>Donec interdum ante
        ut enim consequat, quis varius nulla dapibus. Vivamus mollis fermentum augue a varius. Vestibulum in sapien at lectus gravida lobortis vulputate sed metus. Duis scelerisque justo et maximus
        efficitur. Donec eu eleifend quam. Curabitur aliquet commodo sapien eget vestibulum. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vestibulum vel
        aliquet nunc, finibus posuere lorem. Suspendisse consectetur volutpat est ut ornare.</p>
    </div>
  </div>
  <div class="tab-2">
    <label for="tab2-2">Order History</label>
    <input id="tab2-2" name="tabs-two" type="radio">
    <div>
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