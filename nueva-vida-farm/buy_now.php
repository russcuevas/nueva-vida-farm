<?php
include 'database/connection.php';
session_start();

// CHECK IF THE USER IS AUTH
$customer_id = $_SESSION['customer_id'];
if (!isset($customer_id)) {
    header('location: login');
    exit;
}

// REDIRECT BACK IF THE USER WANTS TO EDIT THE URL
if (isset($_GET['product_id'])) {
    $product_id = filter_var($_GET['product_id'], FILTER_VALIDATE_INT);

    if ($product_id === false) {
        header('location: shop');
        exit;
    }

    $sql = "SELECT * FROM `tbl_product` WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header('location: shop');
        exit;
    }

    if (isset($_GET['quantity'])) {
        $quantity = filter_var($_GET['quantity'], FILTER_VALIDATE_INT);

        if ($quantity === false || $quantity < 1 || $quantity > $product['product_stocks']) {
            header('location: shop');
            exit;
        }
    } else {
        $quantity = 1;
    }
} else {
    header('location: shop');
    exit;
}

// USER CANT GO REDIRECT TO BUY_NOW
if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
    header('location: shop');
    exit;
}

$sql = "SELECT * FROM `tbl_customer` WHERE customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$customer_id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

$product = null;
$quantity = 1;

if (isset($_GET['product_id'])) {
    $product_id = filter_var($_GET['product_id'], FILTER_VALIDATE_INT);

    $sql = "SELECT * FROM `tbl_product` WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (isset($_GET['quantity'])) {
        $quantity = filter_var($_GET['quantity'], FILTER_VALIDATE_INT);
    }
}

date_default_timezone_set('Asia/Manila');

if (isset($_POST['submit'])) {
    $product_id = $_GET['product_id'];

    $sql = "SELECT product_name, product_size, product_price, product_stocks FROM `tbl_product` WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "<script>window.alert ('Product not found.');</script>";
        exit;
    }

    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $total_amount = $product['product_price'] * $quantity;

    if ($quantity > $product['product_stocks']) {
        echo "<script>window.alert ('Not enough stock available.');</script>";
        exit;
    }

    $new_stock_quantity = $product['product_stocks'] - $quantity;

    $updateStockSQL = "UPDATE `tbl_product` SET product_stocks = ? WHERE product_id = ?";
    $stmt = $conn->prepare($updateStockSQL);
    $stmt->execute([$new_stock_quantity, $product_id]);

    $status = "";
    if ($new_stock_quantity >= 5) {
        $status = "Available";
    } elseif ($new_stock_quantity >= 1 && $new_stock_quantity <= 4) {
        $status = "Low Stock";
    } else {
        $status = "Not Available";
    }

    $updateStatusSQL = "UPDATE `tbl_product` SET product_status = ? WHERE product_id = ?";
    $stmt = $conn->prepare($updateStatusSQL);
    $stmt->execute([$status, $product_id]);

    $reference_number = generateReferenceNumber();
    $payment_method = $_POST['payment_method'];
    $order_date = date('Y-m-d H:i:s A');
    $total_quantity = $quantity;

    $insertOrderSQL = "INSERT INTO tbl_order (reference_number, payment_method, customer_id, order_date, total_amount, product_id, total_quantity, total_products)
    VALUES (:reference_number, :payment_method, :customer_id, :order_date, :total_amount, :product_id, :total_quantity, :total_products)";

    $insertOrderStmt = $conn->prepare($insertOrderSQL);
    $insertOrderStmt->bindParam(':reference_number', $reference_number);
    $insertOrderStmt->bindParam(':payment_method', $payment_method);
    $insertOrderStmt->bindParam(':customer_id', $customer_id);
    $insertOrderStmt->bindParam(':order_date', $order_date);
    $insertOrderStmt->bindParam(':total_amount', $total_amount);
    $insertOrderStmt->bindParam(':product_id', $product_id);
    $insertOrderStmt->bindParam(':total_quantity', $total_quantity);
    $insertOrderStmt->bindParam(':total_products', $total_products);

    $total_products = $product['product_name'] . ' ' . $product['product_size'] . ' (' . $quantity . ')';

    if ($insertOrderStmt->execute()) {
        $order_id = $conn->lastInsertId();

        $initialStatus = "Pending";
        $insertStatusQuery = "INSERT INTO tbl_orderstatus (status, order_id, update_date) VALUES (:status, :order_id, :update_date)";
        $stmt = $conn->prepare($insertStatusQuery);
        $stmt->bindParam(':status', $initialStatus);
        $stmt->bindParam(':order_id', $order_id);
        $update_date = date('Y-m-d H:i:s A');
        $stmt->bindParam(':update_date', $update_date);


        if ($stmt->execute()) {
            header('location: order_status');
            exit;
        } else {
            echo "<script>window.alert ('Failed to insert order status.');</script>";
        }
    } else {
        echo "<script>window.alert ('Order insertion failed.');</script>";
    }
}

// GENERATE REF NO.
function generateReferenceNumber()
{
    $prefix = "ORDER";
    $timestamp = date("YmdHis");
    $randomNumber = mt_rand(1000, 9999);
    $referenceNumber = $prefix . $timestamp . $randomNumber;

    return $referenceNumber;
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
    <link rel="shortcut icon" href="assets/favicon/egg.png" type="image/x-icon">
    <!--===============================================================================================-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!--===============================================================================================-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="assets/css/HoldOn.min.css">
</head>

<body class="animate__animated animate__fadeInDown">

    <div class="d-flex flex-row align-items-center p-2" id="navigation">
        <span class="material-symbols-outlined" id="backButton">
            arrow_back
        </span>
        <a href="shop" id="backText">Back to shop</a>
    </div>

    <div class="d-flex flex-column gap-3 justify-content-center align-items-center mt-3 mt-md-0">
        <h2 style="border-bottom: 5px solid #222222;
        font-family: 'Rubik', sans-serif; font-weight: bold;">ORDER SUMMARY</h2>

        <form action="" method="POST">
            <div class="d-flex flex-column justify-content-center align-items-center p-3" id="checkoutBox">
                <div class="d-flex flex-column justify-content-center gap-1 p-3" style="background-color: black; width: 100%;">
                    <div class="d-flex justify-content-center">
                        <h3 style="color: white;">Selected Item</h3>
                    </div>

                    <?php if (isset($product)) : ?>
                        <input type="hidden" name="quantity" id="quantity" value="<?php echo $quantity; ?>" />
                        <input type="hidden" name="total_products" value="<?php echo $product['product_name']; ?> <?php echo $product['product_size']; ?> x <?php echo $quantity; ?>">
                        <div class="d-flex flex-row justify-content-between">
                            <h4 style="color: #777777;"><?php echo $product['product_name']; ?> (<?php echo $product['product_size'] ?>)</h4>
                            <h4 style="color: #049547;">₱<?php echo $product['product_price']; ?> x <?php echo $quantity ?></h4>
                        </div>

                        <div class="d-flex flex-row justify-content-between align-items-center px-3 py-1 bg-white">
                            <h4 class="mt-1" style="color: #777777;">Total Price :</h4>
                            <h4 class="mt-1">₱<?php echo number_format($product['product_price'] * $quantity, 2); ?></h4>
                        </div>
                    <?php else : ?>
                        <div>
                            <p>Product not found.</p>
                        </div>
                    <?php endif; ?>
                </div>


                <div class="d-flex justify-content-center align-items-center p-2 my-3" id="informationBox">
                    <h3 class="mt-1">My Information</h3>
                </div>

                <div class="d-flex justify-content-start gap-1" style="width: 100%;">
                    <span class="material-symbols-outlined">
                        person
                    </span>
                    <h4><?php echo $customer['first_name'] . ' ' . $customer['last_name']; ?></h4>
                </div>
                <div class="d-flex justify-content-start gap-1" style="width: 100%;">
                    <span class="material-symbols-outlined">
                        call
                    </span>
                    <h4><?php echo $customer['phone']; ?></h4>
                </div>
                <div class="d-flex justify-content-start gap-1" style="width: 100%;">
                    <span class="material-symbols-outlined">
                        mail
                    </span>
                    <h4><?php echo $customer['email']; ?></h4>
                </div>
                <div class="d-flex justify-content-start gap-1" style="width: 100%;">
                    <span class="material-symbols-outlined">
                        location_on
                    </span>
                    <h4><?php echo $customer['address']; ?></h4>
                </div>

                <select name="payment_method" id="" class="mt-3 mb-3">
                    <option value="CASH ON PICKUP" selected>CASH ON PICKUP</option>
                </select>


                <button type="submit" name="submit" class="placeOrder">Buy now</button>
            </div>
        </form>
    </div>

    <script src="assets/js/checkout.js"></script>
    <script src="assets/js/HoldOn.min.js"></script>
</body>

</html>