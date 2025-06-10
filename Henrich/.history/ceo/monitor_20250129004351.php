<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('System Monitor - CEO Dashboard');
Page::setBodyClass('ceo-monitor');

ob_start(); ?>

<div class="monitor-container">
    <div class="page-header">
        <h1>System Monitor</h1>
        <div class="header-actions">