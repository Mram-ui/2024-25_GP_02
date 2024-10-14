<?php
session_start();

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL); // Remember to remove in production

// DB connection
$servername = "localhost"; 
$username = "root";
$password = "root";
$dbname = "raqeebdb";
  
// Create connection
$connection = new mysqli($servername, $username, $password, $dbname);
                        
// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if (isset($_POST['email'])) {
    $select = "SELECT * FROM company WHERE Email = ?";
    $stmt = $connection->prepare($select);
    $stmt->bind_param("s", $_POST['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo '<script> alert("This email is already being used, please log in instead"); window.location.href="../../Front-End/HTML/login.html"; </script>';
        exit();
    }
}

if (isset($_POST['cname']) && isset($_POST['email']) && isset($_POST['password']) && isset($_FILES["logo"]) && $_FILES["logo"]["error"] == 0) {
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Handle image upload
    $temp_name = $_FILES['logo']['tmp_name'];
    $path_parts = pathinfo($_FILES["logo"]["name"]);
    $extension = $path_parts['extension'];
    $filenewname = $_POST['cname'] . "_" . uniqid() . "." . $extension;
    $folder = "../../images/" . $filenewname;

    if (move_uploaded_file($temp_name, $folder)) {
        // Insert new company record into the database
        $sql = "INSERT INTO company (Logo, CompanyName, Email, Password) VALUES (?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssss", $filenewname, $_POST['cname'], $_POST['email'], $pass);
        $stmt->execute();
        $_SESSION["CompanyID"] = $stmt->insert_id; // Save Company ID to session
        
        header('Location: ../../Back-End/PHP/userHome.php?id=' . urlencode($_SESSION["CompanyID"])); // Redirect with Company ID
        exit();
    } else {
        echo "Failed to upload image"; // Handle image upload error
    }
}

$connection->close(); // Close the connection at the end
?>
