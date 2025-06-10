<?php
require_once '../includes/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $employee_id = filter_var($_POST['employee_id'], FILTER_SANITIZE_STRING);
    
    // Check if employee exists and doesn't have an account yet
    $stmt = $conn->prepare("SELECT * FROM employees WHERE employee_id = ? AND email = ?");
    $stmt->bind_param("ss", $employee_id, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Create account request
        $stmt = $conn->prepare("INSERT INTO account_requests (username, email, employee_id, status) VALUES (?, ?, ?, 'pending')");
        $stmt->bind_param("sss", $username, $email, $employee_id);
        
        if ($stmt->execute()) {
            $success = "Account request submitted. Please wait for admin approval.";
                      value="<?php echo $_GET['name']; ?>"><br>
          <?php }else{ ?>
               <input type="text" 
                      name="name" 
                      placeholder="Name"><br>
          <?php }?>

          <label>User Name</label>
          <?php if (isset($_GET['uname'])) { ?>
               <input type="text" 
                      name="uname" 
                      placeholder="User Name"
                      value="<?php echo $_GET['uname']; ?>"><br>
          <?php }else{ ?>
               <input type="text" 
                      name="uname" 
                      placeholder="User Name"><br>
          <?php }?>


     	<label>Password</label>
     	<input type="password" 
                 name="password" 
                 placeholder="Password"><br>

          <label>Re Password</label>
          <input type="password" 
                 name="re_password" 
                 placeholder="Re_Password"><br>

     	<button type="submit">Sign Up</button>
          <a href="index.php" class="ca">Already have an account?</a>
     </form>
</body>
</html>