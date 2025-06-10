<?php
require_once 'access_control.php';
require_once '../includes/config.php';

// Get current settings with error handling
try {
    $settings = $conn->query("SELECT * FROM system_settings ORDER BY name ASC");
    if (!$settings) {
        throw new Exception("Settings table not found. Please run database setup.");
    }
    $settings = $settings->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $error = "Error loading settings: " . $e->getMessage();
    $settings = [];
}

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
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($settings)): ?>
            <form method="POST" class="settings-form">
                <?php foreach ($settings as $setting): ?>
                    <div class="form-group">
                        <label for="<?= $setting['name'] ?>">
                            <?= ucwords(str_replace('_', ' ', $setting['name'])) ?>
                            <?php if ($setting['description']): ?>
                                <small class="text-muted"><?= htmlspecialchars($setting['description']) ?></small>
                            <?php endif; ?>
                        </label>
                        <input type="text" 
                               id="<?= $setting['name'] ?>"
                               name="settings[<?= $setting['name'] ?>]" 
                               value="<?= htmlspecialchars($setting['value']) ?>" 
                               required>
                    </div>
                <?php endforeach; ?>
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>
        <?php else: ?>
            <div class="alert alert-warning">
                No settings found. Please run the database setup script at: 