<?php
include '../database/connection.php';

session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location: admin_login');
}

$add_product = '';
$error_add = '';
$warning_add = '';
$warning_pic = '';

// ADD PRODUCT MODAL
if (isset($_POST['submit'])) {
    $product_name = $_POST['product_name'];
    $product_size = $_POST['product_size'];
    $product_price = $_POST['product_price'];
    $product_stocks = $_POST['product_stocks'];
    $product_status = '';

    $product_image = $_FILES['product_image']['name'];
    $image_size = $_FILES['product_image']['size'];
    $product_tmp_name = $_FILES['product_image']['tmp_name'];

    $image_hash = md5(uniqid(rand(), true));
    $file_extension = pathinfo($product_image, PATHINFO_EXTENSION);

    $new_image_name = $image_hash . '.' . $file_extension;

    $product_folder = '../assets/images/products/' . $new_image_name;

    if (!in_array($file_extension, ['jpg', 'jpeg', 'png'])) {
        $warning_pic = 'ONLY JPEG, JPG, PNG files are allowed.';
    } elseif ($image_size > 2000000) {
        $warning_add = 'Image size must be 2MB or less.';
    } else {
        $check_product = $conn->prepare("SELECT COUNT(*) FROM `tbl_product` WHERE product_name = ? AND product_size = ?");
        $check_product->execute([$product_name, $product_size]);
        $product_count = $check_product->fetchColumn();

        if ($product_count > 0) {
            $error_add = 'Product is already exist';
        } else {
            move_uploaded_file($product_tmp_name, $product_folder);

            if ($product_stocks >= 5 && $product_stocks <= 1000000) {
                $product_status = "Available";
            } elseif ($product_stocks >= 1 && $product_stocks < 5) {
                $product_status = "Low Stock";
            } elseif ($product_stocks == 0) {
                $product_status = "Not Available";
            } else {
                $product_status = "Unknown";
            }

            $insert_product = $conn->prepare("INSERT INTO `tbl_product` (product_name, product_size, product_price, product_image, product_stocks, product_status) VALUES (?, ?, ?, ?, ?, ?)");
            $insert_product->execute([$product_name, $product_size, $product_price, $new_image_name, $product_stocks, $product_status]);

            $add_product = "Product added successfully";
        }
    }
}

$update_product = '';
$warning_update = '';
$warning_picture = '';
$error_update = '';
// UPDATE PRODUCT MODAL
if (isset($_POST['update'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_size = $_POST['product_size'];
    $product_price = $_POST['product_price'];
    $product_stocks = $_POST['product_stocks'];
    $product_status = '';

    $stmt = $conn->prepare("SELECT COUNT(*) FROM `tbl_product` WHERE (product_name = ? AND product_size = ?) AND product_id <> ?");
    $stmt->execute([$product_name, $product_size, $product_id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $error_update = 'Product is already exist';
    } else {
        if ($product_stocks >= 5 && $product_stocks <= 1000000) {
            $product_status = "Available";
        } elseif ($product_stocks >= 1 && $product_stocks < 5) {
            $product_status = "Low Stock";
        } elseif ($product_stocks == 0) {
            $product_status = "Not Available";
        } else {
            $product_status = "Unknown";
        }

        if (!empty($_FILES['product_image']['name'])) {
            $image_hash = md5(uniqid(rand(), true));
            $file_extension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);

            $new_image_name = $image_hash . '.' . $file_extension;

            $product_folder = '../assets/images/products/' . $new_image_name;

            $image_size = $_FILES['product_image']['size'];
            $product_tmp_name = $_FILES['product_image']['tmp_name'];

            if (!in_array($file_extension, ['jpg', 'jpeg', 'png'])) {
                $warning_picture = 'ONLY JPEG, JPG, PNG files are allowed.';
            } elseif ($image_size > 2000000) {
                $warning_update = 'Image size must be 2MB or less.';
            } else {
                move_uploaded_file($product_tmp_name, $product_folder);
                $stmt = $conn->prepare("SELECT product_image FROM `tbl_product` WHERE product_id = ?");
                $stmt->execute([$product_id]);
                $old_image = $stmt->fetchColumn();

                if ($old_image && file_exists('../assets/images/products/' . $old_image)) {
                    unlink('../assets/images/products/' . $old_image);
                }

                $update_product = $conn->prepare("UPDATE `tbl_product` SET product_name=?, product_size=?, product_price=?, product_stocks=?, product_status=?, product_image=? WHERE product_id=?");
                $update_product->execute([$product_name, $product_size, $product_price, $product_stocks, $product_status, $new_image_name, $product_id]);
            }
        } else {
            $product_image = $_POST['existing_product_image'];

            $update_product = $conn->prepare("UPDATE `tbl_product` SET product_name=?, product_size=?, product_price=?, product_stocks=?, product_status=? WHERE product_id=?");
            $update_product->execute([$product_name, $product_size, $product_price, $product_stocks, $product_status, $product_id]);
        }

        $update_product = "Product updated successfully";
    }
}

// SELECT AND DISPLAY TO TABLE
$get = "SELECT * FROM `tbl_product`";
$stmt = $conn->query($get);
$product = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="shortcut icon" href="../assets/favicon/egg.png" type="image/x-icon">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!--===============================================================================================-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!--===============================================================================================-->
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
                <a href="products" class="active"><span class="material-symbols-outlined">
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
                <div class="d-flex flex-column gap-3" id="tableContainer">
                    <div class="d-flex flex-column w-100 p-4" id="tableTitle">
                        <h1>Products</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard" style="text-decoration: none;">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Products</li>
                            </ol>
                        </nav>
                    </div>

                    <div class="container pt-4 pt-md-5 mb-4">
                        <div class="d-flex align-items-center px-3" id="tableAddItem">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product
                                +</button>
                        </div>

                        <div class="m-0 p-0 p-md-3">
                            <div class="table-responsive" style="overflow: scroll; height: 390px;">
                                <table id="example" class="table table-hover table-bordered">
                                    <thead class="table-success">
                                        <tr>
                                            <th>Product ID</th>
                                            <th>Product Name</th>
                                            <th>Product Image</th>
                                            <th>Product Size</th>
                                            <th>Product Price</th>
                                            <th>Product Stocks</th>
                                            <th>Product Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($product as $products) : ?>
                                            <tr>
                                                <td><?php echo $products['product_id'] ?></td>
                                                <td><?php echo $products['product_name'] ?></td>
                                                <td><img src="../assets/images/products/<?php echo $products['product_image'] ?>" alt=""></td>
                                                <td><?php echo $products['product_size'] ?></td>
                                                <td>₱<?php echo $products['product_price'] ?></td>
                                                <td><?php echo $products['product_stocks'] ?></td>
                                                <td style="font-weight: 900; color:
                                        <?php
                                            if ($products['product_status'] === 'Available') {
                                                echo '#004225';
                                            } elseif ($products['product_status'] === 'Not Available') {
                                                echo '#BB2525';
                                            } elseif ($products['product_status'] === 'Low Stock') {
                                                echo '#E55604';
                                            } else {
                                                echo '#000';
                                            }
                                        ?>
                                    "><?php echo $products['product_status'] ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center place-items-center gap-2" id="actionLists">
                                                        <a href="#" class="view-product-link" data-id-product="<?php echo $products['product_id']; ?>" data-product-name="<?php echo $products['product_name']; ?>" data-product-image="<?php echo $products['product_image']; ?>" data-product-size="<?php echo $products['product_size'] ?>" data-product-price="<?php echo $products['product_price']; ?>" data-product-status="<?php echo $products['product_status'] ?>" data-product-stocks="<?php echo $products['product_stocks']; ?>" style="color:black;">
                                                            <span data-bs-toggle="modal" data-bs-target="#viewProductModal" class="material-symbols-outlined" id="view">
                                                                visibility
                                                            </span>
                                                        </a>

                                                        <a style="color: black;" href="#" onclick="openSettingsModal(
                                            <?php echo $products['product_id']; ?>,
                                            '<?php echo $products['product_name']; ?>',
                                            '<?php echo $products['product_image']; ?>',
                                            '<?php echo $products['product_size']; ?>',
                                            <?php echo $products['product_price']; ?>,
                                            <?php echo $products['product_stocks']; ?>,
                                            '<?php echo $products['product_status']; ?>'
                                        );">
                                                            <span data-bs-toggle="modal" data-bs-target="#settingsProductModal" class="material-symbols-outlined" id="settings">
                                                                settings
                                                            </span>
                                                        </a>

                                                        <a href="#" style="color: black;" class="delete-product-link" data-delete-product-id="<?php echo $products['product_id']; ?>">
                                                            <span data-bs-toggle="modal" data-bs-target="#deleteProductModal" class="material-symbols-outlined delete" id="delete">
                                                                delete
                                                            </span>
                                                        </a>

                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- View Modal -->
            <div class="modal fade" id="viewProductModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">View Product</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <label for="">Product ID : <span id="product_ID"></span></label>
                            <label for="">Product Name : <span id="product_Name"></span></label>
                            <label for="">Produce Image</label>
                            <img src="" alt="Product Image" id="product_Image">
                            <label for="">Product Size : <span id="product_Size"></span></label>
                            <label for="" class="mt-2">Product Price : <span id="product_Price"></span></label>
                            <label for="" class="mt-2">Product Stocks : <span id="product_Stocks"></span></label>
                            <label for="" class="mt-2">Product Status : <span id="product_Status"></span></label>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Settings Modal -->
            <div class="modal fade" id="settingsProductModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Product</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <label for="">Product ID</label>
                                <input style="opacity: 60%;" type="text" name="product_id" id="modal_product_id" readonly>
                                <label for="">Product Name</label>
                                <input class="mt-2" type="text" name="product_name" id="modal_product_name" required>
                                <input type="hidden" name="existing_product_image" id="existing_product_image">
                                <label for="" class="mt-2">Product Image</label>
                                <img src="" alt="Product Image" id="modal_product_image">
                                <input class="mt-2" type="file" name="product_image" id="modal_product_image_input" accept=".jpg, .jpeg, .png" style="border: 0px solid transparent !important; padding: 0px !important;" onchange="showSelectedImage(this);">
                                <label for="">Product Size</label>
                                <select name="product_size" id="modal_product_size" required>
                                    <option value="" disabled selected>--SELECT PRODUCT SIZES--</option>
                                    <option value="SMALL">SMALL</option>
                                    <option value="MEDIUM">MEDIUM</option>
                                    <option value="LARGE">LARGE</option>
                                    <option value="DOUBLE YOLK">DOUBLE YOLK</option>
                                </select>
                                <label for="" class="mt-2">Product Price</label>
                                <input class="mt-2" type="text" name="product_price" id="modal_product_price" oninput="validateProductPrice(this)" required>
                                <label for="" class="mt-2">Product Stocks</label>
                                <input class="mt-2" type="text" name="product_stocks" id="modal_product_stocks" oninput="validateProductStocks(this)" required>
                                <label for="">Product Status</label>
                                <input style="opacity: 60%;" type="text" name="product_status" id="modal_product_status" readonly>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <input type="submit" name="update" class="btn btn-success" value="Update">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete Modal -->
            <div class="modal fade" id="deleteProductModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Delete Product</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <label for="">Are you sure you want to delete?</label>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Add Modal -->
            <div class="modal fade" id="addProductModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Product</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <label for="">Product Name</label>
                                <input class="mt-2" type="text" name="product_name" id="" placeholder="Enter Product Name..." required>
                                <label for="productImage" class="mt-2">Product Image</label>
                                <input class="mt-2" type="file" name="product_image" accept=".jpg, .jpeg, .png" id="productImage" onchange="previewImage()" required>
                                <img src="#" alt="Product Preview" id="imagePreview" class="mt-2" style="display: none; max-width: 100%; max-height: 200px;">
                                <h1 style="font-size: 15px; color: red; text-align: right;">ONLY JPEG JPG PNG ALLOWED</h1>
                                <label for="">Product Size</label>
                                <select name="product_size" id="" required>
                                    <option value="" disabled selected>--SELECT PRODUCT SIZES--</option>
                                    <option value="SMALL">SMALL</option>
                                    <option value="MEDIUM">MEDIUM</option>
                                    <option value="LARGE">LARGE</option>
                                    <option value="DOUBLE YOLK">DOUBLE YOLK</option>
                                </select>
                                <label for="productPrice" class="mt-2">Product Price</label>
                                <input class="mt-2" type="text" name="product_price" id="productPrice" oninput="validateProductPrice(this)" placeholder="Enter Product Price..." required>
                                <label for="" class="mt-2">Product Stocks</label>
                                <input class="mt-2" type="text" name="product_stocks" id="productStocks" oninput="validateProductStocks(this)" placeholder="Enter Product Stocks..." required>
                                <input type="hidden" name="product_status" value="Available">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <input type="submit" name="submit" value="Submit" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>



            </div>
            <!--===============================================================================================-->
        </div>
        <!--===============================================================================================-->
        <script src="../assets/js/dashboard.js"></script>
        <!--===============================================================================================-->
        <script src="../assets/js/products.js"></script>
        <!--===============================================================================================-->
        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
        <!--===============================================================================================-->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <!--===============================================================================================-->
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <!--===============================================================================================-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

        <script src="../assets/js/sweetalert2/dist/sweetalert2.min.js"></script>
        <!--===============================================================================================-->
        <script>
            new DataTable('#example');
        </script>
        <!--===============================================================================================-->
        <script src="../ajax/admin_get_product.js"></script>
        <!-- SWEET ALERT -->
        <?php if ($add_product) : ?>
            <script>
                Swal.fire({
                    icon: "success",
                    title: "<?php echo $add_product; ?>",
                    timer: 3000,
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                });
            </script>
        <?php endif ?>

        <?php if ($warning_add) : ?>
            <script>
                Swal.fire({
                    icon: "warning",
                    title: "<?php echo $warning_add; ?>",
                    timer: 3000,
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                });
            </script>
        <?php endif ?>

        <?php if ($warning_pic) : ?>
            <script>
                Swal.fire({
                    icon: "warning",
                    title: "<?php echo $warning_pic; ?>",
                    timer: 3000,
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                });
            </script>
        <?php endif ?>

        <?php if ($error_add) : ?>
            <script>
                Swal.fire({
                    icon: "error",
                    title: "<?php echo $error_add ?> ",
                    timer: 3000,
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                });
            </script>
        <?php endif ?>

        <?php if ($update_product) : ?>
            <script>
                Swal.fire({
                    icon: "success",
                    title: "<?php echo $update_product; ?>",
                    timer: 3000,
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                });
            </script>
        <?php endif ?>

        <?php if ($error_update) : ?>
            <script>
                Swal.fire({
                    icon: "error",
                    title: "<?php echo $error_update; ?>",
                    timer: 3000,
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                })
            </script>
        <?php endif ?>

        <?php if ($warning_update) : ?>
            <script>
                Swal.fire({
                    icon: "warning",
                    title: "<?php echo $warning_update; ?>",
                    timer: 3000,
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                });
            </script>
        <?php endif ?>

        <?php if ($warning_picture) : ?>
            <script>
                Swal.fire({
                    icon: "warning",
                    title: "<?php echo $warning_picture ?>",
                    timer: 3000,
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                })
            </script>
        <?php endif ?>
        <script>
            // VIEW MODAL
            $(document).ready(function() {
                $("#example").on("click", ".view-product-link", function() {
                    const productId = $(this).data("id-product");
                    const productName = $(this).data("product-name");
                    const productImage = $(this).data("product-image");
                    const productSize = $(this).data("product-size");
                    const productPrice = $(this).data("product-price");
                    const productStocks = $(this).data("product-stocks");
                    const productStatus = $(this).data("product-status");

                    $("#product_ID").text(productId).css("font-weight", "900");
                    $("#product_Name").text(productName).css("font-weight", "900");
                    $("#product_Image").attr("src", "../assets/images/products/" + productImage);
                    $("#product_Size").text(productSize).css("font-weight", "900");
                    $("#product_Price")
                        .html("&#8369;" + productPrice)
                        .css("font-weight", "900");
                    $("#product_Stocks").text(productStocks);

                    const productStatusElement = $("#product_Status");
                    productStatusElement.text(productStatus);

                    if (productStatus === "Available") {
                        productStatusElement.css("color", "#004225");
                        productStatusElement.css("font-weight", "900");
                    } else if (productStatus === "Not Available") {
                        productStatusElement.css("color", "#BB2525");
                        productStatusElement.css("font-weight", "900");
                    } else if (productStatus === "Low Stock") {
                        productStatusElement.css("color", "#E55604");
                        productStatusElement.css("font-weight", "900");
                    } else {
                        productStatusElement.css("color", "default-color");
                        productStatusElement.css("font-weight", "normal");
                    }
                });
            });


            // SHOW IMAGE IN ADD MODAL
            function previewImage() {
                const input = document.getElementById("productImage");
                const preview = document.getElementById("imagePreview");

                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = "block";
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }

            // LIMIT DECIMAL ADD PRICE
            function validateProductPrice(input) {
                input.value = input.value.replace(/[^0-9.]/g, "");

                input.value = input.value.replace(/^0+/, "");

                if (input.value.length > 60) {
                    input.value = input.value.slice(0, 60);
                }

                const parts = input.value.split(".");
                if (parts.length > 1) {
                    parts[1] = parts[1].slice(0, 2);
                    input.value = parts[0] + "." + parts[1];
                }

                const floatValue = parseFloat(input.value);
                if (!isNaN(floatValue)) {
                    const integerPart = Math.floor(floatValue);
                    const decimalPart = floatValue - integerPart;
                    if (decimalPart > 0.99) {
                        input.value = (integerPart + 0.99).toFixed(2);
                    }
                }
            }

            // PRODUCT STOCKS SO THAT IT CANT TYPE LETTERS
            function validateProductStocks(input) {
                input.value = input.value.replace(/[^0-9]/g, '');

            }


            // SETTINGS MODAL
            function openSettingsModal(
                product_id,
                product_name,
                product_image,
                product_size,
                product_price,
                product_stocks,
                product_status
            ) {
                document.getElementById("modal_product_id").value = product_id;
                document.getElementById("modal_product_name").value = product_name;

                const basePath = "../assets/images/products/";
                document.getElementById("modal_product_image").src = basePath + product_image;

                document.getElementById("modal_product_size").value = product_size;
                document.getElementById("modal_product_price").value = product_price;
                document.getElementById("modal_product_stocks").value = product_stocks;
                document.getElementById("modal_product_status").value = product_status;
                document.getElementById("existing_product_image").value = product_image;
            }

            function showSelectedImage(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        document.getElementById("modal_product_image").src = e.target.result;
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }

            // DELETE MODAL
            $(document).ready(function() {
                $("#example").on("click", ".delete-product-link", function() {
                    const productId = $(this).data("delete-product-id");
                    const deleteUrl = "delete_product.php?product_id=" + productId;
                    $("#confirmDelete").attr("data-delete-url", deleteUrl);
                });

                $("#confirmDelete").on("click", function() {
                    const deleteUrl = $(this).attr("data-delete-url");

                    $.get(deleteUrl, function(response) {
                        if (response === "success") {
                            Swal.fire({
                                icon: "success",
                                title: "Product successfully deleted",
                                timer: 3000,
                                toast: true,
                                position: "top-end",
                                showConfirmButton: false,
                            });

                            setTimeout(function() {
                                window.location.href = "products";
                            }, 2000);
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: "Failed to delete product",
                            });
                        }
                    });

                    $("#deleteProductModal").modal("hide");
                });
            });
        </script>

</body>

</html>