<?php
include 'database/connection.php';
session_start();

if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];
} else {
    header('location: login.php');
}

// DISPLAY PRODUCTS
$get = "SELECT * FROM `tbl_product`";
$stmt = $conn->query($get);
$product = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    <link rel="stylesheet" href="assets/css/shop.css">
    <!--===============================================================================================-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!--===============================================================================================-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!--===============================================================================================-->
    <link rel="shortcut icon" href="assets/favicon/egg.png" type="image/x-icon">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!--===============================================================================================-->
    <link rel="stylesheet" href="assets/js/sweetalert2/dist/sweetalert2.css" />
    <link rel="stylesheet" href="assets/css/HoldOn.min.css">
</head>

<body class="animate__animated animate__fadeIn">

    <nav class="navbar px-3 py-3 px-md-5">
        <h2>Nueva Vida Farm</h2>

        <div class="d-flex align-items-center justify-content-center flex-row gap-3">
            <i class="bi bi-bag" style="position: relative; cursor: pointer;" onclick="window.location.href = 'cart.php';">
                <span id="cart-count" style="position: absolute; right: -10px; top: -5px; font-size: 12px; font-style: normal; color: red;">
                    <!-- Cart count will be displayed here -->
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

    <div class="container p-0">
        <h1 style="text-align: center; color: white;">Order Here!</h1>

        <div class="mb-3">
            <label for="sizeFilter" class="form-label" style="color: white;">Filter by Size:</label>
            <select id="sizeFilter" class="form-select">
                <option value="ALL">All Sizes</option>
                <option value="SMALL">Small</option>
                <option value="MEDIUM">Medium</option>
                <option value="LARGE">Large</option>
                <option value="DOUBLE YOLK">Double Yolk</option>
            </select>
        </div>

        <div id="noProductsMessage" style="display: none; color: red; text-align: center; font-weight: 500;">No products available at the selected size.</div>


        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 m-2 m-md-0 my-md-4" id="gridCards">
<?php
$productsAvailable = false;
foreach ($product as $products):
    if ($products['product_status'] !== 'Not Available'):
        $maxQuantity = $products['product_stocks'];
        $productSize = $products['product_size'];
        ?>
																																																																<div class="col" data-product-size="<?php echo $productSize; ?>">
																																																																    <form class="add-to-cart-form" action="functions/add_to_cart.php" method="POST">
																																																																        <div class="d-flex flex-column">
																																																																            <input type="hidden" name="product_id" value="<?php echo $products['product_id']; ?>">
																																																																            <img src="assets/images/products/<?php echo $products['product_image']; ?>" alt="">
																																																																            <div class="box d-column d-flex justify-content-center align-items-center py-2">
																																																																                <h3 class="mt-2"><?php echo $products['product_name'] ?></h3>
																																																																            </div>
																																																																            <div class="box d-flex flex-column justify-content-around px-3">
																																																																                <div class="d-flex flex-row gap-2">
																																																																                    <h4>PRICE : </h4>
																																																																                    <h3 style="color: red;">₱<?php echo $products['product_price'] ?></h3>
																																																																                </div>
																																																																                <div class="d-flex flex-row gap-2">
																																																																                    <h4>SIZE : </h4>
																																																																                    <input type="text" style="width: 133px; margin-bottom: 25px; font-weight: 900; cursor: default; background-color: black; border:none; font-size: 20px; color: white;" name="product_size[]" value="<?php echo $products['product_size']; ?>" readonly>
																																																																                </div>
																																																																                <div class="d-flex flex-row gap-2">
																																																																                    <h4>Qty</h4>
																																																																                    <input type="number" style="cursor: pointer" name="quantity" class="quantity" value="1" min="1" max="<?php echo min($maxQuantity, 999); ?>" onkeydown="preventTyping(event)">
																																																																                </div>
																																																																            </div>
																																																																            <a href="buy_now.php?product_id=<?php echo $products['product_id']; ?>&quantity=1" class="buy_now_link" style="text-decoration: none; color: black;">BUY NOW</a>
																																																																            <button type="submit">ADD TO CART</button>
																																																																        </div>
																																																																    </form>
																																																																</div>
																																																																<?php
    endif;
endforeach;
?>
<?php if (!$productsAvailable): ?>
<!-- NO PRODUCT AVAIL -->
<?php endif;?>

        </div>
    </div>
    </div>
    </div>
    <!--===============================================================================================-->
    <!-- Include HoldOn.js from a CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="assets/js/HoldOn.min.js"></script>
    <script src="ajax/add_to_cart.js"></script>
    <script src="ajax/get_count_cart.js"></script>
    <script src="assets/js/home.js"></script>
    <!--===============================================================================================-->
    <script src="assets/js/sweetalert2/dist/sweetalert2.min.js"></script>
    <script>
    // PREVENTING AUTO TYPE IN QUANTITY
    function preventTyping(event) {
        event.preventDefault();
    }
    </script>


    <!-- FOR BUY NOW SINGLE PAGE -->
    <script>
        const quantityInputs = document.querySelectorAll('.quantity');
        quantityInputs.forEach(input => {
            input.addEventListener('input', function() {
                const quantity = this.value;
                const parentForm = this.closest('form');
                const productId = parentForm.querySelector('[name="product_id"]').value;
                const buyNowLink = parentForm.querySelector('.buy_now_link');
                const buyNowUrl = `buy_now.php?product_id=${productId}&quantity=${quantity}`;
                buyNowLink.href = buyNowUrl;
            });
        });
    </script>

    <!-- FOR FILTERING BY SIZE THE PRODUCTS THAT YOU WANT -->
    <script>
        const sizeFilter = document.getElementById('sizeFilter');
        const products = document.querySelectorAll('[data-product-size]');
        const noProductsMessage = document.getElementById('noProductsMessage');

        sizeFilter.addEventListener('change', () => {
            const selectedSize = sizeFilter.value;
            let productsAvailable = false;

            products.forEach((product) => {
                const productSize = product.getAttribute('data-product-size');
                if (selectedSize === 'ALL' || selectedSize === productSize) {
                    product.style.display = 'block';
                    productsAvailable = true;
                } else {
                    product.style.display = 'none';
                }
            });

            if (!productsAvailable) {
                noProductsMessage.style.display = 'block';
            } else {
                noProductsMessage.style.display = 'none';
            }
        });
    </script>

</body>
</html>