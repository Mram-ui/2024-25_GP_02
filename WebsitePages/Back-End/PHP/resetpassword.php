<?php
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "raqeebdb";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if (!isset($_POST['oldpass'])) {
        $email = $_POST['email'];
        $pass = password_hash($_POST['newpass'], PASSWORD_DEFAULT);

        $query = "UPDATE company SET Password='$pass' WHERE Email='$email'";
        $res = mysqli_query($conn, $query);

        if ($res) {
            echo "Password updated successfully, please <a href='../../Front-End/HTML/login.html'>Log in</a>";
        } else {
            echo "Could not update password, please try again.";
        }
    } else {
        session_start(); 
        $CompanyID = $_SESSION['CompanyID'] ?? null;

        if ($CompanyID) {
            $query = "SELECT * FROM company WHERE CompanyID=" . $CompanyID;
            $row = mysqli_fetch_assoc(mysqli_query($conn, $query));

            if ($row) {
                if (password_verify($_POST['oldpass'], $row['Password'])) {
                    $newPass = password_hash($_POST['newpass'], PASSWORD_DEFAULT);
                    $query = "UPDATE company SET Password='$newPass' WHERE CompanyID=" . $CompanyID;
                    $res = mysqli_query($conn, $query);

                    if ($res) {
                        echo '1'; // Password changed successfully
                    } else {
                        echo '2'; // Error in updating password
                    }
                } else {
                    echo '3'; // Incorrect old password
                }
            } else {
                echo '2'; // Error: No such user found
            }
        } else {
            echo '2'; // Error: No session found
        }
    }
?>
