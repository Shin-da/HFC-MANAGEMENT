<?php
// Check if user is supervisor
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'supervisor') {
    // Use JavaScript to redirect instead of PHP header()
    $_SESSION['error_message'] = "Unauthorized access";
    echo '<script>window.location.href = "../index.php?error=Unauthorized access";</script>';
    // Don't exit, let the script continue
}
?>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="../assets/images/logo.png" alt="HFC Logo" class="logo">
        <h3>Supervisor Panel</h3>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li class="<?php echo Page::getCurrentPage() === 'index' ? 'active' : ''; ?>">
                <a href="index.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="<?php echo Page::getCurrentPage() === 'inventory' ? 'active' : ''; ?>">
                <a href="inventory.php">
                    <i class="fas fa-boxes"></i>
                    <span>Inventory</span>
                </a>
            </li>
            <li class="<?php echo Page::getCurrentPage() === 'add-stockmovement' ? 'active' : ''; ?>">
                <a href="add.stockmovement.php">
                    <i class="fas fa-plus-circle"></i>
                    <span>Add Stock Movement</span>
                </a>
            </li>
            <li class="<?php echo Page::getCurrentPage() === 'stockactivity' ? 'active' : ''; ?>">
                <a href="stockactivity.php">
                    <i class="fas fa-history"></i>
                    <span>Stock Activity</span>
                </a>
            </li>
            <li class="<?php echo Page::getCurrentPage() === 'products' ? 'active' : ''; ?>">
                <a href="products.php">
                    <i class="fas fa-shopping-basket"></i>
                    <span>Products</span>
                </a>
            </li>
            <li class="<?php echo Page::getCurrentPage() === 'sales' ? 'active' : ''; ?>">
                <a href="sales.php">
                    <i class="fas fa-chart-line"></i>
                    <span>Sales</span>
                </a>
            </li>
            <li class="<?php echo Page::getCurrentPage() === 'customer' ? 'active' : ''; ?>">
                <a href="customer.php">
                    <i class="fas fa-users"></i>
                    <span>Customers</span>
                </a>
            </li>
            <li class="<?php echo Page::getCurrentPage() === 'myaccount' ? 'active' : ''; ?>">
                <a href="myaccount.php">
                    <i class="fas fa-user-circle"></i>
                    <span>My Account</span>
                </a>
            </li>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <div class="user-info">
            <img src="<?php echo isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : '../assets/images/default-avatar.png'; ?>" alt="Profile" class="avatar">
            <div class="user-details">
                <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <span class="role">Supervisor</span>
            </div>
        </div>
        <a href="../login/logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<style>
.sidebar {
    width: 250px;
    height: 100vh;
    background: #2c3e50;
    color: #ecf0f1;
    position: fixed;
    left: 0;
    top: 0;
    display: flex;
    flex-direction: column;
    z-index: 1000;
}

.sidebar-header {
    padding: 20px;
    text-align: center;
    border-bottom: 1px solid #34495e;
}

.sidebar-header .logo {
    width: 80px;
    height: 80px;
    margin-bottom: 10px;
}

.sidebar-header h3 {
    margin: 0;
    font-size: 1.2em;
    color: #ecf0f1;
}

.sidebar-nav {
    flex: 1;
    padding: 20px 0;
    overflow-y: auto;
}

.sidebar-nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar-nav li {
    margin: 5px 0;
}

.sidebar-nav a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #ecf0f1;
    text-decoration: none;
    transition: background-color 0.3s;
}

.sidebar-nav a:hover {
    background-color: #34495e;
}

.sidebar-nav li.active a {
    background-color: #3498db;
}

.sidebar-nav i {
    width: 20px;
    margin-right: 10px;
}

.sidebar-footer {
    padding: 20px;
    border-top: 1px solid #34495e;
}

.user-info {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.user-info .avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
}

.user-details {
    display: flex;
    flex-direction: column;
}

.user-details .username {
    font-weight: bold;
}

.user-details .role {
    font-size: 0.8em;
    color: #bdc3c7;
}

.logout-btn {
    display: flex;
    align-items: center;
    padding: 10px;
    color: #e74c3c;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.logout-btn:hover {
    background-color: #34495e;
}

.logout-btn i {
    margin-right: 10px;
}
</style> 