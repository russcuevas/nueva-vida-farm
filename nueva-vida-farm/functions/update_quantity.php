<?php
include '../database/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $orderItemId = isset($_POST['order_item_id']) ? $_POST['order_item_id'] : null;
    $newQuantity = isset($_POST['new_quantity']) ? intval($_POST['new_quantity']) : null;

    if ($orderItemId !== null && $newQuantity !== null) {
        try {
            $getCurrentQuantitySql = "SELECT product_id, quantity FROM tbl_orderitem WHERE order_item_id = :order_item_id";
            $stmt = $conn->prepare($getCurrentQuantitySql);
            $stmt->bindParam(':order_item_id', $orderItemId, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $productId = $row['product_id'];
                $currentQuantity = $row['quantity'];

                $quantityChange = $newQuantity - $currentQuantity;

                $updateQuantitySql = "UPDATE tbl_orderitem SET quantity = :new_quantity WHERE order_item_id = :order_item_id";
                $stmt = $conn->prepare($updateQuantitySql);
                $stmt->bindParam(':new_quantity', $newQuantity, PDO::PARAM_INT);
                $stmt->bindParam(':order_item_id', $orderItemId, PDO::PARAM_INT);
                $stmt->execute();

                $updateStockSql = "UPDATE tbl_product SET product_stocks = product_stocks - :quantity_change WHERE product_id = :product_id";
                $stmt = $conn->prepare($updateStockSql);
                $stmt->bindParam(':quantity_change', $quantityChange, PDO::PARAM_INT);
                $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
                $stmt->execute();

                $getProductStockSql = "SELECT product_stocks FROM tbl_product WHERE product_id = :product_id";
                $stmt = $conn->prepare($getProductStockSql);
                $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
                $stmt->execute();
                $productStock = $stmt->fetchColumn();

                if ($productStock >= 5) {
                    $status = "Available";
                } elseif ($productStock >= 1 && $productStock <= 4) {
                    $status = "Low Stock";
                } else {
                    $status = "Not Available";
                }

                $updateStatusSql = "UPDATE tbl_product SET product_status = :status WHERE product_id = :product_id";
                $stmt = $conn->prepare($updateStatusSql);
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
                $stmt->execute();

                $rowCount = $stmt->rowCount();
                if ($rowCount > 0) {
                    echo json_encode(array('status' => 'success', 'message' => 'Quantity updated successfully.'));
                } else {
                    echo json_encode(array('status' => 'error', 'message' => 'No rows were updated.'));
                }
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Invalid order item.'));
            }
        } catch (PDOException $e) {
            echo json_encode(array('status' => 'error', 'message' => 'Database error: ' . $e->getMessage()));
        }
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Invalid or missing data.'));
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Invalid request method.'));
    header('location: ../login.php');
}
