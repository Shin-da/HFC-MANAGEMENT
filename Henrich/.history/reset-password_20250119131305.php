<?php
session_start();
require_once "config.php";

// Check if password reset token is valid
if (!isset($_GET["token"]) || empty($_GET["token"])) {
    header("Location: login.php");
    exit();
}

$token = $_GET["token"];
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {