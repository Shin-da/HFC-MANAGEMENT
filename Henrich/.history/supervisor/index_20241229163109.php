<?php
require '../reusable/redirect404.php';
require '../session/session.php';
include "../database/dbconnect.php";
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<!DOCTYPE html>
<html>

<head>
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>HOME</title>
     <?php include '../reusable/header.php'; ?>

     <style>
          @media (max-width: 425px) {
               .container {
                    width: 100%;
                    height: 100vh;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    background: #f3f2f2;
               }
               .left-panel {
                    flex-direction: column;
                    align-items: center;
                    width: 100%;
                    height: 100%;
               }
               .left {
                    width: 100%;
                    height: 30%;
               }
               .left img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
               }
               .title {
                    text-align: center;
                    margin-bottom: 20px;
               }
               .title h1 {
                    font-size: 24px;
                    font-weight: 500;
               }
               .title p {
                    color: rgba(#000, .5);
                    font-size: 14px;
                    font-weight: 400;
               }
               .login-form {
                    padding: 20px;
                    background: #fefefe;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    width: 100%;
                    height: 70%;
                    border-radius: 4px;
                    box-shadow: 0px 2px 6px -1px rgba(0, 0, 0, .12);
               }
               .input-group {
                    display: flex;
                    flex-direction: column;
                    align-items: flex-start;
                    width: 100%;
               }
               .input-group .icon {
                    padding: 10px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 100%;
                    border-bottom: solid 1px #515151;
               }
               .input-group .icon i {
                    font-size: 16px;
                    color: #999;
               }
               .wave-group {
                    position: relative;
                    width: 100%;
               }
               .wave-group .input {
                    font-size: 16px;
                    padding: 10px 10px 10px 5px;
                    display: block;
                    width: 100%;
                    border: none;
                    border-bottom: solid 1px #515151;
                    background: transparent;
               }
               .wave-group .input:focus {
                    outline: none;
               }
               .wave-group .label {
                    color: #999;
                    font-size: 18px;
                    font-weight: normal;
                    position: absolute;
                    pointer-events: none;
                    left: 5px;
                    top: 10px;
                    display: flex;
               }
               .wave-group .label-char {
                    transition: 0.2s ease all;
                    transition-delay: calc(var(--index) * .05s);
               }
               .wave-group .input:focus~label .label-char,
               .wave-group .input:valid~label .label-char {
                    transform: translateY(-20px);
                    font-size: 14px;
                    color: #5264AE;
               }
               .wave-group .bar {
                    position: relative;
                    display: block;
                    width: 100%;
               }
               .wave-group .bar:before,
               .wave-group .bar:after {
                    content: '';
                    height: 2px;
                    width: 0;
                    bottom: 1px;
                    position: absolute;
                    background: #5264AE;
                    transition: 0.2s ease all;
                    -moz-transition: 0.2s ease all;
                    -webkit-transition: 0.2s ease all;
               }
               .wave-group .bar:before {
                    left: 50%;
               }
               .wave-group .bar:after {
                    right: 50%;
               }
               .wave-group .input:focus~.bar:before,
               .wave-group .input:focus~.bar:after {
                    width: 50%;
               }
          }
     </style>

<?php include '../reusable/footer.php'; ?>

</html>


