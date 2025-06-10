<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Payroll Overview - CEO Dashboard');
Page::setBodyClass('ceo-payroll');

ob_start(); ?>

<div class="payroll-container">
    <div class="page-header">
        <h1>Payroll Overview</h1>
        <div class="header-actions">