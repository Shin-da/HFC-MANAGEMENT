<?php
require_once 'access_control.php';
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $conn->prepare("UPDATE system_settings SET value = ? WHERE name = ?");
        foreach ($_POST['settings'] as $name => $value) {
            $stmt->bind_param("ss", $value, $name);
            $stmt->execute();
        }
        $_SESSION['success'] = "Settings updated successfully";
        header("Location: system-settings.php");
        exit();
    } catch (Exception $e) {
        $error = "Error updating settings: " . $e->getMessage();
    }
}

// Get current settings
$settings = $conn->query("SELECT * FROM system_settings")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - HFC Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/admin_header.php'; ?>
    
    <div class="container">
        <h2>System Settings</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <form method="POST" class="settings-form">
            <?php foreach ($settings as $setting): ?>
                <div class="form-group">
                    <label for="<?= $setting['name'] ?>"><?= ucwords(str_replace('_', ' ', $setting['name'])) ?></label>
                    <input type="text" 
                           name="settings[<?= $setting['name'] ?>]" 
                           value="<?= htmlspecialchars($setting['value']) ?>" 
                           required>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>

    <?php include '../includes/admin_footer.php'; ?>
</body>
</html>
