<?php
session_start();

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL); //remember to remove 

$connection = mysqli_connect("localhost","root","root","raqeebdb");   
$error = mysqli_connect_error();
if($error != null){
    echo '<p> cant connect to DB';
}             
else{ 
    
    if(isset($_POST['email'])){
       $select = "SELECT * FROM `company` WHERE `Email` = ?"; //fix form here
        $stmt = $connection->prepare($select);
        $stmt->bind_param("s", $_POST['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            echo '<script> alert("this email is already being used, please log in instead"); window.location.href="../../Front-End/HTML/login.html"; </script>'; //edit this
            exit();
        }
    }
        
    if (isset($_POST['cname']) && isset($_POST['email']) && isset($_POST['password']) && isset($_FILES["logo"]) && $_FILES["logo"]["error"] == 0) {
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        //do image
        $temp_name = $_FILES['logo']['tmp_name'];
        $path_parts = pathinfo($_FILES["logo"]["name"]); //  to change file name
        $extension = $path_parts['extension']; //get extension
//        $filenewname=$_POST['email'].".".$extension; // newname.extension (the dot in the email prevent it from being imported, so I changed it to bname and a unique id)
        $filenewname=$_POST['cname']. "_" . uniqid() . "." . $extension; // newname.extension
        $folder = "../../images/".$filenewname; //create path to put image

      if (move_uploaded_file($temp_name, $folder)) {
            //finishes image
        
       
        
        $sql = "INSERT INTO `company` (`Logo`, `CompanyName`, `Email`, `Password`) VALUES (?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssss", $filenewname, $_POST['cname'], $_POST['email'], $pass);
        $stmt->execute();
        $lastInsertedID =$stmt->insert_id;
        $_SESSION["userID"]=$lastInsertedID;
        
        header('Location:userHome.php');
        
        exit();
        
        } else{
        echo "Failed to upload image";} //edit this
        
    }
}
?>
