<?php
include '../database/connection.php';

session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location: admin_login');
    exit();
}

if (isset($_GET['product_id']) && is_numeric($_GET['product_id']) && $_GET['product_id'] > 0) {
    $product_id = $_GET['product_id'];

    try {
        $conn->beginTransaction();

        $fetch_image_name = $conn->prepare("SELECT product_image FROM tbl_product WHERE product_id = ?");
        $fetch_image_name->execute([$product_id]);
        $new_image_name = $fetch_image_name->fetchColumn();

        $fetch_all_reference_numbers = $conn->prepare("SELECT DISTINCT reference_number FROM tbl_order WHERE product_id = ?");
        $fetch_all_reference_numbers->execute([$product_id]);
        $all_reference_numbers = $fetch_all_reference_numbers->fetchAll(PDO::FETCH_COLUMN);

        foreach ($all_reference_numbers as $reference_number) {
            $fetch_order_ids = $conn->prepare("SELECT DISTINCT o.order_id, os.status FROM tbl_order o
                LEFT JOIN tbl_orderstatus os ON o.order_id = os.order_id
                WHERE o.reference_number = ?");
            $fetch_order_ids->execute([$reference_number]);
            $order_ids = $fetch_order_ids->fetchAll(PDO::FETCH_ASSOC);

            foreach ($order_ids as $order) {
                $order_id = $order['order_id'];
                $status = $order['status'];

                if ($status === 'Completed') {
                    $disable_fk_checks_query = $conn->prepare("SET FOREIGN_KEY_CHECKS = 0");
                    $disable_fk_checks_query->execute();

                } elseif ($status === 'Pending' || $status === 'Ready to pick') {
                    $enable_fk_checks_query = $conn->prepare("SET FOREIGN_KEY_CHECKS = 1");
                    $enable_fk_checks_query->execute();
                }

                if ($status === 'Pending' || $status === 'Ready to pick') {
                    $fetch_total_quantity = $conn->prepare("SELECT total_quantity FROM tbl_order WHERE order_id = ?");
                    $fetch_total_quantity->execute([$order_id]);
                    $total_quantity = $fetch_total_quantity->fetchColumn();

                    $fetch_product_id = $conn->prepare("SELECT product_id FROM tbl_order WHERE order_id = ?");
                    $fetch_product_id->execute([$order_id]);
                    $product_id_deleted = $fetch_product_id->fetchColumn();

                    $delete_order_status = $conn->prepare("DELETE FROM tbl_orderstatus WHERE order_id = ?");
                    $delete_order_status->execute([$order_id]);

                    $update_product_stocks = $conn->prepare("UPDATE tbl_product SET product_stocks = product_stocks + ? WHERE product_id = ?");
                    $update_product_stocks->execute([$total_quantity, $product_id_deleted]);

                    $fetch_updated_product_stocks = $conn->prepare("SELECT product_stocks FROM tbl_product WHERE product_id = ?");
                    $fetch_updated_product_stocks->execute([$product_id_deleted]);
                    $updated_product_stocks = $fetch_updated_product_stocks->fetchColumn();

                    if ($updated_product_stocks >= 5) {
                        $product_status = "Available";
                    } elseif ($updated_product_stocks >= 1 && $updated_product_stocks <= 4) {
                        $product_status = "Low Stock";
                    } else {
                        $product_status = "Not Available";
                    }

                    $update_product_status = $conn->prepare("UPDATE tbl_product SET product_status = ? WHERE product_id = ?");
                    $update_product_status->execute([$product_status, $product_id_deleted]);

                    $delete_order = $conn->prepare("DELETE FROM tbl_order WHERE order_id = ?");
                    $delete_order->execute([$order_id]);
                }

            }
        }

        $delete_cart = $conn->prepare("DELETE FROM tbl_orderitem WHERE product_id = ?");
        $delete_cart->execute([$product_id]);

        $delete_product = $conn->prepare("DELETE FROM tbl_product WHERE product_id = ?");

        if ($delete_product->execute([$product_id])) {
            $product_folder = '../assets/images/products/' . $new_image_name;

            if (file_exists($product_folder)) {
                if (unlink($product_folder)) {
                    echo 'success';
                } else {
                    echo 'failed to delete image';
                }
            } else {
                echo 'image file not found';
            }
        } else {
            echo 'failure';
        }

        $conn->commit();

    } catch (PDOException $e) {
        $conn->rollback();
        echo 'error: ' . $e->getMessage();
    }
} else {
    header('location: products');
}
