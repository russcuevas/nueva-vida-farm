<?php
include 'database/connection.php';
session_start();

if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];
} else {
    header('location: login');
}

// FETCH THE ORDER FROM THE LOGIN CUSTOMER
$sql = "SELECT oi.order_item_id, oi.customer_id, oi.product_id, oi.quantity,
               p.product_name, p.product_image, p.product_price, p.product_size,
               p.product_stocks
        FROM tbl_orderitem oi
        JOIN tbl_product p ON oi.product_id = p.product_id
        WHERE oi.customer_id = :customer_id";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':customer_id', $customer_id);
$stmt->execute();
$orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Cart</title>
    <link rel="stylesheet" href="assets/css/cart.css">
    <!--===============================================================================================-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!--===============================================================================================-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!--===============================================================================================-->
    <link rel="shortcut icon" href="assets/favicon/egg.png" type="image/x-icon">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="assets/js/sweetalert2/dist/sweetalert2.css" />
    <link rel="stylesheet" href="assets/css/HoldOn.min.css">
</head>

<body class="animate__animated animate__fadeIn">
    <nav class="navbar px-3 py-3 px-md-5">
        <h2>Nueva Vida Farm</h2>

        <div class="d-flex align-items-center justify-content-center flex-row gap-3">
            <i class="bi bi-bag" style="position: relative; cursor: pointer;" onclick="window.location.href = 'cart';">
                <span style="position: absolute; right: -10px; top: -5px; font-size: 12px; font-style: normal; color: red;">
                    (<?=$cartCount['cart_count']?>)
                </span>
            </i>
            <div class="d-flex flex-column position-relative">
                <span class="material-symbols-outlined" id="profileButton">
                    person
                </span>

                <div class="d-none flex-column position-absolute" id="profileDropdown">
                    <!-- <a href="#">Profile</a> -->
                    <a href="functions/logout.php">Logout</a>
                </div>
            </div>
        </div>

    </nav>

    <ul class="d-flex flex-wrap py-3 px-3 gap-4 py-md-0 px-md-5 mt-0 mt-md-3" id="lists">
        <li><a href="home">Home</a></li>
        <li><a href="contact">Contact</a></li>
        <li><a href="shop">Shop</a></li>
        <li><a href="cart">Cart</a></li>
        <li><a href="order_status">Order Status</a></li>
    </ul>


    <div class="p-0 p-sm-3 p-md-5 oveflow-hidden" id="cart">
        <div class="col">
            <div class="d-flex px-3 pt-3 pt-sm-0 px-sm-3" style="background-color: #404040; color: white;">
                <h1>Cart</h1>
            </div>

            <div class="p-3 m-0" style="background-color: #404040; color: white;">

                <div class="table-responsive">
                    <form id="checkout-form" action="checkout" method="POST">
                        <table id="example" class="table table-dark table-hover table-striped position-relative">
                            <thead class="table-success">
                                <tr>
                                    <th>Select all
                                    <input type="checkbox" id="select-all">
                                    </th>
                                    <th>Product Name</th>
                                    <th>Product Image</th>
                                    <th>Product Price</th>
                                    <th>Product Size</th>
                                    <th>Product Quantity</th>
                                    <th>Product Subtotal</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
$subtotals = [];
$hasItemsInCart = false;

foreach ($orderItems as $orderItem):
    $subtotal = $orderItem['quantity'] * $orderItem['product_price'];
    $subtotals[] = $subtotal;

    if ($orderItem['quantity'] > 0) {
        $hasItemsInCart = true;
    }
    ?>
																																																																								                                        <tr>
																																																																								                                            <td>
																																																																								                                                <input class="product-checkbox" type="checkbox" name="selected_products[]" value="<?php echo $orderItem['product_id']; ?>">
																																																																								                                            </td>
																																																																								                                            <td><?php echo $orderItem['product_name']; ?></td>
																																																																								                                            <td><img src="assets/images/products/<?php echo $orderItem['product_image']; ?>" alt=""></td>
																																																																								                                            <td>₱<?php echo $orderItem['product_price']; ?></td>
																																																																								                                            <td><?php echo $orderItem['product_size']; ?></td>
																																																																<td>
					    <input
					        type="number"
					        style="cursor: pointer"
					        class="quantity-input"
					        name="product_quantity[<?php echo $orderItem['product_id']; ?>]"
					        value="<?php echo $orderItem['quantity']; ?>"
					        min="1"
					        max="<?php echo $orderItem['quantity'] + $orderItem['product_stocks']; ?>"
					        required
					        data-order-item-id="<?php echo $orderItem['order_item_id']; ?>"
					        data-product-id="<?php echo $orderItem['product_id']; ?>"
					        onchange="updateDatabase(this);"
					        onkeydown="preventTyping(event, this);"
					    >
					</td>

																																																																								                                            <td>₱<span id="product-subtotal-<?php echo $orderItem['order_item_id']; ?>"><?php echo $subtotal; ?></span></td>
																																																																								                                            <td>
																																																																								                                                <a href="#" class="btn btn-danger btn-remove">Remove</a>
																				<input type="hidden" name="order_item_id" value="<?php echo $orderItem['order_item_id']; ?>">

																																																																								                                            </td>
																																																																								                                        </tr>
																																																																								                                        <?php endforeach;?>
                            </tbody>
                            <tfoot style="border-bottom: 0px solid transparent !important;" id="tableFooter">
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <?php if ($hasItemsInCart): ?>
                                    <td>
                                        <div class="d-flex justify-content-end mt-2">
                                            <h3>Total Price:</h3>
                                        </div>
                                    </td>
                                    <td style="font-size: 30px; font-weight: <?php echo array_sum($subtotals) > 0 ? '800' : 'normal'; ?>; color: <?php echo array_sum($subtotals) > 0 ? '#dc3545' : 'black'; ?>">
                                        ₱<?php echo number_format(array_sum($subtotals), 2); ?></td>
                                    <td>
                                        <button type="submit" class="btn btn-primary">Proceed to Checkout</button>
                                    </td>
                                    <?php endif;?>
                                    </td>
                                </tr>
                            </tfoot>

                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--===============================================================================================-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="assets/js/HoldOn.min.js"></script>
    <script src="ajax/update_quantity.js"></script>
    <script src="ajax/remove_cart.js"></script>
    <script src="assets/js/home.js"></script>
    <!--===============================================================================================-->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!--===============================================================================================-->
    <script src="assets/js/sweetalert2/dist/sweetalert2.min.js"></script>
    <!--===============================================================================================-->
    <script src="assets/js/cart_datatable.js"></script>
    <!--===============================================================================================-->
    <!-- SWEETALERT FUNCTION -->
<script>


// CANT REDIRECT IF EMPTY THE SELECTED PRODUCT
function validateForm() {
    const form = document.querySelector("#checkout-form");
    const productCheckboxes = document.querySelectorAll(".product-checkbox");

    const isAnyProductSelected = Array.from(productCheckboxes).some(checkbox => checkbox.checked);

    if (!isAnyProductSelected) {
        Swal.fire({
            icon: "warning",
            title: "Please select atleast one product to proceed",
            timer: 3000,
            toast: true,
            position: "top-end",
            showConfirmButton: false,
        });
        return false;
    }

    return true;
}

document.querySelector("#checkout-form").addEventListener("submit", function (event) {
    if (!validateForm()) {
        event.preventDefault();
    }
});



// SELECT ALL PRODUCT IN CART
document.addEventListener("DOMContentLoaded", function () {
    const selectAllCheckbox = document.getElementById("select-all");
    const productCheckboxes = document.querySelectorAll(".product-checkbox");

    selectAllCheckbox.addEventListener("change", function () {
    const isChecked = this.checked;

    productCheckboxes.forEach(function (checkbox) {
            checkbox.checked = isChecked;
        });
    });
});
</script>

</body>

</html>