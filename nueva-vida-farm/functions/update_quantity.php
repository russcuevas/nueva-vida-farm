<?php
include '../database/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $orderItemId = isset($_POST['order_item_id']) ? $_POST['order_item_id'] : null;
    $newQuantity = isset($_POST['new_quantity']) ? intval($_POST['new_quantity']) : null;

    if ($orderItemId !== null && $newQuantity !== null) {
        try {
            $getStockAndQuantitySql = "
                SELECT
                    p.product_id,
                    p.product_stocks,
                    oi.quantity
                FROM tbl_orderitem oi
                INNER JOIN tbl_product p ON oi.product_id = p.product_id
                WHERE oi.order_item_id = :order_item_id
            ";
            $stmt = $conn->prepare($getStockAndQuantitySql);
            $stmt->bindParam(':order_item_id', $orderItemId, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $productId = $row['product_id'];
                $productStocks = $row['product_stocks'];
                $currentQuantity = $row['quantity'];

                $quantityChange = $newQuantity - $currentQuantity;

                if ($productStocks - $quantityChange >= 0) {
                    $updateQuantitySql = "UPDATE tbl_orderitem SET quantity = :new_quantity WHERE order_item_id = :order_item_id";
                    $stmt = $conn->prepare($updateQuantitySql);
                    $stmt->bindParam(':new_quantity', $newQuantity, PDO::PARAM_INT);
                    $stmt->bindParam(':order_item_id', $orderItemId, PDO::PARAM_INT);
                    $stmt->execute();

                    $updateStockSql = "UPDATE tbl_product SET product_stocks = :new_stocks WHERE product_id = :product_id";
                    $newStocks = $productStocks - $quantityChange;
                    $stmt = $conn->prepare($updateStockSql);
                    $stmt->bindParam(':new_stocks', $newStocks, PDO::PARAM_INT);
                    $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
                    $stmt->execute();

                    if ($newStocks >= 5) {
                        $status = "Available";
                    } elseif ($newStocks >= 1 && $newStocks <= 4) {
                        $status = "Low Stock";
                    } else {
                        $status = "Not Available";
                    }
                    $updateStatusSql = "UPDATE tbl_product SET product_status = :status WHERE product_id = :product_id";
                    $stmt = $conn->prepare($updateStatusSql);
                    $stmt->bindParam(':status', $status);
                    $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
                    $stmt->execute();

                    echo json_encode(array('status' => 'success', 'message' => 'Quantity updated successfully.'));
                } else {
                    echo json_encode(array('status' => 'error', 'message' => 'Not enough stock available for this product'));
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
    header('location: ../login');
}
