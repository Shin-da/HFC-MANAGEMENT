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
            <select id="payrollPeriod" class="form-select">
                <option value="current">Current Period</option>
                <option value="previous">Previous Period</option>
                <option value="custom">Custom Range</option>
            </select>
            <button id="exportPayroll" class="btn secondary">
                <i class="bx bx-download"></i> Export Report
            </button>
        </div>
    </div>

    <div class="payroll-grid">
        <div class="payroll-card summary">
            <h2>Payroll Summary</h2>
            <div class="metrics-grid">
                <div class="metric">
                    <span class="metric-title">Total Payroll</span>
                    <span class="metric-value" id="totalPayroll">₱0</span>