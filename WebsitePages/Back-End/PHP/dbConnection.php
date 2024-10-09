<?php 

$connection = mysqli_connect("localhost","root","root","raqeebdb");   
$error = mysqli_connect_error();
if($error != null){
    echo '<p> cant connect to DB';
    exit();
}             


?>
