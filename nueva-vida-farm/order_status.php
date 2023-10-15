<?php
include 'database/connection.php';
session_start();

if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];
} else {
    header('location: login');
}

// DISPLAY CARTS COUNTS
$getCartCount = "SELECT COUNT(*) AS cart_count FROM `tbl_orderitem` WHERE `customer_id` = $customer_id";
$stmtCartCount = $conn->query($getCartCount);
$cartCount = $stmtCartCount->fetch(PDO::FETCH_ASSOC);

// QUERY TO DISPLAY THE ORDERS
$query = "SELECT o.*, os.status, os.update_date, p.product_name, p.product_size
          FROM tbl_order o
          LEFT JOIN tbl_orderstatus os ON o.order_id = os.order_id
          LEFT JOIN tbl_product p ON o.product_id = p.product_id
          WHERE o.customer_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$customer_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$groupedOrders = [];
foreach ($orders as $order) {
    $reference_number = $order['reference_number'];
    if (!isset($groupedOrders[$reference_number])) {
        $groupedOrders[$reference_number] = [];
    }
    $groupedOrders[$reference_number][] = $order;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status</title>
    <link rel="shortcut icon" href="assets/favicon/egg.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/order_status.css">
    <!--===============================================================================================-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!--===============================================================================================-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
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
            <i class="bi bi-bag" style="position: relative; cursor: pointer;" onclick="window.location.href = 'cart';">
                <span style="position: absolute; right: -10px; top: -5px; font-size: 12px; font-style: normal; color: red;">
                    (<?= $cartCount['cart_count'] ?>)
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

    <div class="p-0 p-sm-3 p-md-5 overflow-hidden" id="cart">
        <div class="col">
            <div class="d-flex px-3 pt-3 pt-sm-0 px-sm-3" style="background-color: #404040; color: white;">
                <h1>Order Status</h1>
            </div>

            <div class="p-3 m-0" style="background-color: #404040; color: white;">

                <label for="statusFilter">Filter by Status:</label>
                <select id="statusFilter">
                    <option value="">Show All</option>
                    <option value="Pending">Pending</option>
                    <option value="Ready to pick">Ready to pick</option>
                </select>

                <div class="table-responsive">
                    <table id="example" class="table table-dark table-hover table-striped position-relative">
                        <thead class="table-success">
                            <tr>
                                <th>Reference Number</th>
                                <th>Total Amount</th>
                                <th>Product Ordered</th>
                                <th>Order Status</th>
                                <th>Order Date</th>
                                <th>Pick up Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($groupedOrders as $reference_number => $ordersGroup) : ?>
                                <?php if (reset($ordersGroup)['status'] !== 'Completed') : ?>
                                    <tr data-status="<?php echo reset($ordersGroup)['status']; ?>">
                                        <td style="color: #BB2525; font-weight: 900;"><?php echo $reference_number ?></td>
                                        <td>₱<?php echo reset($ordersGroup)['total_amount'] ?></td>
                                        <td>
                                            <?php
                                            $latestOrder = end($ordersGroup);
                                            echo $latestOrder['total_products'];
                                            ?>
                                        </td>
                                        <td style="font-weight: <?php echo reset($ordersGroup)['status'] === 'Pending' ? '900' : 'normal'; ?>; color:
                                        <?php echo reset($ordersGroup)['status'] === 'Pending' ? '#BB2525' : '#79AC78'; ?>">
                                            <?php if (reset($ordersGroup)['status'] === 'Ready to pick') : ?>
                                                <span style="font-weight: 900; color: inherit;"><?php echo reset($ordersGroup)['status'] . '📦 ' ?></span>
                                            <?php elseif (reset($ordersGroup)['status'] === 'Pending') : ?>
                                                <span style="font-weight: 900; color: inherit;"><?php echo reset($ordersGroup)['status'] . '⌛ ' ?></span>
                                            <?php else : ?>
                                                <?php echo reset($ordersGroup)['status'] ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php $orderDateTimestamp = strtotime(reset($ordersGroup)['order_date']); ?>
                                            <?php $formattedDate = date('F j, Y', $orderDateTimestamp); ?>
                                            <?php $formattedTime = date('h:i A', $orderDateTimestamp); ?>
                                            <?php echo $formattedDate . '<br>' . $formattedTime; ?>
                                        </td>
                                        <td>
                                            <?php if (reset($ordersGroup)['status'] !== 'Pending') : ?>
                                                <?php $updateDateTimestamp = strtotime(reset($ordersGroup)['update_date']); ?>
                                                <?php $formattedDate = date('F j, Y', $updateDateTimestamp); ?>
                                                <?php $formattedTime = date('h:i A', $updateDateTimestamp); ?>
                                                <?php echo $formattedDate . '<br>' . $formattedTime; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (reset($ordersGroup)['status'] !== 'Completed') : ?>
                                                <a class="btn btn-danger" href="components/remove_order.php?order_id=<?php echo reset($ordersGroup)['order_id'] ?>">Cancel Orders</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!--===============================================================================================-->
    <script src="assets/js/home.js"></script>
    <!--===============================================================================================-->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!--===============================================================================================-->
    <script src="assets/js/sweetalert2/dist/sweetalert2.min.js"></script>
    <script>
        setTimeout(() => {
            document.querySelector('#spinnerContainer').style.display = 'none';
        }, 1200);
    </script>
    <?php
    if (isset($_SESSION['remove_orders'])) {
        echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "Cancel order succesfully",
                        timer: 3000,
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                    });
                </script>';

        unset($_SESSION['remove_orders']);
    }
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusFilter = document.getElementById('statusFilter');
            const tableRows = document.querySelectorAll('#example tbody tr');

            statusFilter.addEventListener('change', function() {
                const selectedStatus = statusFilter.value;

                tableRows.forEach(function(row) {
                    const rowStatus = row.getAttribute('data-status');

                    if (selectedStatus === '' || rowStatus === selectedStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            var table = $('#example').DataTable({
                "dom": '<lf<t>ip<l>',
                "ordering": true,
                "info": false,
                "paging": true,
                "bLengthChange": false,
                "searching": false,
            });

            $('#statusFilter').on('change', function() {
                var selectedStatus = $(this).val();
                table.column(4).search(selectedStatus).draw();
            });
        });
    </script>


</body>

</html>