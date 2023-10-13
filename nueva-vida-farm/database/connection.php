<?php
$dbhost = 'mysql:host=localhost;dbname=ordersystem';
$dbname = 'root';
$dbpass = '';

$conn = new PDO($dbhost, $dbname, $dbpass);

// if (!$conn) {
//     echo 'Not connected to database';
// } else {
//     echo 'Successfully connected to database';
// }
