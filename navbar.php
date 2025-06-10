<style>
  * {
    padding: 0;
    margin: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
    text-decoration: none;
    list-style: none;
  }
  :root {
    --body-color: #E4E9F7;
    --text-color: #3a3a3a;
    --accent-color: #A02334;
    --white: #fff;
    --orange-color: #FFAD60;
  }
  body {
    min-height: 100vh;
    background: var(--body-color);
    color: var(--text-color);
  }
  header {
    background-color: var(--white);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: fixed;
    width: 100%;
    top: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    z-index: 1000;
    padding: 15px 5%;
  }
  .logo {
    display: flex;
    align-items: center;
  }
  .logo img {
    width: 40px;
  }
  .navbar {
    display: flex;
    align-items: center;
  }
  .navbar a {
    color: var(--text-color);
    font-size: 1rem;
    margin: 0 15px;
    transition: color 0.3s;
  }
  .navbar a:hover, .navbar a.active {
    color: var(--accent-color);
  }
  .main {
    display: flex;
    align-items: center;
  }
  .main a {
    margin: 0 15px;
    color: var(--text-color);
    font-size: 1rem;
    transition: color 0.3s;
  }
  .main a:hover {
    color: var(--orange-color);
  }
  #menu-icon {
    font-size: 28px;
    color: var(--text-color);
    cursor: pointer;
    display: none;
  }
  .icon-cart {
    margin: 0 15px;
    display: flex;
    align-items: center;
    color: var(--text-color);
  }
  .icon-cart i {
    font-size: 24px;
    margin-right: 5px;
  }
  .icon-cart span {
    background-color: var(--danger-color);
    color: var(--white);
    width: 2.4vh;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
  }
  @media (max-width: 768px) {
    #menu-icon {
      display: block;
    }
    .navbar {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: var(--white);
      flex-direction: column;
      align-items: flex-start;
      padding: 10px 0;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      display: none;
    }
    .navbar.open {
      display: flex;
    }
    .navbar a {
      margin: 10px 0;
      padding: 10px 20px;
      width: 100%;
    }
    .profile-menu {
      position: absolute;
      top: 100%;
      right: 0;
      background: var(--white);
      flex-direction: column;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      display: none;
    }
    .profile-menu.open {
      display: flex;
    }
    .profile-menu a {
      margin: 10px 0;
      padding: 10px 20px;
      width: 100%;
    }
  }
</style>

<header>
  <div id="menu-icon" class="bx bx-menu"></div>
  <a href="#" class="logo">
    <img src="./resources/images/hfc online logo.png" alt="">
  </a>

  <ul class="navbar">
    <li><i class='bx bx-home'></i><a href="app.php" <?php if (basename($_SERVER['PHP_SELF']) == 'app.php') { echo 'class="active"'; } ?>>Home</a></li>
    <li><i class='bx bx-cart'></i><a href="products.php" <?php if (basename($_SERVER['PHP_SELF']) == 'products.php') { echo 'class="active"'; } ?>>Products</a></li>
  </ul>

  <div class="main">
    <div class="profile-icon" id="profile-icon">
      <i class='bx bx-user'></i>
    </div>
    <div class="profile-menu" id="profile-menu">
      <?php if(isset($_SESSION['accountid'])) { ?>
        <a href='orderhistory.php' <?php if (basename($_SERVER['PHP_SELF']) == 'orderhistory.php') { echo 'class="active"'; } ?>><i class="bx bx-history"></i>Order History</a>
        <a href='profile.php' class='user' <?php if (basename($_SERVER['PHP_SELF']) == 'profile.php') { echo 'style="font-weight: bold;"'; } ?>><i class="bx bx-user"></i>Profile</a>
        <a href='#' class='user' id='logout-link' style='color: red;'><i class="bx bx-log-out"></i>Logout</a>
      <?php } else { ?>
        <a href='./login/signup.php' class='user'><i class="bx bx-user-plus"></i>Sign Up</a>
      <?php } ?>
    </div>
    <div class="icon-cart">
      <i class='bx bx-cart'></i>
      <span>0</span>
    </div>
  </div>
</header>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  const menuIcon = document.getElementById('menu-icon');
  const navbar = document.querySelector('.navbar');
  menuIcon.addEventListener('click', () => {
    navbar.classList.toggle('open');
  });

  const profileIcon = document.getElementById('profile-icon');
  const profileMenu = document.getElementById('profile-menu');
  profileIcon.addEventListener('click', () => {
    profileMenu.classList.toggle('open');
  });

  const logoutLink = document.getElementById('logout-link');
  logoutLink.addEventListener('click', (event) => {
    event.preventDefault();
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, logout!'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = './login/logout.php';
      }
    });
  });
</script>

