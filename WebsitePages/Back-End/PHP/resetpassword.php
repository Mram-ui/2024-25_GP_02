<?php
include("dbConnection.php");
session_start();


if(!isset($_POST['oldpass'])){
$email=$_POST['email'];
$pass = password_hash($_POST['newpass'], PASSWORD_DEFAULT);

$query="UPDATE company SET Password='$pass' WHERE Email='$email'";
$res=mysqli_query($connection,$query);
if($res){
    //session_destroy();
    echo "Password updated successfully, please <a href='../../Front-End/HTML/login.html'>Log in</a>";
}
else{
     echo "could not update password, please try again";
}
}

else{
    $CompanyID = $_SESSION['CompanyID'] ?? null;
    $query = 'SELECT * FROM company WHERE CompanyID=' . $CompanyID;
    $row = mysqli_fetch_assoc(mysqli_query($connection, $query));


        if ($row) {
            if (password_verify($_POST['oldpass'], $row['Password'])) {
                $pass = password_hash($_POST['newpass'], PASSWORD_DEFAULT);
                $query="UPDATE company SET Password='$pass' WHERE CompanyID=" . $CompanyID;
                $res=mysqli_query($connection,$query);
                if($res){
                    echo '1';

                    }

                else{
                        echo '2';
                    }
                }
            else{
                echo '3';

            }
        }
        else{
            echo '2';
        }
    }

