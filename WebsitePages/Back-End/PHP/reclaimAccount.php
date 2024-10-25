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

    <div class="main" style="margin: 2%;">
        <h2 id="forgotTitle" class="title" style="margin-top: 8%;">Reset password</h2>
        <br> <br> 

        <div id="reclaimMsg" >
                <?php 
                session_start();


    if(!isset($_SESSION["token"]) && !isset($_SESSION["tokenTimeStamp"])){
        echo "<h3> You do not have access to change password (no sessions)</h3>";

    }
    else{

    if(isset($_SESSION["token"]) && (time()-$_SESSION["tokenTimeStamp"] >600)){

        echo "<h3> Password reset timedout, <a href='forgotpass.php'> please try again </a> ";
    }
    else{

        if($_SESSION["token"]==$_GET['token']){
            echo '<p style="font-size: 17px;">Please enter a new password and re-enter it to confirm and change your password </p>
            <br>
        <form class="form" id="resetPasswordForm">

            <input name="newpass" id="newpass" class="form__input"  type="password" placeholder="New Password" required>
                      <div class="errorMessage" id="newPassNotValid"></div>
            <input name="confirmpass" id="confirmpass" class="form__input"  type="password" placeholder="Confirm Password" required>

            
            <button type="submit" class="form__button button submit"> SEND</button>
            <br>
            <div id="passerr"></div>
          </form>';
        }
        else{
            echo "<h3> You do not have access to change password (not equal)</h3>";
        }



    }
}

    
    
    ?>

        <script type="text/javascript">
            const queryString = window.location.search;
            console.log(queryString);
            const urlParams = new URLSearchParams(queryString);
            var email = urlParams.get('email')


            const passwordInput = document.getElementById('newpass');

        passwordInput.addEventListener('input', function() {
            const passwordValue = passwordInput.value;
            if (passwordValue.length < 8) {
                document.getElementById('newPassNotValid').innerText = 'Password must be at least 8 characters long!';
            } else {
                document.getElementById('newPassNotValid').innerText = '';
            }
        });



         $(document).ready(function(){
            $("#resetPasswordForm").on('submit', function(e){
            e.preventDefault();
            var newpass = $("#newpass").val();
            var confirmpass=$("#confirmpass").val();
            if(newpass!=confirmpass){
                document.getElementById('passerr').innerText = 'Passwords do not match! please try again';
            }
            else{
                $.ajax({

                type:'POST',
                url:"resetpassword.php",
                data:{email:email,
                    newpass:newpass},

                success:function(data){
                    $("#reclaimMsg").html(data);
                }



                })


            }

         })
        })
    </script>

            
    
    </div>
          

</body>






</html>