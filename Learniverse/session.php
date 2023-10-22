<?php
session_start();
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Session Expired. Please Log in again.');</script>";
    header("Location: index.php");
    exit();
}
