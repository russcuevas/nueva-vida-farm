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
    <title>Reports</title>
    <link rel="shortcut icon" href="../assets/favicon/egg.png" type="image/x-icon">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <!--===============================================================================================-->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!--===============================================================================================-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        .no-line-breaks {
            white-space: nowrap;
        }
    </style>

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
                        <a href="#">Profile</a>
                        <a href="../functions/admin_logout.php">Logout</a>
                    </div>
                </div>
            </nav>

            <div class="container pt-4 pt-md-5 mb-4">
                <div class="d-flex flex-column gap-3" id="tableContainer">

                    <div class="d-flex flex-column w-100 px-3 py-4" id="tableTitle">
                        <h1>Reports</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard" style="text-decoration: none;">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Reports</li>
                            </ol>
                        </nav>
                    </div>

                    <div class="px-3">
                        <div>
                            <div class="d-flex flex-row justify-content-between">
                                <h6 style="font-size: 20px;">Month and year : </h6>

                            <div class="d-flex gap-1">
                                <button id="export-pdf" class="btn btn-primary">Export <i class="fa-regular fa-file-pdf"></i></button>
                                <button id="export-excel" class="btn btn-success">Export <i class="fa-regular fa-file-excel"></i></button>
                            </div>


                            </div>
                            <div class="d-flex gap-1">
                                <select id="filterMonth">
                                        <option value="">All Months</option>
                                        <option value="January">January</option>
                                        <option value="February">February</option>
                                        <option value="March">March</option>
                                        <option value="April">April</option>
                                        <option value="May">May</option>
                                        <option value="June">June</option>
                                        <option value="July">July</option>
                                        <option value="August">August</option>
                                        <option value="September">September</option>
                                        <option value="October">October</option>
                                        <option value="November">November</option>
                                        <option value="December">December</option>
                                </select>
                                <select id="filterYear">
                                        <option value="">All Years</option>
                                        <option value="2023">2023</option>
                                        <option value="2024">2024</option>
                                </select>
                            </div>

                            <div class="d-flex gap-2 mt-2">
                                    <button style="display: none;" class="btn btn-primary" onclick="applyFilters()">Filter by</button>
                            </div>

                        </div>
                    </div>

                    <div class="px-3 pb-3">
                        <div class="table-responsive" style="overflow: scroll; height: 530px !important;">
                            <table id="example" class="table table-hover table-bordered">
                                <thead class="table-success">
                                    <tr>
                                        <th>Reference No.</th>
                                        <th>Payment Method</th>
                                        <th>Customer Name</th>
                                        <th>Total Product</th>
                                        <th>Order Date</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($groupedOrders as $reference_number => $ordersGroup): ?>
                                    <?php if (reset($ordersGroup)['order_status'] === 'Completed'): ?>
                                        <tr>
                                            <td style="color: #BB2525; font-weight: 900"><?php echo $reference_number; ?></td>
                                            <td><?php echo reset($ordersGroup)['payment_method']; ?></td>
                                            <td><?php echo reset($ordersGroup)['customer_name']; ?></td>
                                            <td class="no-line-breaks">
                                                <?php foreach ($ordersGroup as $order): ?>
                                                    <?php if ($order === end($ordersGroup)): ?>
                                                        <?php echo $order['total_products'] . '<br>'; ?>
                                                    <?php endif;?>
                                                <?php endforeach;?>
                                            </td>
                                            <td>
                                            <?php
$orderDateTimestamp = strtotime(reset($ordersGroup)['order_date']);
$formattedDate = date('F/d/Y', $orderDateTimestamp);
echo $formattedDate;
?>
                                            </td>
                                            <td><?php echo reset($ordersGroup)['total_amount']; ?></td>
                                            <td style="color: #004225; font-weight: 900;"><?php echo reset($ordersGroup)['order_status']; ?></td>
                                        </tr>
                                    <?php endif;?>
                                <?php endforeach;?>
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
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.2/xlsx.full.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-beta4/html2canvas.min.js"></script>
    <script src="../assets/js/reports.js"></script>

<script>
    window.jsPDF = window.jspdf.jsPDF;
</script>

</body>

</html>
