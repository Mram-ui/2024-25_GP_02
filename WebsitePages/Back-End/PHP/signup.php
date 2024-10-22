<?php
    session_start();

    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

    // DB connection
    $servername = "localhost"; 
    $username = "root";
    $password = "";
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
            echo '<script> alert("This email is already being used! Please log in instead.");
                   window.location.href="../../Front-End/HTML/login.html?error=invalid_credentialsUP";</script>';            
            exit();
        }
    }

    if (isset($_POST['cname']) && isset($_POST['email']) && isset($_POST['password'])) {
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $filenewname = null;

        if (isset($_FILES["logo"]) && $_FILES["logo"]["error"] == 0) {
            $temp_name = $_FILES['logo']['tmp_name'];
            $path_parts = pathinfo($_FILES["logo"]["name"]);
            $extension = $path_parts['extension'];
            $filenewname = $_POST['cname'] . "_" . uniqid() . "." . $extension;
            $folder = "../../images/" . $filenewname;

            if (!move_uploaded_file($temp_name, $folder)) {
                echo "Failed to upload image";
                exit();
            }
        }

        // Insert new company record into the database
        $sql = "INSERT INTO company (Logo, CompanyName, Email, Password) VALUES (?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssss", $filenewname, $_POST['cname'], $_POST['email'], $pass);
        $stmt->execute();
        $_SESSION["CompanyID"] = $stmt->insert_id;

        header('Location: ../../Back-End/PHP/userHome.php?id=' . urlencode($_SESSION["CompanyID"]));
        exit();
    }

    $connection->close();
?>
