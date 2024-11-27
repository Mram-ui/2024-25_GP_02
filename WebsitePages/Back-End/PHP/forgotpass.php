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
            
            .form-message {
                margin-top: 1%;
            }
            
            .form__button {
                margin-top: 2%;
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
                    <div class="form-message" id='msg'></div>
                    <button type='submit' class="form__button button submit">SEND</button>
                </form>
            </div>
        </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#ForgotPasswordForm").on('submit', function(e) {
                e.preventDefault();
                var email = $("#email").val();
                
                $.ajax({
                    type: 'POST',
                    url: "forgetPassProcessing.php",
                    data: {email: email},
                    success: function(response) {
                        $(".form-message").css("display", "block");
                        $("#msg").html(response);
                    },
                    error: function() {
                        $(".form-message").css("display", "block");
                        $("#msg").html("An error occurred. Please try again.");
                    }
                });
            });
        });
    </script>
    </body>
</html>
