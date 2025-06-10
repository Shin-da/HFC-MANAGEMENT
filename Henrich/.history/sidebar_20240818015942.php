<style>
    .menu-bar .menu li:last-child {
        border-bottom: none;
    }

    .menu-bar .menu .active {
        background: var(--accent-color);
        color: white;
    }

    .menu-bar .menu .active i {
        color: white;
    }

    .menu-bar .menu .active .nav-text {
        color: white;
    }
</style>

         
 <nav class="sidebar close hidden">
     <header>

         <div class="image-text">
             <span class="image">
                 <img draggable="false" src="images/hfclogo.png" alt="logo">
             </span>

             <div class="text header-text">
                 <span class="name">Jeff</span>
                 <span class="profession"> Management</span>
             </div>
         </div>

     </header>

     <div class="menu-bar">
         <li class="" >
             <i class="bx bx-user icon" ></i>
             <span class="text nav-text" style="color: var(--accent-color);">
                 <?php echo strtoupper($_SESSION['username']); ?>
             </span>
             <span class="text nav-text" style="color: var(--accent-color);">
                 <?php echo strtoupper($_SESSION['username']); ?>
             </span> 
        </li>

         <div class="menu">

             <ul class="menu-links">
                 <li class="nav-link " id="home">
                     <a href="home.php">
                         <i class="bx bx-home-alt icon"></i>
                         <span class="text nav-text">Dashboard</span>
                     </a>
                 </li>
                 <li class="nav-link" id="orders">
                     <a href="orders.php">
                         <i class='bx bx-cart-alt icon'></i>
                         <span class="text nav-text">Transactions</span>
                     </a>
                 </li>
                 <li class="nav-link" id="inventory">
                     <a href="inventory.php">
                         <i class='bx bx-food-menu icon'></i>
                         <span class="text nav-text">Inventory</span>
                     </a>
                 </li>
                 <li class="nav-link" id="agents">
                     <a href="customer.php">
                         <i class='bx bx-user icon'></i>
                         <span class="text nav-text">Customers</span>
                     </a>
                 </li>
                 <li class="nav-link" id="agents">
                     <a href="listofproducts.php">
                         <i class='bx bx-package icon'></i>
                         <span class="text nav-text">Products</span>
                     </a>
                 </li>

             </ul>

         </div>

         <script>
             var links = document.querySelectorAll(".nav-link");

             links.forEach(function(link) {
                 if (link.querySelector("a").href === window.location.href) {
                     link.classList.add("active");
                 }
             });
         </script>
         

         <div class="bottom-content" style="display: none;">
             <li class>
                 <a href="logout.php">
                     <i class="bx bx-log-out icon"></i>
                     <span class="text nav-text">Logout</span>
                 </a>
             </li>
             <li class="mode">
                 <div class="moon-sun">
                     <i class="bx bx-moon icon moon"></i>
                     <i class="bx bx-sun icon sun "></i>
                 </div>
                 <span class="mode-text text">Dark mode</span>

                 <div class="toggle-switch">
                     <span class="switch"></span>
                 </div>

             </li>
         </div>

     </div>
 </nav>