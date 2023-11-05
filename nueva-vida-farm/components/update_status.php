<?php
include '../database/connection.php';

session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location: admin_login');
    exit();
}

date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_status = $_POST['order_status'];
    $order_id = $_POST['order_id'];

    $sql = "SELECT reference_number, order_date, customer_id, payment_method, total_amount, total_products, total_quantity FROM tbl_order WHERE order_id = :order_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':order_id', $order_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $reference_number = $result['reference_number'];
        $order_date = $result['order_date'];
        $customer_id = $result['customer_id'];
        $payment_method = $result['payment_method'];
        $total_amount = $result['total_amount'];
        $total_products = $result['total_products'];
        $total_quantity = $result['total_quantity'];

        $newUpdateDate = null;

        if ($order_status === 'Ready to pick') {
            $user_entered_datetime = $_POST['update_date'];
            $timestamp = strtotime($user_entered_datetime);
            $newUpdateDate = date('Y-m-d H:i:s', $timestamp);
        } elseif ($order_status === 'Pending') {
            $newUpdateDate = date('Y-m-d H:i:s', strtotime($order_date));
        } elseif ($order_status === 'Completed') {
            $newUpdateDate = date('Y-m-d H:i:s', strtotime('now'));
        }

        $conn->beginTransaction();

        $update_sql = "UPDATE tbl_orderstatus SET status = :status, update_date = :update_date WHERE order_id IN (
            SELECT order_id FROM tbl_order WHERE reference_number = :reference_number
        )";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bindParam(':status', $order_status);
        $update_stmt->bindParam(':reference_number', $reference_number);
        $update_stmt->bindParam(':update_date', $newUpdateDate);

        if ($update_stmt->execute()) {
            if ($order_status === 'Completed') {
                $get_latest_order_id_sql = "SELECT MAX(order_id) AS max_order_id FROM tbl_order WHERE reference_number = :reference_number";
                $get_latest_order_id_stmt = $conn->prepare($get_latest_order_id_sql);
                $get_latest_order_id_stmt->bindParam(':reference_number', $reference_number);
                $get_latest_order_id_stmt->execute();
                $latest_order_id_row = $get_latest_order_id_stmt->fetch();
                $latest_order_id = $latest_order_id_row['max_order_id'];

                $get_total_products_sql = "SELECT total_products FROM tbl_order WHERE order_id = :order_id";
                $get_total_products_stmt = $conn->prepare($get_total_products_sql);
                $get_total_products_stmt->bindParam(':order_id', $latest_order_id);
                $get_total_products_stmt->execute();
                $total_products_row = $get_total_products_stmt->fetch();
                $total_products = $total_products_row['total_products'];

                $insert_report_sql = "INSERT INTO tbl_reports (order_id, reference_number, payment_method, customer_id, order_date, total_amount, total_products, total_quantity, status, update_date) 
                VALUES (:order_id, :reference_number, :payment_method, :customer_id, :order_date, :total_amount, :total_products, :total_quantity, :status, :update_date)";
                $insert_report_stmt = $conn->prepare($insert_report_sql);
                $insert_report_stmt->bindParam(':order_id', $latest_order_id);
                $insert_report_stmt->bindParam(':reference_number', $reference_number);
                $insert_report_stmt->bindParam(':payment_method', $payment_method);
                $insert_report_stmt->bindParam(':customer_id', $customer_id);
                $insert_report_stmt->bindParam(':order_date', $order_date);
                $insert_report_stmt->bindParam(':total_amount', $total_amount);
                $insert_report_stmt->bindParam(':total_products', $total_products);
                $insert_report_stmt->bindParam(':total_quantity', $total_quantity);
                $insert_report_stmt->bindParam(':status', $order_status);
                $insert_report_stmt->bindParam(':update_date', $newUpdateDate);

                if ($insert_report_stmt->execute()) {
                    $insert_user_report_sql = "INSERT INTO tbl_user_reports (order_id, reference_number, payment_method, customer_id, order_date, total_amount, total_products, is_Seen, status, update_date) 
                    VALUES (:order_id, :reference_number, :payment_method, :customer_id, :order_date, :total_amount, :total_products, 0, :status, :update_date)";
                    $insert_user_report_stmt = $conn->prepare($insert_user_report_sql);
                    $insert_user_report_stmt->bindParam(':order_id', $latest_order_id);
                    $insert_user_report_stmt->bindParam(':reference_number', $reference_number);
                    $insert_user_report_stmt->bindParam(':payment_method', $payment_method);
                    $insert_user_report_stmt->bindParam(':customer_id', $customer_id);
                    $insert_user_report_stmt->bindParam(':order_date', $order_date);
                    $insert_user_report_stmt->bindParam(':total_amount', $total_amount);
                    $insert_user_report_stmt->bindParam(':total_products', $total_products);
                    $insert_user_report_stmt->bindParam(':status', $order_status);
                    $insert_user_report_stmt->bindParam(':update_date', $newUpdateDate);

                    if ($insert_user_report_stmt->execute()) {
                        $delete_orderstatus_sql = "DELETE FROM tbl_orderstatus WHERE order_id IN (
                            SELECT order_id FROM tbl_order WHERE reference_number = :reference_number
                        )";
                        $delete_orderstatus_stmt = $conn->prepare($delete_orderstatus_sql);
                        $delete_orderstatus_stmt->bindParam(':reference_number', $reference_number);
                        $delete_orderstatus_stmt->execute();

                        $delete_order_sql = "DELETE FROM tbl_order WHERE reference_number = :reference_number";
                        $delete_order_stmt = $conn->prepare($delete_order_sql);
                        $delete_order_stmt->bindParam(':reference_number', $reference_number);
                        $delete_order_stmt->execute();

                        $conn->commit();

                        $_SESSION['update_status'] = 'Status updated successfully';
                        header('Location: ' . $_SERVER['HTTP_REFERER']);
                        exit();
                    } else {
                        $conn->rollback();
                        echo "<script>window.alert ('Error inserting into user reports');</script>";
                        echo "<script>window.location.href = ('../admin/orders');</script>";
                    }
                } else {
                    $conn->rollback();
                    echo "<script>window.alert ('Error inserting into reports');</script>";
                    echo "<script>window.location.href = ('../admin/orders');</script>";
                }
            } else {
                $conn->commit();
                $_SESSION['update_status'] = 'Status updated successfully';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }
        } else {
            $conn->rollback();
            echo "<script>window.alert ('Error updating status');</script>";
            echo "<script>window.location.href = ('../admin/orders');</script>";
        }
    } else {
        echo "<script>window.alert ('Order not found');</script>";
        echo "<script>window.location.href = ('../admin/orders');</script>";
    }
} else {
    header('Location: ../admin/orders');
    exit();
}
