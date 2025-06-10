<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

// Verify CEO access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('CEO Dashboard - HFC Management');
Page::setBodyClass('ceo-dashboard');

ob_start(); ?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>CEO Executive Dashboard</h1>
        <div class="date-time"><?php echo date('F j, Y'); ?></div>
    </div>

    <div class="dashboard-grid">
        <!-- Company Overview -->
        <div class="dashboard-card company-overview">
            <h2>Company Overview</h2>