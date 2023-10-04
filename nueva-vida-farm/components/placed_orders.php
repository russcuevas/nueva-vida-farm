<?php
include '../database/connection.php';

session_start();

if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];
} else {
    header('location: login.php');
    exit;
}

if (isset($_POST['placeorder'])) {
    date_default_timezone_set('Asia/Manila');
    $payment_method = $_POST['payment_method'];
    $reference_number = generateReferenceNumber();

    $totalPrice = 0;
    $orderedProductNames = [];

    foreach ($_POST['selected_products'] as $product_id) {
        $order_date = date('Y-m-d H:i:s');
        $totalAmount = 0;
        $totalQuantity = 0;

        $quantity = $_POST['product_quantity'][$product_id];
        $product_price = getProductPrice($product_id, $conn);
        $total_amount = $product_price * $quantity;

        $totalPrice += $total_amount;

        $product_name = getProductName($product_id, $conn);
        $product_size = $_POST['product_size'][$product_id];
        $orderedProductInfo = "$product_name $product_size ($quantity)";

        $orderedProductNames[] = $orderedProductInfo;

        $orderedProductsString = implode("<br>", $orderedProductNames);

        $insertOrderQuery = "INSERT INTO tbl_order (reference_number, payment_method, customer_id, order_date, total_amount, product_id, total_quantity, total_products)
        VALUES (:reference_number, :payment_method, :customer_id, :order_date, :total_amount, :product_id, :total_quantity, :total_products)";
        $stmt = $conn->prepare($insertOrderQuery);
        $stmt->bindParam(':reference_number', $reference_number);
        $stmt->bindParam(':payment_method', $payment_method);
        $stmt->bindParam(':customer_id', $customer_id);
        $stmt->bindParam(':order_date', $order_date);
        $stmt->bindParam(':total_amount', $total_amount);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':total_quantity', $quantity);
        $stmt->bindParam(':total_products', $orderedProductsString);
        $stmt->execute();

        $order_id = $conn->lastInsertId();

        $initialStatus = "Pending";
        $insertStatusQuery = "INSERT INTO tbl_orderstatus (status, order_id, update_date)
        VALUES (:status, :order_id, :update_date)";
        $stmt = $conn->prepare($insertStatusQuery);
        $stmt->bindParam(':status', $initialStatus);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->bindParam(':update_date', $order_date);
        $stmt->execute();

        $totalAmount += $total_amount;
        $totalQuantity += $quantity;

        $deleteOrderItemsQuery = "DELETE FROM tbl_orderitem WHERE customer_id = :customer_id AND product_id = :product_id";
        $stmt = $conn->prepare($deleteOrderItemsQuery);
        $stmt->bindParam(':customer_id', $customer_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
    }

    $updateTotalAmountQuery = "UPDATE tbl_order SET total_amount = :total_price WHERE reference_number = :reference_number";
    $stmt = $conn->prepare($updateTotalAmountQuery);
    $stmt->bindParam(':total_price', $totalPrice);
    $stmt->bindParam(':reference_number', $reference_number);
    $stmt->execute();

    header('location: ../order_status.php');
    exit;
} else {
    header('location: ../login.php');
}

// GETTING PRICE IN DIFFERENT PRODUCTS
function getProductPrice($product_id, $conn)
{
    $query = "SELECT product_price FROM tbl_product WHERE product_id = :product_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return $result['product_price'];
    } else {
        return 0;
    }
}

function getProductName($product_id, $conn)
{
    $query = "SELECT product_name FROM tbl_product WHERE product_id = :product_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['product_name'];
}

// GENERATE REF NO.
function generateReferenceNumber()
{
    $prefix = "ORDER";
    $timestamp = date("YmdHis");
    $randomNumber = mt_rand(1000, 9999);
    $referenceNumber = $prefix . $timestamp . $randomNumber;

    return $referenceNumber;
}
