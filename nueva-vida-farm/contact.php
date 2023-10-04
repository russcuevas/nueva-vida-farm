<?php
include 'database/connection.php';
session_start();

if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];
} else {
    header('location: login.php');
}

// DISPLAY CARTS COUNTS
$getCartCount = "SELECT COUNT(*) AS cart_count FROM `tbl_orderitem` WHERE `customer_id` = $customer_id";
$stmtCartCount = $conn->query($getCartCount);
$cartCount = $stmtCartCount->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="assets/css/shop.css">
    <!--===============================================================================================-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!--===============================================================================================-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!--===============================================================================================-->
    <link rel="shortcut icon" href="assets/favicon/egg.png" type="image/x-icon">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="assets/js/sweetalert2/dist/sweetalert2.css" />
    <link rel="stylesheet" href="assets/css/contact.css">
</head>

<body>

    <nav class="navbar px-3 py-3 px-md-5">
        <h2>Nueva Vida Farm</h2>

        <div class="d-flex align-items-center justify-content-center flex-row gap-3">
            <i class="bi bi-bag" style="position: relative; cursor: pointer;" onclick="window.location.href = 'cart.php';">
                <span style="position: absolute; right: -10px; top: -5px; font-size: 12px; font-style: normal; color: red;">
                    (<?=$cartCount['cart_count']?>)
                </span>
            </i>
            <div class="d-flex flex-column position-relative">
                <span class="material-symbols-outlined" id="profileButton">
                    person
                </span>

                <div class="d-none flex-column position-absolute" id="profileDropdown">
                    <a href="#">Profile</a>
                    <a href="components/logout.php">Logout</a>
                </div>
            </div>
        </div>

    </nav>

    <ul class="d-flex flex-wrap py-3 px-3 gap-4 py-md-0 px-md-5 mt-0 mt-md-3" id="lists">
        <li><a href="home.php">Home</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a href="shop.php">Shop</a></li>
        <li><a href="cart.php">Cart</a></li>
        <li><a href="order_status.php">Order Status</a></li>
    </ul>

    <div class="container-md d-flex justify-content-center align-items-center flex-column flex-lg-row p-0 p-md-5">
        <div class="col w-100">
            <div class="mapouter">
                <div class="gmap_canvas"><iframe style="filter: grayscale(100%) invert(92%) contrast(83%);" class="gmap_iframe" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=600&amp;height=400&amp;hl=en&amp;q= Galamay Amo, San Jose, Batangas&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe></div>
                <style>
                    .mapouter {
                        position: relative;
                        text-align: right;
                        width: 100%;
                        height: 100%;
                    }

                    .gmap_canvas {
                        overflow: hidden;
                        background: none !important;
                        width: 100%;
                        height: 100%;
                    }

                    .gmap_iframe {
                        height: 100%;
                    }
                </style>
            </div>
        </div>
        <div class="col w-100 bg-white">
            <div class="d-flex flex-column px-3 py-5 p-md-5 gap-4">
                <h1>CONTACT US</h1>
                <div class="d-flex flex-column flex-md-row justify-content-start justify-content-md-between">
                    <h5><span style="margin-right: 5px;"><i class="fa-solid fa-location-dot"></i></span>nuevavidafarmsinc@gmail.com</h5>
                    <h5><span style="margin-right: 5px;"><i class="fa-solid fa-phone"></i></span>123-456-789</h5>
                </div>
                <input type="text" name="" id="" placeholder="Name...">
                <input type="text" name="" id="" placeholder="Email...">
                <textarea name="" id="" cols="30" rows="10" placeholder="Message..."></textarea>
            </div>
        </div>
    </div>
    <!--===============================================================================================-->
    <script src="assets/js/home.js"></script>
    <!--===============================================================================================-->

</body>

</html>