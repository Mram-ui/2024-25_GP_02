<?php
include("dbConnection.php");
session_start();

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
