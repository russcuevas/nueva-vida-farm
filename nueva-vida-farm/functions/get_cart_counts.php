<?php
include '../database/connection.php';
session_start();

$response = array();

if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];
    $getCartCount = "SELECT COUNT(*) AS cart_count FROM `tbl_orderitem` WHERE `customer_id` = $customer_id";
    $stmtCartCount = $conn->query($getCartCount);
    $cartCount = $stmtCartCount->fetch(PDO::FETCH_ASSOC);

    $response['status'] = 'success';
    $response['cart_count'] = $cartCount['cart_count'];
} else {
    $response['status'] = 'error';
    $response['message'] = 'User is not logged in';
}

header('Content-Type: application/json');
echo json_encode($response);
