<?php
include '../database/connection.php';
session_start();

$response = array();

if (!isset($_SESSION['customer_id'])) {
    header('location: ../login.php');
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// BUY ONLY 1 PRODUCT
if (isset($_POST['buy_now'])) {
    $product_id = $_POST['product_id'];
    header("Location: buy_now.php?product_id=$product_id");
    exit();
}

// SUBMIT ADD TO CART
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    if (empty($product_id) || empty($quantity)) {
        $response['status'] = 'warning';
        $response['message'] = 'Please add a quantity to proceed';
    } else {
        if (isset($_SESSION['customer_id'])) {
            $customer_id = $_SESSION['customer_id'];
            $sql = "SELECT * FROM `tbl_orderitem` WHERE customer_id = :customer_id AND product_id = :product_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':customer_id', $customer_id);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->execute();
            $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingItem) {
                $newQuantity = $existingItem['quantity'] + $quantity;
                $sql = "UPDATE `tbl_orderitem` SET quantity = :quantity WHERE customer_id = :customer_id AND product_id = :product_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':quantity', $newQuantity);
                $stmt->bindParam(':customer_id', $customer_id);
                $stmt->bindParam(':product_id', $product_id);
                if ($stmt->execute()) {
                    $sqlUpdateStock = "UPDATE `tbl_product` SET product_stocks = product_stocks - :quantity WHERE product_id = :product_id";
                    $stmtUpdateStock = $conn->prepare($sqlUpdateStock);
                    $stmtUpdateStock->bindParam(':quantity', $quantity);
                    $stmtUpdateStock->bindParam(':product_id', $product_id);
                    if ($stmtUpdateStock->execute()) {
                        updateProductStatus($product_id, $conn);
                        $response['status'] = 'success';
                        $response['message'] = 'Product quantity updated';
                    } else {
                        $response['status'] = 'error';
                        $response['message'] = 'Failed to update product quantity';
                    }
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Failed to update product quantity';
                }
            } else {
                $sql = "INSERT INTO `tbl_orderitem` (customer_id, product_id, quantity) VALUES (:customer_id, :product_id, :quantity)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':customer_id', $customer_id);
                $stmt->bindParam(':product_id', $product_id);
                $stmt->bindParam(':quantity', $quantity);

                if ($stmt->execute()) {
                    $sqlUpdateStock = "UPDATE `tbl_product` SET product_stocks = product_stocks - :quantity WHERE product_id = :product_id";
                    $stmtUpdateStock = $conn->prepare($sqlUpdateStock);
                    $stmtUpdateStock->bindParam(':quantity', $quantity);
                    $stmtUpdateStock->bindParam(':product_id', $product_id);
                    if ($stmtUpdateStock->execute()) {
                        updateProductStatus($product_id, $conn);
                        $response['status'] = 'success';
                        $response['message'] = 'Added to cart successfully';
                    } else {
                        $response['status'] = 'error';
                        $response['message'] = 'Failed to add to cart';
                    }
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Failed to add to cart';
                }
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'User is not logged in';
        }
    }
} else {
    header('location: ../home.php');
}

header('Content-Type: application/json');
echo json_encode($response);

function updateProductStatus($product_id, $conn)
{
    $sqlGetStock = "SELECT product_stocks FROM `tbl_product` WHERE product_id = :product_id";
    $stmtGetStock = $conn->prepare($sqlGetStock);
    $stmtGetStock->bindParam(':product_id', $product_id);
    $stmtGetStock->execute();
    $productStock = $stmtGetStock->fetchColumn();

    if ($productStock >= 5) {
        $status = "Available";
    } elseif ($productStock >= 1 && $productStock <= 4) {
        $status = "Low Stock";
    } else {
        $status = "Not Available";
    }

    $sqlUpdateStatus = "UPDATE `tbl_product` SET product_status = :status WHERE product_id = :product_id";
    $stmtUpdateStatus = $conn->prepare($sqlUpdateStatus);
    $stmtUpdateStatus->bindParam(':status', $status);
    $stmtUpdateStatus->bindParam(':product_id', $product_id);
    $stmtUpdateStatus->execute();
}
