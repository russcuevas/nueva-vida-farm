<?php
use PHPMailer\PHPMailer\PHPMailer;

require '../assets/mailer/vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $mobile = $_POST["mobile"];
    $message = $_POST["message"];

    if (empty($name) || empty($mobile) || empty($message)) {
        $response = [
            'success' => 'false',
            'message' => 'Fields cannot be empty',
        ];
        header('Content-Type: application.json');
        echo json_encode($response);
    } else {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->SMTPDebug = 0;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 587;
            $mail->Username = 'russelarchiefoodorder@gmail.com';
            $mail->Password = 'cjwitldatrerscln';

            $mail->Port = 587;

            $mail->setFrom('nuevavidafarm@gmail.com', 'Nueva Vida Farm');
            $mail->addAddress('russelarchiefoodorder@gmail.com');

            $mail->isHTML(true);
            $mail->Subject = 'Message from everyone';
            $mail->Body = "

        <html>
        <head>
            <style>
                /* Inline CSS for email compatibility */
                body {
                    background-color: #fff;
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    padding: 20px;
                }
                .header {
                    background-color: #404040;
                    color: #ffffff;
                    text-align: center;
                    padding: 10px;
                }
                .content {
                    padding: 20px;
                }
                .footer {
                    background-color: #f5f5f5;
                    text-align: center;
                    padding: 10px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1 style='color: #9fef00 '>Nueva vida farm</h1>
                </div>
                <div class='content'>
                    <h3>Thank you for your contacting us!</h3>
                    <p>Name: $name</p>
                    <p>Mobile: $mobile</p>
                    <p>Message: $message</p>
                </div>
            </div>
        </body>
        </html>
        ";

            $mail->send();
            $response = [
                'success' => true,
                'message' => 'Message sent!',
            ];
            header('Content-Type: application/json');
            echo json_encode($response);

        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo,
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    }
} else {
    header('location: ../home');
}
