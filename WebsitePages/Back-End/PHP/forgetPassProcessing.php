<?php
    session_start();

    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "raqeebdb";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if(isset($_POST['email'])) {
        if (empty($_POST["email"])) {
            echo "<div class='error-message' style='color:red;'>Please enter your email to retrieve your account.</div>";
        } else {
            $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "<div class='error-message' style='color:red;'>Please enter a valid email address!</div>";
            } else {
                $query = "SELECT * FROM company WHERE Email='$email'";
                $row = mysqli_query($conn, $query);

                if (mysqli_num_rows($row) > 0) {
                    $token = uniqid(md5(time()));
                    $_SESSION["token"] = $token;
                    $_SESSION["tokenTimeStamp"] = time();
                    $_SESSION["email"] = $email;

                    date_default_timezone_set('Asia/Riyadh');

                    require '../../Back-End/PHP/PHPMailer/PHPMailerAutoload.php';
                    $mail = new PHPMailer;
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->Port = 587;
                    $mail->SMTPSecure = 'tls';
                    $mail->SMTPAuth = true;
                    $mail->Username = "raqeeb.project@gmail.com";
                    $mail->Password = "vjrd xeyj lrlw ymcf";
                    $mail->setFrom('raqeeb.project@gmail.com', 'Raqeeb');
                    $mail->addAddress($email, 'User');
                    $mail->Subject = 'Forgot password';
                    $mail->msgHTML("<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Account Reclaim</title>

</head>
<body style='margin: 0;
            padding: 0;
            font-family: 'Montserrat', sans-serif;
            color: #718089;
            text-align: center;
            background-image: url('http://localhost/2024-25_GP_02-main/WebsitePages/images/emailBackground.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            flex-direction:column;
            justify-content: center;
            align-items: center;
            font-size;13px;'>
<img style='width:40%; height:auto;text-align:center; margin-bottom:7px;'src= 'http://localhost/2024-25_GP_02-main/WebsitePages/images/Logo2.png'> 
    <div class='content' style='background: #ecf0f3; padding: 20px; border-radius: 10px;box-shadow:10px 10px 10px #d1d9e6, -10px -10px 10px #f9f9f9;'>
        <p>Greetings,</p>
        <p>
            You have requested to reclaim your account. If the request was made by you, please click 
            <a href='http://localhost/2024-25_GP_02-main/WebsitePages/Back-End/PHP/reclaimAccount.php?token=$token&email=$email' style=' color: #004aad;'>Here</a>
        </p>
        <p>Please note that the reset link will expire in 10 minutes. </p>
        <p> If you did not make this request, please ignore this email.</p>
    </div>
</body>
</html>");
                    $mail->AltBody = 'This is a plain-text message body';

                    if (!$mail->send()) {
                        echo "<div class='error-message' style='color:red;'>Mailer Error: {$mail->ErrorInfo}</div>";
                    } else {
                        echo "<div class='success-message' style='color:green;'>Email sent! Please check your inbox to reset your password.</div>";
                    }
                } else {
                    echo "<div class='form-message' id='msg' > <p style='color:red;'> No account was found with that email</p> <a href='../../Front-End/HTML/login.html'>Sign up instead </a> </div>";
                }
            }
        }
    }
?>
