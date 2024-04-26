<?php
session_start();
require 'customerSupport.php';

if (!isset($_SESSION['email'])) {
    echo "<script>alert('Session Expired. Please Log in again.');</script>";
    header("Location: ../index.php");
    exit();
}
