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


</head>

<body>
    <header class="header">
        <div class="logo">
            <a href="../../Back-End/PHP/index.php"><img src="../../images/Logo2.png" alt="Company Logo"></a>
        </div>
    </header>

    <div class="main" style="margin: 2%;" id="forgetpass">
        <a id='arrow' href="../../Front-End/HTML/login.html"><i  class="fa fa-chevron-left" style="color: #003f91; font-size: 30px; float: left; margin-left: 10px; margin-top: 10px;"></i></a>
        <h2 id="forgotTitle" class="title" style="margin-top: 8%;">Forgot your password?</h2>
        <br> <br> 
        <div id="forgotDesc" > 
            <p style="font-size: 17px;">Please enter your registered email address and we will send you an email to reset your password</p>
        <form class="form" id="ForgotPasswordForm">
            <br>
            <input name="femail" class="form__input" id="email" type="email" placeholder="Email" required>
            <button type='submit' class="form__button button submit"> SEND</button>
          </form>

          <div class="form-message" id='msg'></div>

        </div>

    </div>
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
