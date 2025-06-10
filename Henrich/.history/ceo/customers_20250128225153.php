<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Customer Analytics - CEO Dashboard');
Page::setBodyClass('ceo-customers');

ob_start(); ?>

<div class="customers-container">
    <div class="page-header">
        <h1>Customer Analytics</h1>
        <div class="header-actions">
            <select id="analysisRange" class="form-select">
                <option value="30">Last 30 Days</option>
                <option value="90">Last Quarter</option>