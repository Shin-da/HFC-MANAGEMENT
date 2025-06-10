<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    header('Location: ../login/login.php');
    exit();
}

Page::setTitle('Supply Chain Overview - CEO Dashboard');
Page::setBodyClass('ceo-supply-chain');

ob_start(); ?>

<div class="supply-chain-container">
    <div class="page-header">
        <h1>Supply Chain Management</h1>
        <div class="header-actions">
            <select id="timeframeSelect" class="form-select">
                <option value="weekly">Weekly</option>
                <option value="monthly" selected>Monthly</option>
                <option value="quarterly">Quarterly</option>
            </select>