<?php
include '../database/connection.php';

if (isset($_GET['year'])) {
    $year = $_GET['year'];

    $query = "SELECT DATE_FORMAT(update_date, '%M') AS month, 
              SUM(total_amount) AS total_sales 
              FROM tbl_reports 
              WHERE YEAR(update_date) = :year 
              GROUP BY DATE_FORMAT(update_date, '%Y-%m')";

    $stmt = $conn->prepare($query);
    $stmt->execute(['year' => $year]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Year parameter not provided']);
    header('location: ../login.php');
}
