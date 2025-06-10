<?php
require_once 'access_control.php';
require_once '../includes/Page.php';
require_once '../includes/functions.php';

// Initialize page
Page::setTitle('System Settings - HFC Admin');
Page::setBodyClass('admin-page');
Page::setCurrentPage('settings');
Page::setAdminPage(true);

// Add required styles
Page::addStyle('../assets/css/admin.css');

// Add required scripts
Page::addScript('../assets/js/settings.js');

ob_start();
?>

<div class="container-fluid">
    <section class="panel">
        <div class="container-fluid">
            <div class="table-header">
                <div class="title">
                    <span>
                        <h2>System Settings</h2>
                    </span>
                    <span style="font-size: 12px;">Configure system-wide settings</span>
                </div>
                <div class="title">
                    <span><?php echo date('l, F jS'); ?></span>
                </div>
            </div>

            <div class="settings-container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">General Settings</h5>
                            </div>
                            <div class="card-body">
                                <form id="generalSettingsForm">
                                    <div class="form-group">
                                        <label for="siteName">Site Name</label>
                                        <input type="text" class="form-control" id="siteName" name="site_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="siteDescription">Site Description</label>
                                        <textarea class="form-control" id="siteDescription" name="site_description" rows="3"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="contactEmail">Contact Email</label>
                                        <input type="email" class="form-control" id="contactEmail" name="contact_email" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="contactPhone">Contact Phone</label>
                                        <input type="tel" class="form-control" id="contactPhone" name="contact_phone">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save General Settings</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Email Settings</h5>
                            </div>
                            <div class="card-body">
                                <form id="emailSettingsForm">
                                    <div class="form-group">
                                        <label for="smtpHost">SMTP Host</label>
                                        <input type="text" class="form-control" id="smtpHost" name="smtp_host">
                                    </div>
                                    <div class="form-group">
                                        <label for="smtpPort">SMTP Port</label>
                                        <input type="number" class="form-control" id="smtpPort" name="smtp_port">
                                    </div>
                                    <div class="form-group">
                                        <label for="smtpUsername">SMTP Username</label>
                                        <input type="text" class="form-control" id="smtpUsername" name="smtp_username">
                                    </div>
                                    <div class="form-group">
                                        <label for="smtpPassword">SMTP Password</label>
                                        <input type="password" class="form-control" id="smtpPassword" name="smtp_password">
                                    </div>
                                    <div class="form-group">
                                        <label for="smtpEncryption">SMTP Encryption</label>
                                        <select class="form-control" id="smtpEncryption" name="smtp_encryption">
                                            <option value="tls">TLS</option>
                                            <option value="ssl">SSL</option>
                                            <option value="">None</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save Email Settings</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Security Settings</h5>
                            </div>
                            <div class="card-body">
                                <form id="securitySettingsForm">
                                    <div class="form-group">
                                        <label for="sessionTimeout">Session Timeout (minutes)</label>
                                        <input type="number" class="form-control" id="sessionTimeout" name="session_timeout" min="5" max="1440">
                                    </div>
                                    <div class="form-group">
                                        <label for="maxLoginAttempts">Maximum Login Attempts</label>
                                        <input type="number" class="form-control" id="maxLoginAttempts" name="max_login_attempts" min="1" max="10">
                                    </div>
                                    <div class="form-group">
                                        <label for="lockoutDuration">Lockout Duration (minutes)</label>
                                        <input type="number" class="form-control" id="lockoutDuration" name="lockout_duration" min="5" max="1440">
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="require2FA" name="require_2fa">
                                            <label class="custom-control-label" for="require2FA">Require Two-Factor Authentication</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save Security Settings</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Backup Settings</h5>
                            </div>
                            <div class="card-body">
                                <form id="backupSettingsForm">
                                    <div class="form-group">
                                        <label for="backupFrequency">Backup Frequency</label>
                                        <select class="form-control" id="backupFrequency" name="backup_frequency">
                                            <option value="daily">Daily</option>
                                            <option value="weekly">Weekly</option>
                                            <option value="monthly">Monthly</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="backupTime">Backup Time</label>
                                        <input type="time" class="form-control" id="backupTime" name="backup_time">
                                    </div>
                                    <div class="form-group">
                                        <label for="backupRetention">Backup Retention (days)</label>
                                        <input type="number" class="form-control" id="backupRetention" name="backup_retention" min="1" max="365">
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="autoBackup" name="auto_backup">
                                            <label class="custom-control-label" for="autoBackup">Enable Automatic Backup</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save Backup Settings</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
