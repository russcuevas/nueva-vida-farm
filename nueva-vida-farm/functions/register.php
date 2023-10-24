<?php
include '../database/connection.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    if ($email) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_customer WHERE email = ?");
        $stmt->execute([$email]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $response['status'] = 'error';
            $response['message'] = 'Email is already taken';
        } else {
            if (strlen($password) < 8 || strlen($password) > 12) {
                $response['status'] = 'error';
                $response['message'] = 'Password must be between 8-12 characters';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("INSERT INTO tbl_customer (first_name, last_name, email, password, address, phone) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$first_name, $last_name, $email, $hashed_password, $address, $phone]);
                $response['status'] = 'success';
                $response['message'] = 'Registered successfully';
            }
        }
    }
} else {
    header('location: ../home');
}

header("Content-type: application/json");
echo json_encode($response);
