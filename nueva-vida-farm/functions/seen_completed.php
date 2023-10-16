<?php
include '../database/connection.php';

session_start();

if (!isset($_SERVER['HTTP_REFERER'])) {
    header('location: ../home');
    exit();
}

if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];

    if (isset($_POST['markAsSeen'])) {
        $sql = "UPDATE tbl_user_reports SET is_Seen = 1 WHERE customer_id = :customer_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update']);
        }
    } else {
        header('location: ../home');
    }
} else {
    header('location: ../login');
}
