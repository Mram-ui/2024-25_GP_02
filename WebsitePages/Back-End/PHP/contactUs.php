<?php

            session_start();
            
            include("dbConnection.php");
            
            if(isset($_POST['cname']) && isset($_POST['email']) && isset($_POST['phone']) && isset($_POST['msg'])){
            
                $cname=$_POST['cname'];
                $email=$_POST['email'];
                $phone=$_POST['phone'];
                $msg=$_POST['msg'];
    
                /**
                 * This example shows settings to use when sending via Google's Gmail servers.
                 * The IMAP section shows how to save this message to the 'Sent Mail' folder using IMAP commands.
                 */
                
                //SMTP needs accurate times, and the PHP time zone MUST be set
                //This should be done in your php.ini, but this is how to do it if you don't have access to that
                date_default_timezone_set('Asia/Riyadh');
                
                require 'PHPMailer/PHPMailerAutoload.php';
                
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
                $mail->Password = "vjrd xeyj lrlw ymcf"; //due to upload to github password is not there
                
                //Set who the message is to be sent from
                $mail->setFrom('raqeeb.project@gmail.com', 'Raqeeb');
                
                //Set an alternative reply-to address
                $mail->addReplyTo('no-replyto@webscript.info', 'First Last');
                
                //Set who the message is to be sent to
                $mail->addAddress("raqeeb.project@gmail.com", 'Contact us');
                
                //Set the subject line
                $mail->Subject = 'Contact us request';
                
                //Read an HTML message body from an external file, convert referenced images to embedded,
                //convert HTML into a basic plain-text alternative body
                $mail->msgHTML("<!DOCTYPE html> <html> <body> Contact us Request <br> Name:$cname <br> Email:$email <br> Phone:$phone <br> Message:$msg </body> </html>");
                
                //Replace the plain text body with one created manually
                $mail->AltBody = 'This is a plain-text message body';
                
                //Attach an image file
                //$mail->addAttachment('images/phpmailer_mini.png');
                
                //send the message, check for errors
                if (!$mail->send()) {
                    echo "Mailer Error: . $mail->ErrorInfo ";
                }
                 else {
                    echo "<br> We got your message! We will contact you back soon";
                }
            }
            else{
                echo 'Please fill out all the information';
            }
?>
