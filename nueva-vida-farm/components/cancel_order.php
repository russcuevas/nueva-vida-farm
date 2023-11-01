
<?php
include '../database/connection.php';
session_start();

if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
    $response = array();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['order_id'])) {
            $order_id = $_GET['order_id'];

            try {
                $conn->beginTransaction();

                $getRemovedOrderQuery = "SELECT reference_number FROM tbl_order WHERE order_id = :order_id";
                $stmt = $conn->prepare($getRemovedOrderQuery);
                $stmt->bindParam(':order_id', $order_id);
                $stmt->execute();
                $removedOrder = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$removedOrder) {
                    $response['message'] = 'Order not found';
                    $response['status'] = 400;
                } else {
                    $reference_number = $removedOrder['reference_number'];

                    $getOrdersQuery = "SELECT * FROM tbl_order WHERE reference_number = :reference_number";
                    $stmt = $conn->prepare($getOrdersQuery);
                    $stmt->bindParam(':reference_number', $reference_number);
                    $stmt->execute();
                    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($orders as $order) {
                        $order_id = $order['order_id'];
                        $product_id = $order['product_id'];
                        $total_quantity = $order['total_quantity'];

                        $deleteOrderStatusQuery = "DELETE FROM tbl_orderstatus WHERE order_id = :order_id";
                        $stmt = $conn->prepare($deleteOrderStatusQuery);
                        $stmt->bindParam(':order_id', $order_id);
                        $stmt->execute();

                        $deleteOrderQuery = "DELETE FROM tbl_order WHERE order_id = :order_id";
                        $stmt = $conn->prepare($deleteOrderQuery);
                        $stmt->bindParam(':order_id', $order_id);
                        $stmt->execute();

                        $updateProductStockQuery = "UPDATE tbl_product SET product_stocks = product_stocks + :total_quantity WHERE product_id = :product_id";
                        $stmt = $conn->prepare($updateProductStockQuery);
                        $stmt->bindParam(':total_quantity', $total_quantity);
                        $stmt->bindParam(':product_id', $product_id);
                        $stmt->execute();

                        $getProductStockQuery = "SELECT product_stocks FROM tbl_product WHERE product_id = :product_id";
                        $stmt = $conn->prepare($getProductStockQuery);
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

                        $updateProductStatusQuery = "UPDATE tbl_product SET product_status = :status WHERE product_id = :product_id";
                        $stmt = $conn->prepare($updateProductStatusQuery);
                        $stmt->bindParam(':status', $status);
                        $stmt->bindParam(':product_id', $product_id);
                        $stmt->execute();

                        $response['message'] = 'Order canceled successfully';
                        $response['status'] = 200;
                    }

                    $conn->commit();
                }
            } catch (Exception $e) {
                $conn->rollBack();
                $response['message'] = 'Error canceling the order';
                $response['status'] = 400;
                echo "Error: " . $e->getMessage();
            }
        } else {
            $response['message'] = 'Invalid order ID';
            $response['status'] = 400;
            header('location: ../admin/admin_login');
        }
    } else {
        $response['message'] = 'Invalid request method';
        $response['status'] = 400;
        header('location: ../admin/admin_login');
    }
} else {
    header('location: ../login');
    exit;
}

header('Content-type: application/json');
echo json_encode($response);
?>


