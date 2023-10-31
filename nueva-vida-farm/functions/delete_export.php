<?php
include '../database/connection.php';

$response = array();

if (isset($_POST['selectedMonth']) && isset($_POST['selectedYear'])) {
    $selectedMonth = $_POST['selectedMonth'];
    $selectedYear = $_POST['selectedYear'];

    $months = [
        "January" => 1,
        "February" => 2,
        "March" => 3,
        "April" => 4,
        "May" => 5,
        "June" => 6,
        "July" => 7,
        "August" => 8,
        "September" => 9,
        "October" => 10,
        "November" => 11,
        "December" => 12
    ];

    $monthNumber = $months[$selectedMonth];

    try {
        if ($selectedMonth === '' && $selectedYear === '') {
            $sql = "UPDATE tbl_reports SET is_Deleted = 0";
            $stmt = $conn->prepare($sql);
        } elseif ($selectedMonth === '' && $selectedYear !== '') {
            $sql = "UPDATE tbl_reports SET is_Deleted = 0 WHERE YEAR(update_date) = :year";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':year', $selectedYear);
        } elseif ($selectedMonth !== '' && $selectedYear === '') {
            $sql = "UPDATE tbl_reports SET is_Deleted = 0 WHERE MONTH(update_date) = :month";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':month', $monthNumber);
        } else {
            $sql = "UPDATE tbl_reports SET is_Deleted = 0 WHERE YEAR(update_date) = :year AND MONTH(update_date) = :month";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':month', $monthNumber);
            $stmt->bindParam(':year', $selectedYear);
        }

        $stmt->execute();
        $rowCount = $stmt->rowCount();

        if ($rowCount > 0) {
            $response['status'] = 200;
        } else {
            $response['status'] = 400;
        }
    } catch (PDOException $e) {
        $response['status'] = 400;
        $response['error'] = $e->getMessage();
    }
} else {
    $response['status'] = 400;
}

header('Content-type: application/json');
echo json_encode($response);
