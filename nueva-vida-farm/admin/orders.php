<?php
include '../database/connection.php';

session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location: admin_login');
}

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
        GROUP BY o.order_id, od.product_id";

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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <link rel="shortcut icon" href="../assets/favicon/egg.png" type="image/x-icon">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!--===============================================================================================-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="../assets/js/sweetalert2/dist/sweetalert2.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

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

            <div class="container pt-4 pt-md-5 mb-4">
                <div class="d-flex flex-column  gap-3" id="tableContainer">

                    <div class="d-flex flex-column w-100 px-3 py-4" id="tableTitle">
                        <h1>Orders</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard" style="text-decoration: none;">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Orders</li>
                            </ol>
                        </nav>
                    </div>

                    <div class="px-3 pb-3">
                        <div class="d-flex justify-content-end mb-3">
                            <div class="btn-group gap-1" role="group" aria-label="Filter Orders">
                                <button style="border-radius: 10px;" type="button" class="btn btn-success" id="filterPending">Pending</button>
                                <button style="border-radius: 10px;" type="button" class="btn btn-success" id="filterReadyToPick">Ready to pick</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="example" class="table table-hover table-bordered">
                                <thead class="table-success">
                                    <tr>
                                        <th>Reference Number</th>
                                        <th>Order Status</th>
                                        <th>Payment Method</th>
                                        <th>Customer Name</th>
                                        <th>Total Amount</th>
                                        <th>Total Product</th>
                                        <th>Order Date</th>
                                        <th>Manage Order</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($groupedOrders as $reference_number => $ordersGroup) : ?>
                                        <?php if (reset($ordersGroup)['order_status'] !== 'Completed') : ?>
                                            <tr>
                                                <td style="color: #BB2525; font-weight: 900"><?php echo $reference_number; ?></td>
                                                <td style="font-weight: 900; color: <?php echo (reset($ordersGroup)['order_status'] === 'Pending') ? '#E55604' : ((reset($ordersGroup)['order_status'] === 'Ready to pick') ? '#3D0C11' : ''); ?>">
                                                    <?php if (reset($ordersGroup)['order_status'] === 'Pending') : ?>
                                                        <?php echo reset($ordersGroup)['order_status']; ?> ⌛
                                                    <?php elseif (reset($ordersGroup)['order_status'] === 'Ready to pick') : ?>
                                                        <?php echo reset($ordersGroup)['order_status']; ?> 📦
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo reset($ordersGroup)['payment_method']; ?></td>
                                                <td><?php echo reset($ordersGroup)['customer_name']; ?></td>
                                                <td>₱<?php echo reset($ordersGroup)['total_amount']; ?></td>
                                                <td>
                                                    <?php foreach ($ordersGroup as $order) : ?>
                                                        <?php if ($order === end($ordersGroup)) : ?>
                                                            <?php echo $order['total_products'] . '<br>'; ?>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </td>
                                                <!-- DATE -->
                                                <td>
                                                    <?php
                                                    $currentStatus = reset($ordersGroup)['order_status'];
                                                    $orderDateTimestamp = strtotime(reset($ordersGroup)['order_date']);

                                                    if ($currentStatus === 'Pending') {
                                                        $formattedDate = date('F/d/Y', $orderDateTimestamp);
                                                        $formattedTime = date('h:i A', $orderDateTimestamp);
                                                        echo $formattedDate . '<br>' . $formattedTime;
                                                    } elseif ($currentStatus === 'Ready to pick') {
                                                        $updatedDateTimestamp = strtotime(reset($ordersGroup)['update_date']);
                                                        $formattedDate = date('F/d/Y', $updatedDateTimestamp);
                                                        $formattedTime = date('h:i A', $updatedDateTimestamp);
                                                        echo $formattedDate . '<br>' . $formattedTime;
                                                    }
                                                    ?>
                                                </td>
                                                <form action="../components/update_status.php" method="POST">
                                                    <td>
                                                        <select name="order_status" style="border: none; background-color: #91ca9e; padding: 10px; border-radius: 10px; font-weight: 900;" class="drop-down" onchange="this.form.submit()">
                                                            <?php $currentStatus = reset($ordersGroup)['order_status']; ?>
                                                            <?php if ($currentStatus === 'Pending') : ?>
                                                                <option value="Pending" selected>Pending</option>
                                                                <option value="Ready to pick">Ready to pick</option>
                                                            <?php elseif ($currentStatus === 'Ready to pick') : ?>
                                                                <option value="Ready to pick" selected>Ready to pick</option>
                                                                <option value="Pending">Pending</option>
                                                                <option value="Completed">Completed</option>
                                                            <?php endif; ?>
                                                        </select>
                                                        <input type="hidden" name="order_id" value="<?php echo reset($ordersGroup)['order_id']; ?>">
                                                    </td>
                                                </form>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>



                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--===============================================================================================-->
    </div>
    <!--===============================================================================================-->
    <script src="../assets/js/dashboard.js"></script>
    <!--===============================================================================================-->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <!--===============================================================================================-->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!--===============================================================================================-->
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!--===============================================================================================-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!--===============================================================================================-->
    <script>
        $(document).ready(function() {
            var table = $('#example').DataTable({
                "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>' +
                    '<"row"<"col-sm-12"t>>' +
                    '<"row"<"col-sm-5"i><"col-sm-7"p>>',
                "ordering": true,
                "info": false,
                "paging": true,
                "bLengthChange": false,
                "searching": true,
            });

            $('#filterPending').on('click', function() {
                table.column(1).search('Pending').draw();
            });

            $('#filterReadyToPick').on('click', function() {
                table.column(1).search('Ready to pick').draw();
            });
        });
    </script>



    <!--===============================================================================================-->
    <script src="../assets/js/sweetalert2/dist/sweetalert2.min.js"></script>
    <!--===============================================================================================-->
    <?php
    if (isset($_SESSION['update_status'])) {
        echo '<script>
                            Swal.fire({
                                icon: "success",
                                title: "' . $_SESSION['update_status'] . '",
                                timer: 3000, // Auto close the alert after 3 seconds
                                toast: true,
                                position: "top-end",
                                showConfirmButton: false
                            });
                        </script>';
        unset($_SESSION['update_status']);
    }
    ?>
</body>

</html>