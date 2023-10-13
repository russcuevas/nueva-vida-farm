<?php
include '../database/connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['admin_id'])) {
    header('Location: products');
    exit;
}

$existingProducts = array();

try {
    $sql = "SELECT product_name, product_size FROM tbl_product";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $productName = strtolower($row["product_name"]);
            $productSize = strtolower($row["product_size"]);

            if (!isset($existingProducts[$productName])) {
                $existingProducts[$productName] = array();
            }

            $existingProducts[$productName][] = $productSize;
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($existingProducts);
