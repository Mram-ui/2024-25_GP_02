<?php
    session_start();

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

    if (isset($_POST['semail']) && isset($_POST['spassword'])) {
        $select = "SELECT * FROM company WHERE Email = ?";
        $stmt = $connection->prepare($select);
        $stmt->bind_param("s", $_POST['semail']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($_POST['spassword'], $row['Password'])) {
                $_SESSION["CompanyID"] = $row['CompanyID']; // Use CompanyID here
                header('Location: userHome.php?id=' . urlencode($_SESSION["CompanyID"])); // Pass the user ID in the URL
                exit();
            }
        }

        echo '<script> alert("Email or password incorrect! Please try again."); window.location.href="../../Front-End/HTML/login.html?error=invalid_credentialsLOG"; </script>';
        exit();
    }
?>
