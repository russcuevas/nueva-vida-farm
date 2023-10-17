<?php
include '../database/connection.php';

session_start();
$response = array();

if (!isset($_SESSION['customer_id'])) {
    $response['status'] = 'error';
} elseif (isset($_POST['selected_products'])) {
    $selectedProducts = json_decode($_POST['selected_products'], true);

    foreach ($selectedProducts as $productId) {
        if (!is_numeric($productId)) {
            $response['status'] = 'error';
            break;
        }

        $get_order_items_query = "SELECT * FROM `tbl_orderitem` WHERE `product_id` = :product_id";
        $stmt = $conn->prepare($get_order_items_query);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalQuantity = 0;
        foreach ($orderItems as $orderItem) {
            $totalQuantity += $orderItem['quantity'];
        }

        $update_product_stocks_query = "UPDATE `tbl_product` SET `product_stocks` = `product_stocks` + :totalQuantity WHERE `product_id` = :product_id";
        $stmt = $conn->prepare($update_product_stocks_query);
        $stmt->bindParam(':totalQuantity', $totalQuantity);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();

        if ($totalQuantity >= 5) {
            $status = "Available";
        } elseif ($totalQuantity >= 1 && $totalQuantity <= 4) {
            $status = "Low Stock";
        } else {
            $status = "Not Available";
        }

        $update_product_status_query = "UPDATE `tbl_product` SET `product_status` = :status WHERE `product_id` = :product_id";
        $stmt = $conn->prepare($update_product_status_query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();

        $remove_order_items_query = "DELETE FROM `tbl_orderitem` WHERE `product_id` = :product_id";
        $stmt = $conn->prepare($remove_order_items_query);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
    }

    $response['status'] = 'success';
} else {
    $response['status'] = 'error';
}

header('Content-Type: application/json');

echo json_encode($response);
exit();
