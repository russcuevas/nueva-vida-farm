<?php
include '../database/connection.php';

session_start();
if (!isset($_SERVER['HTTP_REFERER'])) {
    header('location: ../shop');
    exit();
}

$customer_id = $_SESSION['customer_id'];
if (!isset($customer_id)) {
    header('location: ../home');
}

$query = "SELECT product_id, product_stocks FROM tbl_product";
$result = $conn->query($query);

if ($result) {
    $products = $result->fetchAll(PDO::FETCH_ASSOC);
} else {
    $products = [];
}

echo json_encode($products);
