<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Company Settings - CEO Dashboard');
Page::setBodyClass('ceo-settings');
Page::addStyle('/assets/css/ceo/settings.css');

ob_start(); ?>

<div class="settings-container">
    <div class="page-header">
        <h1>Company Settings</h1>
        <button id="saveAllSettings" class="btn primary">
            <i class="bx bx-save"></i> Save All Changes
        </button>
    </div>

    <div class="settings-grid">
        <div class="settings-card general">
            <h2>General Settings</h2>
            <form id="generalSettingsForm">
                <div class="form-group">
                    <label for="companyName">Company Name</label>
                    <input type="text" id="companyName" name="companyName" value="Henrich Food Corporation">
                </div>
                <div class="form-group">
                    <label for="fiscalYear">Fiscal Year Start</label>
                    <input type="month" id="fiscalYear" name="fiscalYear">
                </div>
                <div class="form-group">
                    <label for="timezone">Timezone</label>
                    <select id="timezone" name="timezone">
                        <option value="Asia/Manila">Philippines (GMT+8)</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="settings-card security">
            <h2>Security Settings</h2>
            <form id="securitySettingsForm">
                <div class="form-group">
                    <label>Session Timeout</label>
                    <select name="sessionTimeout">
                        <option value="30">30 minutes</option>
                        <option value="60">1 hour</option>
                        <option value="120">2 hours</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Password Policy</label>
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="requireSpecialChars"> Require Special Characters
                        </label>
                        <label>
                            <input type="checkbox" name="requireNumbers"> Require Numbers
                        </label>
                    </div>
                </div>
            </form>
        </div>

        <div class="settings-card notifications">
            <h2>Notification Settings</h2>
            <div id="notificationSettings"></div>
        </div>

        <div class="settings-card system">
            <h2>System Information</h2>
            <div class="info-grid" id="systemInfo"></div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>
