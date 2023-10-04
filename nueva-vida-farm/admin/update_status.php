<?php
include '../database/connection.php';

session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location: admin_login.php');
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['order_status'])) {
    $orderID = $_POST['order_id'];
    $newStatus = $_POST['order_status'];

    $sqlGetCurrentStatus = "SELECT status FROM tbl_orderstatus WHERE order_id = :order_id";
    $stmtGetCurrentStatus = $conn->prepare($sqlGetCurrentStatus);
    $stmtGetCurrentStatus->bindParam(":order_id", $orderID, PDO::PARAM_INT);
    $stmtGetCurrentStatus->execute();
    $currentStatus = $stmtGetCurrentStatus->fetchColumn();

    if ($newStatus !== $currentStatus) {
        $sqlUpdate = "UPDATE tbl_orderstatus SET status = :status, update_date = NOW() WHERE order_id = :order_id";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bindParam(":status", $newStatus, PDO::PARAM_STR);
        $stmtUpdate->bindParam(":order_id", $orderID, PDO::PARAM_INT);

        if ($stmtUpdate->execute()) {
            echo "Status updated successfully for order ID $orderID.";
        } else {
            echo "Error updating status for order ID $orderID: ";
        }
    } else {
        header('location: orders.php');
    }
} else {
    header('location: orders.php');
}
