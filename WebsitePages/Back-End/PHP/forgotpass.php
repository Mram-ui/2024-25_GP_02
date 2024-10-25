<!DOCTYPE html>
<html lang="es" dir="ltr">
    <head>
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
        <meta charset="utf-8">
        <title>Forgot password</title>
        <link rel="stylesheet" type="text/css" href="../../Front-End/CSS/login.css">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
        <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <style>
            
            #EmailNotValid {
                color: red;
                font-size: 0.9em;
                margin-right: 12%;
                margin-top: 1%;
            } 
            
            .form__input {
                margin-top: 3%;
                margin-bottom: 0%;
            }
            
            .form__button button submit {
                margin-top: -20%;
                margin-bottom: 0%;
            }
            
            #forgetpass {
                position: relative;
                width: 10%;    
                height: 2%;
                padding: 25px;
                background-color: #ecf0f3;
                box-shadow: 10px 10px 10px #d1d9e6, -10px -10px 10px #f9f9f9;
                border-radius: 12px;
                overflow: hidden;
                align-self: center;
            }
            
            #forgotDesc{
                margin: 0%;
                padding: 0
            }
            
            #logo {
                width: 30%;
                margin-top: 5%;
            }
                
        </style>
    </head>

    <body>
        <div class="main" style="margin-top: 8%;" id="forgetpass">
            <a href="../../Back-End/PHP/index.php"><img id="logo" src="../../images/Logo2.png" alt="Company Logo"></a>
            <a id='arrow' href="../../Front-End/HTML/login.html"><i  class="fa fa-chevron-left" style="color: #003f91; font-size: 30px; float: left; margin-left: 20px; margin-top: 20px;"></i></a>
            <h2 id="forgotTitle" class="title" style="margin-top: 3%;">FORGET YOUR PASSWORD?</h2>
            <br> <br> 
            <div id="forgotDesc" > 
                <p style="font-size: 17px;">Please enter your registered email address and<br> we will send you an email to reset your password</p>
                <form class="form" id="ForgotPasswordForm">
                    <br>
                    <input name="femail" class="form__input" id="email" type="email" placeholder="Email" required>
                    <div class="errorMessage" id="EmailNotValid"></div>
                    <button type='submit' class="form__button button submit"> SEND</button>
                  </form>
                  <div class="form-message" id='msg'></div>
            </div>
        </div>
<!--         <script type="text/javascript">
            $(document).ready(function() {
                const emailInput = document.getElementById("email");

                // Email validation on blur (when input loses focus)
                emailInput.addEventListener('blur', function() {
                    const emailValue = emailInput.value;
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                    // Validate email format
                    if (!emailPattern.test(emailValue)) {
                        document.getElementById('EmailNotValid').innerText = 'Please enter a valid email address!';
                    } else {
                        document.getElementById('EmailNotValid').innerText = '';

                        // AJAX request to check if email exists
                        $.ajax({
                            type: 'POST',
                            url: "checkEmailExistence.php", // This will check if the email exists in the DB
                            data: { email: emailValue },
                            success: function(response) {
                                if (response.trim() === "email_not_found") {
                                    document.getElementById('EmailNotValid').innerText = 'Email does not exist!';
                                } else {
                                    document.getElementById('EmailNotValid').innerText = '';
                                }
                            }
                        });
                    }
                });

                // Email validation on input (while typing)
                emailInput.addEventListener('input', function() {
                    const emailValue = emailInput.value;
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                    if (emailPattern.test(emailValue)) {
                        document.getElementById('EmailNotValid').innerText = '';
                    }
                });

                // Form submission
                $("#ForgotPasswordForm").on('submit', function(e) {
                    e.preventDefault();
                    const email = $("#email").val();
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                    // Check email format before proceeding
                    if (!emailPattern.test(email)) {
                        $("#EmailNotValid").text("Please enter a valid email address!");
                        return; // Stop form submission if email is invalid
                    }

                    // Proceed with password reset if email is valid
                    $.ajax({
                        type: 'POST',
                        url: "forgetPassProcessing.php", // This is your existing processing PHP file
                        data: { email: email },
                        success: function(data) {
                            if (data.trim() === "email_sent") {
                                $("#forgetpass").html("<p>Password reset instructions have been sent to your email.</p>");
                            } else {
                                $("#EmailNotValid").text("An error occurred. Please try again.");
                            }
                        },
                        error: function() {
                            $("#EmailNotValid").text("An error occurred while processing your request.");
                        }
                    });
                });
            });
        </script>-->
        <script type="text/javascript">
         $(document).ready(function(){
            $("#ForgotPasswordForm").on('submit', function(e){
            e.preventDefault();
            var email = $("#email").val();
            $.ajax({

                type:'POST',
                url:"forgetPassProcessing.php",
                data:{email:email},

                success:function(data){
                    $(".form-message").css("display","block");
                    $("#forgetpass").html(data);


                }
            })
         })
        })
    </script>
    </body>
</html>
