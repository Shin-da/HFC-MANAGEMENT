<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
<nav class="admin-nav">
    <div class="nav-brand">
        <img src="../resources/images/hfclogo.png" alt="HFC Logo" height="40">
        <span>HFC Management</span>
    </div>
    <ul class="nav-links">
        <li><a href="../admin/index.php">Dashboard</a></li>
        <li><a href="../admin/manage-supervisors.php">Supervisors</a></li>
        <li><a href="../admin/manage-account-requests.php">Account Requests</a></li>
        <li><a href="../admin/system-settings.php">Settings</a></li>
    </ul>
    <div class="nav-user">
        <span>Welcome, Admin</span>
        <a href="../login/logout.php" class="btn btn-logout">Logout</a>
    </div>
</nav>

<style>
.admin-nav {
    background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
    padding: 1rem 2rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.nav-brand {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.nav-brand span {
    font-weight: 600;
    color: var(--background-warm);
}

.nav-links {
    display: flex;
    gap: 2rem;
    list-style: none;
}

.nav-links a {
    text-decoration: none;
    color: var(--background-light);
    font-weight: 500;
    transition: color 0.3s ease;
}

.nav-links a:hover {
    color: var(--accent-gold);
}

.nav-user {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.btn-logout {
    padding: 0.5rem 1rem;
    background: var(--secondary-dark);
    color: white;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}