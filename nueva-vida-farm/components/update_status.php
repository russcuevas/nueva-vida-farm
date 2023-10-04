<?php
include '../database/connection.php';

session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location: admin_login.php');
    exit();
}

date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_status = $_POST['order_status'];
    $order_id = $_POST['order_id'];

    $sql = "SELECT reference_number FROM tbl_order WHERE order_id = :order_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':order_id', $order_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $reference_number = $result['reference_number'];

        $update_sql = "UPDATE tbl_orderstatus SET status = :status, update_date = NOW() WHERE order_id IN (
            SELECT order_id FROM tbl_order WHERE reference_number = :reference_number
        )";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bindParam(':status', $order_status);
        $update_stmt->bindParam(':reference_number', $reference_number);

        if ($update_stmt->execute()) {
            $_SESSION['update_status'] = 'Status updated successfully';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        } else {
            echo "<script>window.alert ('Error updating status');</script>";
            echo "<script>window.location.href = ('../admin/orders.php');</script>";

        }
    } else {
        echo "<script>window.alert ('Order not found');</script>";
        echo "<script>window.location.href = ('../admin/orders.php');</script>";

    }
} else {
    header('Location: ../admin/orders.php');
    exit();
}
