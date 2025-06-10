<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Compliance & Risk Management - CEO Dashboard');
Page::setBodyClass('ceo-compliance');

ob_start(); ?>

<div class="compliance-container">
    <div class="page-header">
        <h1>Compliance & Risk Management</h1>
        <div class="header-actions">
            <select id="riskLevel" class="form-select">
                <option value="all">All Risk Levels</option>