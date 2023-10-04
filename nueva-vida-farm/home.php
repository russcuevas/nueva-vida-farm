<?php
include 'database/connection.php';

session_start();
$customer_id = $_SESSION['customer_id'];
if (!isset($customer_id)) {
    header('location: login.php');
}

// DISPLAY CARTS COUNTS
$getCartCount = "SELECT COUNT(*) AS cart_count FROM `tbl_orderitem` WHERE `customer_id` = $customer_id";
$stmtCartCount = $conn->query($getCartCount);
$cartCount = $stmtCartCount->fetch(PDO::FETCH_ASSOC);

// DISPLAY THE PRODUCTS
$get = "SELECT * FROM `tbl_product` WHERE product_status IN ('Available', 'Low Stock') LIMIT 6";
$stmt = $conn->query($get);
$product = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales And Inventory Management</title>
    <link rel="stylesheet" href="assets/css/home.css">
    <!--===============================================================================================-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!--===============================================================================================-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!--===============================================================================================-->
    <link rel="shortcut icon" href="assets/favicon/egg.png" type="image/x-icon">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="assets/js/sweetalert2/dist/sweetalert2.css" />
</head>

<body>
    <div id="spinnerContainer">
        <div class="spinner"></div>
    </div>

    <nav class="navbar px-3 py-3 px-md-5">
        <h2>Nueva Vida Farm</h2>

        <div class="d-flex align-items-center justify-content-center flex-row gap-3">
            <i class="bi bi-bag" style="position: relative; cursor: pointer;" onclick="window.location.href = 'cart.php';">
                <span style="position: absolute; right: -10px; top: -5px; font-size: 12px; font-style: normal;">
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

    <div class="d-flex justify-content-center flex-row px-0 px-md-5" id="homeContent">
        <div class="col" style="overflow: hidden;">
            <img src="assets/images/homepage/eggtray.jpg" alt="">
        </div>

        <div class="col d-none d-md-flex justify-content-center align-items-center flex-column" style="background-color: black;">
            <h1>Nueva Vida Farm</h1>
            <h2>Family Friendly Poultry Farm</h2>
        </div>
    </div>

    <div class="d-flex flex-column">
        <div class="d-flex justify-content-center mt-4">
            <h1 style="color: white;">Products</h1>
        </div>

        <div class="py-3 px-3 px-md-5">
            <?php if (empty($product)): ?>
                <div style="text-align: center; color: red; font-size: 40px;">No Products Available</div>
            <?php else: ?>
                <div class="swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($product as $products): ?>
                            <div class="swiper-slide">
                                <img src="assets/images/products/<?php echo $products['product_image']; ?>" alt="">
                                <div class="swiper-content">
                                    <h2><?php echo $products['product_name']; ?> <span>(<?php echo $products['product_size'] ?>)</span></h2>
                                    <h4 style="color: red;">₱<?php echo $products['product_price']; ?></h4>
                                </div>
                            </div>
                        <?php endforeach?>
                    </div>
                </div>
            <?php endif?>
        </div>


        <div class="d-flex justify-content-center align-items-center py-2" id="viewAllContainer">
            <button><a href="./shop.php" style="color: black; text-decoration: none;">View All</a></button>
        </div>

        <div class="d-flex justify-content-center flex-row gap-3 py-3 mb-2" id="sliderButtons">
            <i class="bi bi-caret-left-fill"></i>
            <i class="bi bi-caret-right-fill"></i>
        </div>

        <div class="d-flex justify-content-center flex-column px-0 px-md-5" id="gridBox">
            <div class="d-flex flex-column flex-lg-row">
                <div class="col d-flex justify-content-center flex-column align-items-center bg-black p-4 p-lg-0">
                    <h1>Come on down to the farm!</h1>
                    <h3 style="color: white;">Lorem ipsum dolor, sit amet consectetur</h3>
                    <h5 style="color: white;" class="mt-5">Email : nuevavidafarmsinc@gmail.com</h5>
                    <h5 style="color: white;">Phone Number : 123-456-789</h5>
                </div>
                <div class="col d-flex justify-content-center align-items-center">
                    <img src="assets/images/homepage/eggtray.jpg" alt="">
                </div>
            </div>
            <div class="mapouter mb-2 mb-md-5" id="googleMap">
                <div class="gmap_canvas"><iframe width="100%" height="100%" id="gmap_canvas" src="https://maps.google.com/maps?q=Galamay Amo, San Jose, Batangas, the Egg Basket of the Philippines.&t=&z=10&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe><a href="https://2yu.co">2yu</a><br>
                    <style>
                        .mapouter {
                            position: relative;
                            text-align: right;
                            height: 100%;
                            width: 100%;
                        }
                    </style><a href="https://embedgooglemap.2yu.co/">html embed google map</a>
                    <style>
                        .gmap_canvas {
                            overflow: hidden;
                            background: none !important;
                            height: 100%;
                            width: 100%;
                        }
                    </style>
                </div>
            </div>
        </div>
    </div>


    <!--===============================================================================================-->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <!--===============================================================================================-->
    <script src="assets/js/home.js"></script>
    <!--===============================================================================================-->
    <script src="assets/js/sweetalert2/dist/sweetalert2.min.js"></script>

    <script>
        setTimeout(() => {
            document.querySelector('#spinnerContainer').style.display = 'none';
        }, 1200);

        function onWindowLoad() {
            fadeOut();
            const login_success = '<?php echo isset($_SESSION["login_success"]) ? $_SESSION["login_success"] : "" ?>';
            if (login_success) {
                Swal.fire({
                    icon: "success",
                    text: "Login successfully!",
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 2000,
                });
                <?php $_SESSION["login_success"] = false;?>
            }
        }

        window.onload = onWindowLoad;
    </script>

    <script>
        const mySwiper = new Swiper('.swiper', {
            direction: 'horizontal',
            slidesPerView: 1,
            centeredSlides: false,
            spaceBetween: 10,
            navigation: {
                nextEl: '.bi-caret-right-fill',
                prevEl: '.bi-caret-left-fill',
            },
            breakpoints: {
                568: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                },
                1400: {
                    slidesPerView: 5,
                }
            },
        });
    </script>
    <!--===============================================================================================-->
</body>

</html>