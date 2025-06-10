<style>
    html {
        background-color: #96CEB4;
    }

    .body {
        background-color: #f2f2f2;
        margin: 10px;
        margin-bottom: 20px;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #a94442;
    }

    .alert {
        background-color: #dff0d8;
        padding: 10px;
        border-radius: 5px;
        color: #3c763d;
        border: 1px solid #3c763d;
    }

    .alert-danger {
        background-color: #f2dede;
        border-color: #ebccd1;
        color: #a94442;
    }

    .alert-success {
        background-color: #dff0d8;
        border-color: #d6e9c6;
        color: #3c763d;
    }

    .output-table {
        border-collapse: collapse;
        width: 100%;
    }

    .output-table td,
    .output-table th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    .output-table tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .output-table th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #2196F3;
        color: white;
    }
</style>

<?= "<div class='body'>"; ?>
<?php
/*************  ✨ Codeium Command 🌟  *************/

if (!file_exists('./database/dbconnect.php')) {
    die("Warning: require(./database/dbconnect.php): Failed to open stream: No such file or directory");
}

require './database/dbconnect.php';

if (!file_exists('./session/session.php')) {
    die("Warning: require(./session/session.php): Failed to open stream: No such file or directory");
}

require './session/session.php';


/******  4d3da905-caf8-4c82-93ab-ea997e57ffbe  *******/