<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Employee Overview - CEO Dashboard');
Page::setBodyClass('ceo-employees');

ob_start(); ?>

<div class="employees-container">
    <div class="page-header">
        <h1>Employee Overview</h1>
        <div class="header-actions">