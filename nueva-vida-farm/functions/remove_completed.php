<?php
include '../database/connection.php';

session_start();

$customer_id = $_SESSION['customer_id'];
if (!isset($customer_id)) {
    header('location: ../home');
}

if (isset($_GET['report_id'])) {
    $report_id = $_GET['report_id'];

    $stmt = $conn->prepare("SELECT * FROM `tbl_user_reports` WHERE report_id = ? AND customer_id = ?");
    $stmt->execute([$report_id, $customer_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $delete_stmt = $conn->prepare("DELETE FROM `tbl_user_reports` WHERE report_id = ?");
        $delete_stmt->execute([$report_id]);

        $_SESSION['delete_completed'] = 'Order deleted successfully';
        header('location: ../completed_orders');
    } else {
        header('location: ../login');
    }
}
