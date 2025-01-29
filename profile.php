<?php
include "header.php";
include "./database/dbconnect.php";
include "./session/session.php";
include "sweetalert.php";
if (isset($_SESSION['accountid'])) {
    $accountId = $_SESSION['accountid'];
    $query = "SELECT * FROM customeraccount WHERE accountid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $accountId);
    $stmt->execute();
    $result = $stmt->get_result();
    $customerAccount = $result->fetch_assoc();
}
if (isset($_GET['success'])) {
    $success = $_GET['success'];
    echo "<script>
            swal({
                title: '$success',
                icon: 'success',
                button: 'OK',
            });
        </script>";
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
    echo "<script>
            swal({
                title: '$error',
                icon: 'error',
                button: 'OK',
            });
        </script>";
}
?>
<style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Roboto', sans-serif;
        font-size: 1rem;
        background-color: #f4f4f9;
        margin: 0;
        padding: 0;
    }

    .panel {
        background-color: #fff;
        padding: 2rem;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        max-width: 800px;
        margin: 3rem auto;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .container {
        overflow-y: scroll;
        width: 100%;
        padding: 3rem;
    }

    .profile {
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .profile h1 {
        color: #333;
        margin-bottom: 1rem;
        font-size: 1.5rem;
        text-align: left;
    }

    .profile-info {
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        width: 100%;
        justify-content: space-between;
    }

    .profile-pic {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-right: 1rem;
        border: 1px solid #ddd;
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .profile-pic img {
        width: 10rem;
        height: 10rem;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #007bff;
        margin-bottom: 0.5rem;
    }

    .profile-details {
        display: flex;
        flex-direction: column;
    }

    .profile-details p {
        margin: 0.5rem 0;
        color: #555;
        font-size: 1rem;
    }

    .profile-details label {
        font-weight: bold;
        margin-right: 0.5rem;
    }

    .profile-info button {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 0.5rem 1rem;
        font-size: 1rem;
        cursor: pointer;
        border-radius: 0.25rem;
        margin-top: 1rem;
    }

    .profile-info button:hover {
        background-color: #0069d9;
    }

    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 0.5rem;
        margin-bottom: 1rem;
        border: 1px solid #ccc;
        border-radius: 0.25rem;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
        outline: none;
        border-color: #007bff;
    }

    #upload-picture-form {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        margin-top: 1rem;
    }

    #upload-picture-form input[type="file"] {
        display: none;
    }

    #upload-picture-form label {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 0.5rem 1rem;
        font-size: 1rem;
        cursor: pointer;
        border-radius: 0.25rem;
        margin-top: 1rem;
    }

    #upload-picture-form label:hover {
        background-color: #0069d9;
    }

    #upload-picture-form button {
        display: none;
        margin-top: 1rem;
    }

    #preview {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 1rem;
    }

    #preview-label {
        display: none;
    }

    #preview img {
        width: 7.5rem;
        height: 7.5rem;
        border-radius: 50%;
        object-fit: cover;
        border: 3px dashed #007bff;
        margin-right: 0;
    }

    #preview p {
        font-size: 1rem;
        color: #555;
        margin-top: 0.5rem;
    }


    @keyframes load8 {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    @media only screen and (max-width: 600px) {
        .panel {
            padding: 1rem;
            margin: 1.5rem;
        }

        .profile-info {
            flex-direction: column;
            align-items: center;
        }

        .profile-pic {
            margin: 0 0 1rem 0;
        }
    }
</style>

<body>
    <?php include "navbar.php"; ?>
    <div class="error-message">
        <?php
            if (isset($_GET['error'])) {
                echo '<p>' . $_GET['error'] . '</p>';
            } else if (isset($_GET['success'])) {
                echo '<p style="color:green;">' . $_GET['success'] . '</p>';
            }
        ?>
    </div>
    <div class="panel">
        <div class="container">
            <div class="profile">
                <h1>My Profile</h1>
                <div class="profile-info">
                    <?php if (isset($customerAccount)) { ?>
                        <div class="profile-pic">
                            <p id="preview-label">Preview : </p>
                            <img src="<?php echo htmlspecialchars($customerAccount['profilepicture']); ?>" alt="Profile Picture">
                            <form id="upload-picture-form" action="uploadpicture.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="accountId" value="<?php echo htmlspecialchars($customerAccount['accountid']); ?>">
                                <label for="profile-picture">Upload Profile Picture</label>
                                <input type="file" name="profile-picture" id="profile-picture" onchange="showPreview(event)">
                                <button type="submit" id="upload-button">Upload</button>
    
                            </form>
                        </div>
                        <form action="updateprofile.php" method="post">
                            <div class="profile-details">
                                <p><label>Account ID:</label> <?php echo htmlspecialchars($customerAccount['accountid']); ?></p>
                                <p><label>Email:</label> <?php echo htmlspecialchars($customerAccount['useremail']); ?></p>
                                <p><label>Username:</label> <input type="text" name="username" value="<?php echo htmlspecialchars($customerAccount['username']); ?>"></p>
                                <p><label>Name:</label> <input type="text" name="customername" value="<?php echo htmlspecialchars($customerAccount['customername']); ?>"></p>
                                <p><label>Address:</label> <input type="text" name="customeraddress" value="<?php echo htmlspecialchars($customerAccount['customeraddress']); ?>"></p>
                                <p><label>Phone Number:</label> <input type="text" name="customerphonenumber" value="<?php echo htmlspecialchars($customerAccount['customerphonenumber']); ?>"></p>
                            </div>
                            <button type="submit">Update</button>
                        </form>
                    <?php } else { ?>
                        <script>
                            swal({
                                title: "Please log in to view your profile.",
                                icon: "error",
                                button: "OK",
                            });
                        </script>
       <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <script>
            // Clear URL parameters
            if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.pathname);
        }
    </script>
    <script>
        function showPreview(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const image = document.querySelector('.profile-pic img');
                image.src = reader.result;
                document.getElementById('preview-label').style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
            document.getElementById('upload-button').style.display = 'none';
            document.getElementById('preview').style.display = 'block';
        }
    </script>

</body>
<script>
    function showPreview(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const image = document.querySelector('.profile-pic img');
            image.src = reader.result;
            document.getElementById('preview-label').style.display = 'block';
            image.style.border = '3px dashed #919191';
            document.getElementById('upload-button').style.display = 'block';
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
</html>


