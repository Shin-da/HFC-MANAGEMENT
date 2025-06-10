<!-- Admin Page -->
<?php
require '../reusable/redirect404.php';

session_start();
if (isset($_SESSION['uid']) && isset($_SESSION['role'])) {
    $pageTitle = 'Admin Page';
    $pageDescription = 'Admin page to manage users';
    require '../reusable/header.php';

    // Sidebar 
    include 'admin-sidebar.php';

    // Top navbar
    include '../reusable/navbar.html';

    // Table container
    echo '<div class="table-container">';
    echo '<div class="content-header">';
    echo '<h2>Users</h2>';
    echo '</div>';
    echo '<table class="table">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>UID</th>';
    echo '<th>Name</th>';
    echo '<th>Role</th>';
    echo '<th>Actions</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    require '../database/dbconnect.php';

    $sql = "SELECT uid, name, role FROM user";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr class='clickable-row'> ";
            echo "<td>" . $row["uid"] . "</td>";
            echo "<td>" . $row["name"] . "</td>";
            echo "<td>" . $row["role"] . "</td>";
            echo "<td>";
            echo "<a href='editUser.php?uid=" . $row["uid"] . "'>
                         <i class='bx bx-edit'>
                         </i>
                    </a>";
            echo "<a href='delete.php?id=" . $row["uid"] . "'>
                    <i class='bx bx-trash'></i></a>";
            echo "</td> ";
            echo "</tr>";
        }
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>';

    // Footer
    include '../reusable/footer.php';

} else {
    header("Location: index.php");
    exit();
}

