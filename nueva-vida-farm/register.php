<?php
include 'database/connection.php';

session_start();
if (isset($_SESSION['customer_id'])) {
    header('location: home.php');
}

$registration_success = '';
$registration_error = '';
$registration_email = '';
if (isset($_POST['submit'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashed_password = sha1($password);
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    if ($email) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_customer WHERE email = ?");
        $stmt->execute([$email]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $registration_email = 'Email is already taken';
        } else {
            if (strlen($password) < 8) {
                $registration_error = 'Password must contain 8 characters';
            } else {
                $stmt = $conn->prepare("INSERT INTO tbl_customer (first_name, last_name, email, password, address, phone) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$first_name, $last_name, $email, $hashed_password, $address, $phone]);
                $registration_success = 'Registration successful';
            }
        }
    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--===============================================================================================-->
    <title>Register</title>
    <link rel="stylesheet" href="assets/css/auth.css">
    <!--===============================================================================================-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!--===============================================================================================-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <!--===============================================================================================-->
    <link rel="shortcut icon" href="assets/favicon/egg.png" type="image/x-icon">
    <!--===============================================================================================-->
    <link
      rel="stylesheet"
      href="assets/js/sweetalert2/dist/sweetalert2.css"
    />
</head>

<body>
    <div class="vh-100 d-flex justify-content-center align-items-center" id="mainContainer">
        <div class="d-flex flex-row">
            <form method="POST" action="" class="d-flex flex-column p-5" style="background-color: black;" id="loginContainer">
                <h1>Welcome to Nueva Vida Farm</h1>
                <h2>Sign up an Account.</h2>

                <div class="d-flex justify-content-between flex-column flex-md-row gap-3 gap-md-4 flex-row">
                    <div class="d-flex flex-column w-100">
                        <label for="">First Name</label>
                        <input type="text" name="first_name" placeholder="First Name" value="<?php echo isset($_POST['first_name']) ? $_POST['first_name'] : ''; ?>">
                    </div>
                    <div class="d-flex flex-column w-100">
                        <label for="">Last Name</label>
                        <input type="text" name="last_name" placeholder="Last Name" value="<?php echo isset($_POST['last_name']) ? $_POST['last_name'] : '' ?>">
                    </div>
                </div>
                <label for="" class="mt-3">Email</label>
                <input type="text" name="email" placeholder="Email" oninput="this.value = this.value.replace(/\s/g, '')"  value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
                <label for="" class="mt-3">Password</label>
                <input type="password" name="password" placeholder="Password" oninput="this.value = this.value.replace(/\s/g, '')">
                <label for="" class="mt-3">Address</label>
                <input type="text" name="address" placeholder="Address" value="<?php echo isset($_POST['address']) ? $_POST['address'] : ''; ?>">
                <label for="" class="mt-3">Phone</label>
                <input type="text" name="phone" id="phone" placeholder="Phone" oninput="validatePhone(this)" value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : '' ?>">


                <button type="submit" name="submit" class="mt-5">Submit</button>
                <div class="d-flex justify-content-center mt-2">
                    <a href="login.php">Already have an account? Login here...</a>
                </div>
            </form>

            <div class="d-none d-lg-flex" id="imageCard">
                <img src="assets/images/auth/login.jpg" alt="">
            </div>
        </div>
    </div>

    <!--===============================================================================================-->
    <script src="assets/js/sweetalert2/dist/sweetalert2.min.js"></script>
    <?php if ($registration_success): ?>
    <script>
            Swal.fire({
            icon: "success",
            title: "Registration successfully",
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
        });
    </script>
    <?php endif?>

    <?php if ($registration_email): ?>
    <script>
            Swal.fire({
            icon: "error",
            title: "Email is already taken",
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
        });
    </script>
    <?php endif?>

    <?php if ($registration_error): ?>
    <script>
            Swal.fire({
            icon: "error",
            title: "Password must contain 8 characters",
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
        });
    </script>
    <?php endif?>

    <script>
        function validatePhone(input) {
            input.value = input.value.replace(/\D/g, '');

            if (input.value.length > 11) {
                input.value = input.value.slice(0, 11);
            }
        }
    </script>
</body>

</html>
