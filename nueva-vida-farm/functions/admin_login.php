<?php

include '../database/connection.php';

$response = array();

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $response['status'] = 'warning';
        $response['message'] = 'Please fill up all fields first';
    } else {
        // CHECK IF THE LOGIN REQUEST IS ADMIN
        $stmt = $conn->prepare("SELECT * FROM `tbl_admin` WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && sha1($password) === $admin['password']) {
            session_start();
            $_SESSION['admin_id'] = $admin['staff_id'];
            $_SESSION["login_success"] = true;
            $response['status'] = 'success';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Invalid credentials';
        }
    }
}

header("Content-type: application/json");
echo json_encode($response);
