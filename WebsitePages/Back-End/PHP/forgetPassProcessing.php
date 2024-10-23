<?php

session_start();

include("dbConnection.php");

if(isset($_POST['email'])){

    if (empty($_POST["email"])) {
        echo "Please enter your email to retreive your account";
    } else {
        $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email format";
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
                $mail->msgHTML("<!DOCTYPE html> <html> <body>Greetings, <br> You have requested to reclaim your account, if the request was made by you, please click <a href='http://localhost:8888/GP_Raqeeb/WebsitePages/Back-End/PHP/reclaimAccount.php?token=$token&email=$email'>Here</a>. Please note that the reset link will expire in 10 minutes. <br> If you did not make this request, please ignore this email.</body> </html>");
                
                //Replace the plain text body with one created manually
                $mail->AltBody = 'This is a plain-text message body';
                
                //Attach an image file
                //$mail->addAttachment('images/phpmailer_mini.png');
                
                //send the message, check for errors
                if (!$mail->send()) {
                    echo "Mailer Error: " . $mail->ErrorInfo;
                } else {
                    echo "<img src='../../images/confirmation.png' style='height:250px; width:auto;'> <br> <br> <p style='font-size:20px;'> Email sent! please make sure you open the link on the same browser you requested the email from </p>";
                    //Section 2: IMAP
                    //Uncomment these to save your message in the 'Sent Mail' folder.
                    #if (save_mail($mail)) {
                    #    echo "Message saved!";
                    #}
                }
                
                //Section 2: IMAP
                //IMAP commands requires the PHP IMAP Extension, found at: https://php.net/manual/en/imap.setup.php
                //Function to call which uses the PHP imap_*() functions to save messages: https://php.net/manual/en/book.imap.php
                //You can use imap_getmailboxes($imapStream, '/imap/ssl') to get a list of available folders or labels, this can
                //be useful if you are trying to get this working on a non-Gmail IMAP server.
                function save_mail($mail) {
                    //You can change 'Sent Mail' to any other folder or tag
                    $path = "{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail";
                
                    //Tell your server to open an IMAP connection using the same username and password as you used for SMTP
                    $imapStream = imap_open($path, $mail->Username, $mail->Password);
                
                    $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());
                    imap_close($imapStream);
                
                    return $result;
                }





        
            }else{
                echo "user not found";
            }

        }


    }

    }




?>