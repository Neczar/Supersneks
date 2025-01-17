<?php
include 'config.php';
session_start();

if (isset($_SESSION['customer_id'])) {
   // Update logout time in user_sessions table
    date_default_timezone_set('Asia/Manila');
    $customer_id = $_SESSION['customer_id'];
    $logout_time = date('Y-m-d H:i:s');
    mysqli_query($conn, "CALL UpdateSession($customer_id, NULL, '$logout_time')");
    session_unset();
    session_destroy();
}

header('location:login.php');
?>