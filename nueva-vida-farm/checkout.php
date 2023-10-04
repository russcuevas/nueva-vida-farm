<?php
include 'database/connection.php';
session_start();

if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];
} else {
    header('location: login.php');
}

// GET THE DETAILS OF THE CUSTOMER
$getCustomerInfoQuery = "SELECT * FROM tbl_customer WHERE customer_id = :customer_id";
$stmt = $conn->prepare($getCustomerInfoQuery);
$stmt->bindParam(':customer_id', $customer_id);
$stmt->execute();
$customerInfo = $stmt->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['selected_products']) && !empty($_POST['selected_products'])) {
    $selectedProductsInfo = [];
    $totalPrice = 0;

    foreach ($_POST['selected_products'] as $selectedProductId) {
        $query = "SELECT * FROM `tbl_product` WHERE product_id = :product_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':product_id', $selectedProductId);
        $stmt->execute();

        $productInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        $quantity = $_POST['product_quantity'][$selectedProductId];
        $subtotal = $productInfo['product_price'] * $quantity;
        $totalPrice += $subtotal;

        $selectedProductsInfo[] = [
            'product_id' => $productInfo['product_id'],
            'product_name' => $productInfo['product_name'],
            'product_size' => $productInfo['product_size'],
            'product_price' => $productInfo['product_price'],
            'quantity' => $quantity,
        ];
    }
} else {
    header('location: cart.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <!--===============================================================================================-->
    <link rel="stylesheet" href="assets/css/checkout.css">
    <!--===============================================================================================-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!--===============================================================================================-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <!--===============================================================================================-->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>

<body style="color: #404040">

    <div class="d-flex flex-row align-items-center p-2" id="navigation">
        <span class="material-symbols-outlined" id="backButton">
            arrow_back
            </span>
        <a href="cart.php" id="backText">Back to Cart</a>
    </div>

    <div class="d-flex flex-column gap-3 justify-content-center align-items-center mt-3 mt-md-0">
        <h2 style="border-bottom: 5px solid #222222;
        font-family: 'Rubik', sans-serif; font-weight: bold;">ORDER SUMMARY</h2>

        <form action="components/placed_orders.php" method="POST">
            <div class="d-flex flex-column justify-content-center align-items-center p-3" id="checkoutBox">
            <div class="d-flex flex-column justify-content-center gap-1 p-3"
                style="background-color: black; width: 100%;">
                <div class="d-flex justify-content-center">
                    <h3 style="color: white;">Cart Items</h3>
                </div>

            <?php foreach ($selectedProductsInfo as $product): ?>
                <div class="d-flex flex-row justify-content-between">
                    <input type="hidden" name="selected_products[]" value="<?php echo $product['product_id'] ?>">
                    <input type="hidden" name="product_quantity[<?php echo $product['product_id'] ?>]" value="<?php echo $product['quantity'] ?>">
                    <input type="hidden" name="product_size[<?php echo $product['product_id'] ?>]" value="<?php echo $product['product_size'] ?>">
                    <h4 style="color: #777777;"><?php echo $product['product_name']; ?></h4>
                    <h4 style="color: #049547;">₱<?php echo $product['product_price']; ?> x <?php echo $product['quantity']; ?></h4>
                </div>
            <?php endforeach;?>


            <div class="d-flex flex-row justify-content-between align-items-center px-3 py-1 bg-white">
                <h4 class="mt-1" style="color: #777777;">Total price</h4>
                <h4 class="mt-1" style="color: #049547;">₱<?php echo number_format($totalPrice, 2); ?></h4>
            </div>

            <div class="d-flex justify-content-start align-items-center mt-2" id="viewCartBox">
                <button><a href="cart.php">View Cart</a></button>
            </div>

            </div>

                <div class="d-flex justify-content-center align-items-center p-2 my-3" id="informationBox">
                    <h3 class="mt-1">My Information</h3>
                </div>

                <div class="d-flex justify-content-start gap-1" style="width: 100%;">
                    <span class="material-symbols-outlined">
                        person
                    </span>
                    <h4><?php echo $customerInfo['first_name']; ?> <?php echo $customerInfo['last_name']; ?></h4>
                </div>
                <div class="d-flex justify-content-start gap-1" style="width: 100%;">
                    <span class="material-symbols-outlined">
                        call
                    </span>
                    <h4><?php echo $customerInfo['phone']; ?></h4>
                </div>
                <div class="d-flex justify-content-start gap-1" style="width: 100%;">
                    <span class="material-symbols-outlined">
                        mail
                    </span>
                    <h4><?php echo $customerInfo['email']; ?></h4>
                </div>
                <div class="d-flex justify-content-start gap-1" style="width: 100%;">
                    <span class="material-symbols-outlined">
                        location_on
                    </span>
                    <h4><?php echo $customerInfo['address']; ?></h4>
                </div>

                <select name="payment_method" id="" class="mt-3 mb-3" style="font-weight: 600;">
                    <option value="CASH ON PICKUP">CASH ON PICKUP</option>
                </select>

                <button type="submit" name="placeorder" class="placeOrder">Place Order</button>
            </div>

        </form>

    </div>

    <script src="checkout.js"></script>
</body>
</html>
