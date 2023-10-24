<?php
include '../database/connection.php';

$response = array();

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $response['message'] = 'Please fill up all fields first';
        $response['status'] = 'warning';
    } else {
        // CHECK IF THE LOGIN REQUEST IS CUSTOMER
        $stmt = $conn->prepare("SELECT * FROM `tbl_customer` WHERE email = ?");
        $stmt->execute([$email]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($customer && password_verify($password, $customer['password'])) {
            session_start();
            $_SESSION['customer_id'] = $customer['customer_id'];
            $_SESSION['login_success'] = true;
            $response['status'] = 'success';
        } else {
            $response['message'] = 'Invalid credentials';
            $response['status'] = 'error';
        }
    }
} else {
    header('location: ../home');
}

header("Content-Type: application/json");
echo json_encode($response);
