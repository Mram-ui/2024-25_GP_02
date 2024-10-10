<?php
session_start();

$connection = mysqli_connect("localhost","root","root","raqeebdb");   
$error = mysqli_connect_error();
if($error != null){
    echo '<p> cant connect to DB';
}             
else{ 
    if(isset($_POST['semail']) && isset($_POST['spassword'])){
        $select = "SELECT * FROM `company` WHERE `Email` = ?";
        $stmt = $connection->prepare($select);
        $stmt->bind_param("s", $_POST['semail']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
                if(password_verify($_POST['spassword'], $row['Password'])){
                    $_SESSION["userID"]=$row['id'];
                    header('Location:userHome.php');
                    exit();
                }
            }
        
        echo '<script> alert("email or password incorrect, please try again"); window.location.href="../../Front-End/HTML/login.html"; </script>';
        exit();
        
    }
}