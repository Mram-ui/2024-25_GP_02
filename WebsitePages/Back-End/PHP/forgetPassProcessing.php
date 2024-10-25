<?php

session_start();

include("dbConnection.php");

if(isset($_POST['email'])){

    if (empty($_POST["email"])) {
        echo '
            <a href="../../Back-End/PHP/index.php"><img id="logo" src="../../images/Logo2.png" alt="Company Logo"></a>
            <a id="arrow" href="../../Front-End/HTML/login.html"><i  class="fa fa-chevron-left" style="color: #003f91; font-size: 30px; float: left; margin-left: 20px; margin-top: 20px;"></i></a>
            <h2 id="forgotTitle" class="title" style="margin-top: 3%;">FORGET YOUR PASSWORD?</h2>
            <br> <br> 
            <div id="forgotDesc" > 
                <p style="font-size: 17px;">Please enter your registered email address and<br> we will send you an email to reset your password</p>
                <form class="form" id="ForgotPasswordForm">
                    <br>
                    <input name="femail" class="form__input" id="email" type="email" placeholder="Email" required>
                    <div class="errorMessage" id="EmailNotValid"></div>
                    <button type="submit" class="form__button button submit"> SEND</button>
                  </form>
                  <div class="form-message" id="msg" style="color:red;">Please enter your email to retreive your account</div>
            </div>';
    } else {
        $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo '
            <a href="../../Back-End/PHP/index.php"><img id="logo" src="../../images/Logo2.png" alt="Company Logo"></a>
            <a id="arrow" href="../../Front-End/HTML/login.html"><i  class="fa fa-chevron-left" style="color: #003f91; font-size: 30px; float: left; margin-left: 20px; margin-top: 20px;"></i></a>
            <h2 id="forgotTitle" class="title" style="margin-top: 3%;">FORGET YOUR PASSWORD?</h2>
            <br> <br> 
            <div id="forgotDesc" > 
                <p style="font-size: 17px;">Please enter your registered email address and<br> we will send you an email to reset your password</p>
                <form class="form" id="ForgotPasswordForm">
                    <br>
                    <input name="femail" class="form__input" id="email" type="email" placeholder="Email" required>
                    <div class="errorMessage" id="EmailNotValid"></div>
                    <button type="submit" class="form__button button submit"> SEND</button>
                  </form>
                  <div class="form-message" id="msg" style="color:red;">Invalid email format</div>
            </div>';
        }
        else{

            $query="SELECT * FROM company WHERE Email='$email'";
            $row=mysqli_query($connection,$query);

            if(mysqli_num_rows($row)>0){
                $token=uniqid(md5(time()));
    
                $_SESSION["token"]=$token;
                $_SESSION["tokenTimeStamp"] = time();
                $_SESSION["email"]=$email;
    
                /**
                 * This example shows settings to use when sending via Google's Gmail servers.
                 * The IMAP section shows how to save this message to the 'Sent Mail' folder using IMAP commands.
                 */
                
                //SMTP needs accurate times, and the PHP time zone MUST be set
                //This should be done in your php.ini, but this is how to do it if you don't have access to that
                date_default_timezone_set('Etc/UTC');
                
                require '../../PHPMailer/PHPMailerAutoload.php';
                
                //Create a new PHPMailer instance
                $mail = new PHPMailer;
                
                //Tell PHPMailer to use SMTP
                $mail->isSMTP();
                
                //Enable SMTP debugging
                // 0 = off (for production use)
                // 1 = client messages
                // 2 = client and server messages
                $mail->SMTPDebug = 0;
                
                //Ask for HTML-friendly debug output
                $mail->Debugoutput = 'html';
                
                //Set the hostname of the mail server
                $mail->Host = 'smtp.gmail.com';
                // use
                // $mail->Host = gethostbyname('smtp.gmail.com');
                // if your network does not support SMTP over IPv6
                
                //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
                $mail->Port = 587;
                
                //Set the encryption system to use - ssl (deprecated) or tls
                $mail->SMTPSecure = 'tls';
                
                //Whether to use SMTP authentication
                $mail->SMTPAuth = true;
                
                //Username to use for SMTP authentication - use full email address for gmail
                $mail->Username = "raqeeb.project@gmail.com";
                
                //Password to use for SMTP authentication
                $mail->Password = ""; //due to upload to github password is not there
                
                //Set who the message is to be sent from
                $mail->setFrom('raqeeb.project@gmail.com', 'Raqeeb');
                
                //Set an alternative reply-to address
                $mail->addReplyTo('no-replyto@webscript.info', 'First Last');
                
                //Set who the message is to be sent to
                $mail->addAddress($email, 'John Doe');
                
                //Set the subject line
                $mail->Subject = 'Forgot password';
                
                //Read an HTML message body from an external file, convert referenced images to embedded,
                //convert HTML into a basic plain-text alternative body
                $mail->msgHTML("<!DOCTYPE html> <html> <body>Greetings, <br> You have requested to reclaim your account, if the request was made by you, please click <a href='http://localhost/GP_Raqeeb/WebsitePages/Back-End/PHP/reclaimAccount.php?token=$token&email=$email'>Here</a>. Please note that the reset link will expire in 10 minutes. <br> If you did not make this request, please ignore this email.</body> </html>");
                
                //Replace the plain text body with one created manually
                $mail->AltBody = 'This is a plain-text message body';
                
                //Attach an image file
                //$mail->addAttachment('images/phpmailer_mini.png');
                
                //send the message, check for errors
                if (!$mail->send()) {
                    echo "
                    <a href='../../Back-End/PHP/index.php'><img id='logo' src='../../images/Logo2.png' alt='Company Logo'></a>
                    <a id='arrow' href='../../Front-End/HTML/login.html'><i  class='fa fa-chevron-left' style='color: #003f91; font-size: 30px; float: left; margin-left: 20px; margin-top: 20px;'></i></a>
                    <h2 id='forgotTitle' class='title' style='margin-top: 3%;'>FORGET YOUR PASSWORD?</h2>
                    <br> <br> 
                    <div id='forgotDesc' > 
                        <p style='font-size: 17px;'>Please enter your registered email address and<br> we will send you an email to reset your password</p>
                        <form class='form' id='ForgotPasswordForm'>
                            <br>
                            <input name='femail' class='form__input' id='email' type='email' placeholder='Email' required>
                            <div class='errorMessage' id='EmailNotValid'></div>
                            <button type='submit' class='form__button button submit;> SEND</button>
                          </form>
                          <div class='form-message' id='msg' style='color:red;'> Mailer Error: . $mail->ErrorInfo </div>
                    </div>";
                } else {
                    echo " <a href='../../Back-End/PHP/index.php'> <img id='logo' src='../../images/Logo2.png' alt='Company Logo'></a>
                    <a id='arrow' href='../../Front-End/HTML/login.html'><i  class='fa fa-chevron-left' style='color: #003f91; font-size: 30px; float: left; margin-left: 18px; margin-top: 20px;'></i></a> <br><br><br>

                    <img src='../../images/mail.png' style='height:180px; width:auto;'>  <br>  <h2 class='title' style='margin-top: 3%;'>Email sent!</h2>  <p style='font-size:20px;'> please make sure you open the link on the same browser you made the request from </p>";
                }
                




        
            }else{
                echo "
                <a href='../../Back-End/PHP/index.php'><img id='logo' src='../../images/Logo2.png' alt='Company Logo'></a>
                <a id='arrow' href='../../Front-End/HTML/login.html'><i  class='fa fa-chevron-left' style='color: #003f91; font-size: 30px; float: left; margin-left: 20px; margin-top: 20px;'></i></a>
                <h2 id='forgotTitle' class='title' style='margin-top: 3%;'>FORGET YOUR PASSWORD?</h2>
                <br> <br> 
                <div id='forgotDesc' > 
                    <p style='font-size: 17px;'>Please enter your registered email address and<br> we will send you an email to reset your password</p>
                    <form class='form' id='ForgotPasswordForm'>
                        <br>
                        <input name='femail' class='form__input' id='email' type='email' placeholder='Email' required>
                        <div class='errorMessage' id='EmailNotValid'></div>
                        <button type='submit' class='form__button button submit'> SEND</button>
                      </form>
                      <br>
                      <div class='form-message' id='msg' style='color:red;'> User Not Found </div>
                </div>";
            }

        }


    }

    }




?>