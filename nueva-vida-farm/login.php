<?php
include 'database/connection.php';

session_start();
if (isset($_SESSION['customer_id'])) {
    header('location: home');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--===============================================================================================-->
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/auth.css">
    <!--===============================================================================================-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!--===============================================================================================-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!--===============================================================================================-->
    <link rel="shortcut icon" href="assets/favicon/egg.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="assets/css/HoldOn.min.css">
    <link rel="stylesheet" href="assets/js/sweetalert2/dist/sweetalert2.css" />
</head>

<body class="animate__animated animate__fadeIn">
    <div class="vh-100 d-flex justify-content-center align-items-center" id="mainContainer">
        <div class="d-flex flex-row">
            <form method="POST" action="functions/login.php" class="d-flex flex-column p-5 loginForm" style="background-color: black;" id="loginContainer">
                <h1>Welcome to Nueva Vida Farm</h1>
                <h2>Login Account.</h2>

                <label for="" class="mt-3">Email</label>
                <input type="text" name="email" placeholder="Email" oninput="this.value = this.value.replace(/\s/g, '')" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
                <label for="" class="mt-3">Password</label>
                <input type="password" name="password" placeholder="Password">

                <button type="submit" class="mt-5">Submit</button>
                <div class="d-flex justify-content-center mt-2">
                    <a href="register" style="font-weight: 900;">Dont have an account? Click here..</a>
                </div>
            </form>

            <div class="d-none d-lg-flex" id="imageCard">
                <img src="assets/images/auth/login.jpg" alt="">
            </div>
        </div>
    </div>
    <!--===============================================================================================-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="assets/js/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="ajax/login.js"></script>
    <script src="assets/js/HoldOn.min.js"></script>
</body>

</html>