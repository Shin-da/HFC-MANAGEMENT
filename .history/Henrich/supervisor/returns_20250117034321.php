/*************  ✨ Codeium Command 🌟  *************/
<?php
include_once '../database/dbconnect.php';
require '../reusable/redirect404.php';
require '../session/session.php';

if (isset($_POST['submit'])) {
    $order_id = $_POST['order_id'];
    $reason = $_POST['reason'];
    $message = $_POST['message'];
    $user_id = $_SESSION['user_id'];

    $query = "INSERT INTO returns (order_id, user_id, reason, message) VALUES ('$order_id', '$user_id', '$reason', '$message')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        header('Location: returns.php?success=1');
        exit;
    } else {
        header('Location: returns.php?error=1');
        exit;
    }
}

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success = 'Thank you for submitting your return request. We will contact you soon.';
} elseif (isset($_GET['error']) && $_GET['error'] == 1) {
    $error = 'Something went wrong. Please try again.';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Returns</title>
    <script>
        function search() {
            var input, filter, table, tr, td, i;
            input = document.getElementById("search");
            filter = input.value.toUpperCase();
            table = document.getElementById("returns_table");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[1];

                if (td) {
                    if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/fullcalendar.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/locales-all.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/weather-js/2.0.0/weather.js"></script>
</head>
<body>

<h2>Returns</h2>

<?php if (isset($success)) { ?>
    <div style="color: green;"><?php echo $success; ?></div>
<?php } elseif (isset($error)) { ?>
    <div style="color: red;"><?php echo $error; ?></div>
<?php } ?>

<!-- Table showing records from returns table -->
<table id="returns_table">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer Name</th>
            <th>Order Description</th>
            <th>Order Total</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $query = "SELECT rid, order_id, customername, orderdescription, ordertotal, status FROM returns";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>' .
                        '<td>' . $row['order_id'] . '</td>' .
                        '<td>' . $row['customername'] . '</td>' .
                        '<td>' . $row['orderdescription'] . '</td>' .
                        '<td>$' . $row['ordertotal'] . '</td>' .
                        '<td>' . $row['status'] . '</td>' .
                        '</tr>';
                }
            } else {
                echo "<tr><td colspan='6' style='text-align: center;'>No results found.</td></tr>";
            }
        ?>
    </tbody>
</table>

<!-- Modal for processing return order -->
<div id="myModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h2>Process Return Order</h2>
        <form action="returns.php" method="post">
            <label>Order ID:</label>
            <select name="order_id" required>
                <option value="">Select</option>
                <?php
                    $query = "SELECT hid, customername, orderdescription, ordertotal, status FROM customerorder where status = 'Pending' or status = 'Processing'";
                    $result = mysqli_query($conn, $query);
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<option value="' . $row['hid'] . '">' . $row['customername'] . ' - ' . $row['orderdescription'] . ' - $' . $row['ordertotal'] . ' - ' . $row['status'] . '</option>';
                        }
                    }
                ?>
            </select><br><br>

            <label>Reason:</label>
            <select name="reason" required>
                <option value="">Select</option>
                <option value="Bad quality">Bad quality</option>
                <option value="Wrong item">Wrong item</option>
                <option value="Other">Other</option>
            </select><br><br>

            <label>Message:</label>add callendar and weather section based on location
            <textarea name="message" required></textarea><br><br<|end_header|>
            <textarea name="message" required></textarea><br><br>

            <input type="submit" name="submit" value="Submit">
        </form>
    </div>
</div>

<script>
    var modal = document.getElementById("myModal");

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
<div class="overview">
        <div class="chart-container">
            <div class="chart-title">Calendar and Weather</div>
            <div id="calendar" style="max-width: 60%;"></div>
            <div id="weather" style="margin-top: 20px;"></div>
        </div>
    </div>
</body>
<script>
    $(document).ready(function() {
      var calendarEl = document.getElementById('calendar');
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        initialDate: moment(),
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,dayGridWeek,dayGridDay'
        }
      });
      calendar.render();

      var location = 'Pampanga, Philippines';
      var unit = 'c';
      var lang = 'en';
      var weather = new Weather(location, unit, lang);
      weather.getWeatherData(function(data) {
        var html = `<p>Current weather in <strong>${data.location}</strong> is <strong>${data.currently}</strong> with a temperature of <strong>${data.temperature} ${data.unit}</strong>.</p>`;
        $('#weather').html(html);
      });
    });
  </script>
</html>


/******  83cc837c-94fd-4dd5-a51c-5a80bd549679  *******/