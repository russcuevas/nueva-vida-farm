<?php
include '../database/connection.php';
session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location: admin_login');
}

// TOTAL PRODUCTS
$sql = "SELECT * FROM `tbl_product`";
$stmt = $conn->prepare($sql);
$stmt->execute();
$activeProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// DISPLAY THE RECENT ORDER
$sql = "SELECT
    o.order_id,
    o.reference_number,
    o.payment_method,
    CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
    o.total_amount,
    o.order_date,
    os.status AS order_status,
    os.update_date,
    od.product_id,
    p.product_name,
    p.product_size,
    SUM(od.total_quantity) AS total_quantity,
    o.total_products
FROM tbl_order o
LEFT JOIN tbl_orderstatus os ON o.order_id = os.order_id
LEFT JOIN tbl_customer c ON o.customer_id = c.customer_id
LEFT JOIN tbl_order od ON o.order_id = od.order_id
LEFT JOIN tbl_product p ON od.product_id = p.product_id
WHERE os.status = 'Pending'
GROUP BY o.order_id, od.product_id
ORDER BY o.order_date DESC";

$stmt = $conn->query($sql);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$groupedOrders = [];
foreach ($orders as $order) {
    $reference_number = $order['reference_number'];
    if (!isset($groupedOrders[$reference_number])) {
        $groupedOrders[$reference_number] = [];
    }
    $groupedOrders[$reference_number][] = $order;
}

// GET THE TOTAL PRODUCTS
$sqlTotalProducts = "SELECT COUNT(*) AS total_products FROM `tbl_product`";
$stmtTotalProducts = $conn->prepare($sqlTotalProducts);
$stmtTotalProducts->execute();
$resultTotalProducts = $stmtTotalProducts->fetch(PDO::FETCH_ASSOC);
$totalProducts = $resultTotalProducts['total_products'];

$sqlTotalAmountSum = "SELECT SUM(total_amount) AS total_sum FROM tbl_reports";
$stmtTotalAmountSum = $conn->prepare($sqlTotalAmountSum);
$stmtTotalAmountSum->execute();
$totalAmountSumResult = $stmtTotalAmountSum->fetch(PDO::FETCH_ASSOC);
$totalAmountSum = $totalAmountSumResult['total_sum'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!--===============================================================================================-->
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="shortcut icon" href="../assets/favicon/egg.png" type="image/x-icon">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="../assets/js/sweetalert2/dist/sweetalert2.css" />
</head>

<body class="animate__animated animate__fadeIn">
    <!--===============================================================================================-->
    <div class="d-flex flex-row justify-content-start align-items-start">
        <!--===============================================================================================-->
        <div class="d-none d-md-flex justify-content-start flex-column p-3 position-relative" id="sidebar">
            <div class="d-flex justify-content-center">
                <img onclick="window.location.href = 'dashboard'" style="cursor: pointer;" src="../assets/images/dashboard/logo.png" alt="">
            </div>
            <div class="d-flex flex-column p-3 mt-2" id="lists">
                <a href="dashboard"><span class="material-symbols-outlined">
                        home
                    </span>Dashboard</a>
                <a href="products"><span class="material-symbols-outlined">
                        shopping_bag
                    </span>Inventory</a>
                <a href="orders"><span class="material-symbols-outlined">
                        groups
                    </span>Orders</a>
                <a href="reports"><span class="material-symbols-outlined">
                        person
                    </span>Reports</a>
            </div>
            <span class="material-symbols-outlined backButton">
                close
            </span>
        </div>
        <!--===============================================================================================-->
        <div class="d-flex flex-column" style="width: 100%; overflow: hidden;">
            <nav class="navbar p-3 w-100" id="navbar">
                <span class="material-symbols-outlined" id="menuButton">
                    menu
                </span>

                <div class="d-flex flex-column position-relative">
                    <span class="material-symbols-outlined" id="profileButton">
                        person
                    </span>

                    <div class="d-none flex-column position-absolute" id="profileDropdown">
                        <!-- <a href="#">Profile</a> -->
                        <a href="../functions/admin_logout.php">Logout</a>
                    </div>
                </div>
            </nav>

            <div class="container pt-4 pt-md-5">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
                    <div class="col">
                        <div class="box d-flex justify-content-between flex-column p-3 bg-white rounded">
                            <div class="d-flex flex-column">
                                <h1>Welcome!</h1>
                                <h3>Admin</h3>
                            </div>
                            <!-- <a href="#">View Profile</a> -->
                        </div>
                    </div>

                    <div class="col">
                        <div class="box d-flex justify-content-between flex-column p-3 bg-white rounded">
                            <div class="d-flex flex-column">
                                <h1>Total products</h1>
                                <h3><?php echo count($activeProducts); ?></h3>
                            </div>
                            <!-- <a href="#">View</a> -->
                        </div>
                    </div>

                    <div class="col">
                        <div class="box d-flex justify-content-between flex-column p-3 bg-white rounded">
                            <div class="d-flex flex-column">
                                <h1>Total sales</h1>
                                <!-- Display the total sum here -->
                                <h3 style="color: #BB2525; font-weight: 900;">₱<?php echo number_format($totalAmountSum, 2); ?></h3>
                            </div>
                            <!-- <a href="#">View</a> -->
                        </div>
                    </div>

                </div>
            </div>


            <div class="container pt-4 pt-md-5 mb-4">

                <div class="d-flex flex-column gap-3" id="tableContainer">

                    <div class="d-flex w-100 px-3 py-4" id="tableTitle">
                        <h1 class="mt-1">Recent Orders</h1>
                    </div>

                    <div class="m-0 p-0 p-md-3">
                        <div class="table-responsive" style="overflow: scroll; height: 390px;">
                            <table id="example" class="table table-hover table-bordered">
                                <thead class="table-success">
                                    <tr>
                                        <th>Reference Number</th>
                                        <th>Payment Method</th>
                                        <th>Customer Name</th>
                                        <th>Total Products</th>
                                        <th>Total Amount</th>
                                        <th>Ordered Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($groupedOrders as $reference_number => $ordersGroup) : ?>
                                        <tr>
                                            <td style="color: #BB2525; font-weight: 900;"><?php echo $reference_number; ?></td>
                                            <td><?php echo reset($ordersGroup)['payment_method']; ?></td>
                                            <td><?php echo reset($ordersGroup)['customer_name']; ?></td>
                                            <td>
                                                <?php foreach ($ordersGroup as $order) : ?>
                                                    <?php if ($order === end($ordersGroup)) : ?>
                                                        <?php echo $order['total_products'] . '<br>'; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </td>
                                            <td>₱<?php echo reset($ordersGroup)['total_amount']; ?></td>
                                            <?php
                                            $orderDateTimestamp = strtotime(reset($ordersGroup)['order_date']);
                                            $formattedDate = date('F/d/Y', $orderDateTimestamp);
                                            $formattedTime = date('h:i A', $orderDateTimestamp);
                                            ?>
                                            <td>
                                                <?php echo $formattedDate; ?><br>
                                                <?php echo $formattedTime; ?>
                                            </td>
                                            <td style="color: #FF9B50; font-weight: 900;"><?php echo reset($ordersGroup)['order_status']; ?>⌛</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--===============================================================================================-->
    </div>
    <!--===============================================================================================-->

    <!--===============================================================================================-->

    <!--===============================================================================================-->
    <script src="../assets/js/dashboard.js"></script>
    <!--===============================================================================================-->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        new DataTable('#example');
    </script>
    <!--===============================================================================================-->
    <script src="../assets/js/sweetalert2/dist/sweetalert2.min.js"></script>
    <!--===============================================================================================-->
    <script>
        window.onload = function() {
            const login_success =
                '<?php echo isset($_SESSION["login_success"]) ? $_SESSION["login_success"] : "" ?>';

            if (login_success) {
                Swal.fire({
                    icon: "success",
                    text: "Login successfully!",
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                });

                <?php $_SESSION["login_success"] = false; ?>
            }
        };
    </script>
</body>

</html>