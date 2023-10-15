<?php
include '../database/connection.php';

session_start();

if (!isset($_SERVER['HTTP_REFERER'])) {
    header('location: ../home');
    exit();
}

if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];

    $sql = "SELECT COUNT(*) as count FROM tbl_user_reports WHERE customer_id = :customer_id AND is_Seen = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':customer_id', $customer_id);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit();
} else {
    header('location: ../home');
    exit();
}
