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

$getUserReports = "SELECT * FROM `tbl_user_reports` WHERE customer_id = ?";
$stmt = $conn->prepare($getUserReports);
$stmt->execute([$customer_id]);
$userReports = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!--===============================================================================================-->
    <link rel="stylesheet" href="assets/js/sweetalert2/dist/sweetalert2.css" />
</head>

<body>
    <div id="spinnerContainer">
        <div class="spinner"></div>
    </div>

    <nav class="navbar px-3 py-3 px-md-5">
        <img src="./assets/images/dashboard/logo.png" alt="" style="cursor: pointer;" onclick="window.location.href = './home'">

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
            <div class="d-flex px-3 pt-3 pt-sm-0 px-sm-3">
                <h1>Completed Orders <i class="fa-solid fa-check-double"></i></h1>
            </div>

            <div class="breadcrumbs">
                <a href="order_status" style="margin-left: 20px; text-decoration: none;">Order status</a> <span> / Completed orders</span>
            </div>

            <div class="p-3 m-0">

                <div class="table-responsive">
                    <table id="example" class="table table-dark table-hover table-striped position-relative">
                        <thead class="table-success">
                            <tr>
                                <th>Reference Number</th>
                                <th>Total Amount</th>
                                <th>Product Ordered</th>
                                <th>Order Status</th>
                                <th>Date Ordered</th>
                                <th>Date Completed</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($userReports as $reports) : ?>
                                <tr>
                                    <td style="color: #BB2525; font-weight: 900;"><?php echo $reports['reference_number'] ?></td>
                                    <td>₱<?php echo $reports['total_amount'] ?></td>
                                    <td><?php echo $reports['total_products'] ?></td>
                                    <td style="color: #96C291; font-weight: 900;"><?php echo $reports['status'] ?> ✅</td>
                                    <td>
                                        <?php $orderDateTimestamp = strtotime(($reports)['order_date']); ?>
                                        <?php $formattedDate = date('F j, Y', $orderDateTimestamp); ?>
                                        <?php $formattedTime = date('h:i A', $orderDateTimestamp); ?>
                                        <?php echo $formattedDate . '<br>' . $formattedTime; ?>
                                    </td>
                                    <td>
                                        <?php $orderDateTimestamp = strtotime(($reports)['update_date']); ?>
                                        <?php $formattedDate = date('F j, Y', $orderDateTimestamp); ?>
                                        <?php $formattedTime = date('h:i A', $orderDateTimestamp); ?>
                                        <?php echo $formattedDate . '<br>' . $formattedTime; ?>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-danger delete-transaction" data-reportid="<?php echo $reports['report_id'] ?>">Delete</a>
                                    </td>
                                </tr>
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
    <script>
        $(document).ready(function() {
            $('.delete-transaction').on('click', function(e) {
                e.preventDefault();

                var reportId = $(this).data('reportid');

                Swal.fire({
                    title: 'Delete Transaction',
                    text: 'Are you sure you want to delete this transaction?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'functions/remove_completed.php?report_id=' + reportId;
                    }
                });
            });
        });
    </script>
    <?php
    if (isset($_SESSION['delete_completed'])) {
        echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "Order deleted successfully",
                        timer: 3000,
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                    });
                </script>';

        unset($_SESSION['delete_completed']);
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