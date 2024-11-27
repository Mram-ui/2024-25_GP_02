<?php
include("dbConnection.php");
session_start();

$hallID=$_POST['hallID'];

$sql = "SELECT HallThreshold 
FROM hall 
WHERE HallID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hallID); 
$stmt->execute();
$result = $stmt->get_result();
$res=mysqli_query($connection,$sql);
if($res){
    echo $res;
}
else{
     echo "-1";
}