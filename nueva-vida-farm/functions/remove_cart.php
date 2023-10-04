<?php
include '../database/connection.php';

session_start();
$response = array();

if (!isset($_SESSION['customer_id'])) {
    header('location: ../login.php');
} elseif (isset($_GET['order_item_id'])) {
    $order_item_id = $_GET['order_item_id'];

    if (!is_numeric($order_item_id)) {
        $response['status'] = 'error';
    } else {
        $get_order_item_query = "SELECT * FROM `tbl_orderitem` WHERE `order_item_id` = :order_item_id";
        $stmt = $conn->prepare($get_order_item_query);
        $stmt->bindParam(':order_item_id', $order_item_id);
        $stmt->execute();
        $order_item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order_item) {
            $removed_quantity = $order_item['quantity'];
            $product_id = $order_item['product_id'];

            $update_product_stocks_query = "UPDATE `tbl_product` SET `product_stocks` = `product_stocks` + :removed_quantity WHERE `product_id` = :product_id";
            $stmt = $conn->prepare($update_product_stocks_query);
            $stmt->bindParam(':removed_quantity', $removed_quantity);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->execute();

            $get_product_stocks_query = "SELECT `product_stocks` FROM `tbl_product` WHERE `product_id` = :product_id";
            $stmt = $conn->prepare($get_product_stocks_query);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->execute();
            $productStock = $stmt->fetchColumn();

            if ($productStock >= 5) {
                $status = "Available";
            } elseif ($productStock >= 1 && $productStock <= 4) {
                $status = "Low Stock";
            } else {
                $status = "Not Available";
            }

            $update_product_status_query = "UPDATE `tbl_product` SET `product_status` = :status WHERE `product_id` = :product_id";
            $stmt = $conn->prepare($update_product_status_query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->execute();

            $remove_order_item_query = "DELETE FROM `tbl_orderitem` WHERE `order_item_id` = :order_item_id";
            $stmt = $conn->prepare($remove_order_item_query);
            $stmt->bindParam(':order_item_id', $order_item_id);
            $stmt->execute();

            $response['status'] = 'success';
        } else {
            $response['status'] = 'error';
        }
    }
} else {
    header('location: ../home.php');
}

header('Content-Type: application/json');

echo json_encode($response);
exit();
