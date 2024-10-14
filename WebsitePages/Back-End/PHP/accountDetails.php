<!DOCTYPE html>
<html lang="es" dir="ltr">

<head>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../../Front-End/CSS/accountDetails.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <title>Account Details</title>

    <style>
        #logoutModal {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border: 1px solid #ccc;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            z-index: 1000;
        }

        .modal-buttons {
            display: flex;
            justify-content: space-between;
        }

        .modal-buttons button {
            padding: 10px 20px;
            cursor: pointer;
        }

        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 500;
        }
        
 .popup {
        display: none; /* Hidden by default */
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
        z-index: 1000; /* Ensure popup is above everything */
    }

    .popup-content {
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        width: 300px;
        margin: 100px auto;
        text-align: center;
    }

    .logoutC, .cancelC {
        padding: 10px 20px;
        margin: 10px;
        cursor: pointer;
        background: blue;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        letter-spacing: 1.15px;
        background-color: #004aad;
        color: #f9f9f9;
        box-shadow: 8px 8px 16px #d1d9e6, -8px -8px 16px #f9f9f9;
        border: none;
        outline: none;
        align-self: flex-end;
        transition: 0.5s;  
        font-family: 'Montserrat', sans-serif;
    }

    .logoutC {
         background-color: #F94141;
         color: white;
    }

    .cancelC {
        background-color: darkgray;
        color: #f9f9f9;
    }
    
    .logoutC:hover {
         background-color: red;
         color: white;
    }

    .cancelC:hover {
        background-color: gray;
        color: #f9f9f9;
    }
    

    </style>

    <script>
        // Function to show the confirmation modal
        function confirmLogout(event) {
            event.preventDefault(); // Prevent the default action
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('logoutModal').style.display = 'block';
        }

        // Function to handle the actual logout action
        function logoutConfirmed() {
            console.log("Logging out...");
            window.location.href = '../../Back-End/PHP/logout.php'; // Redirect to logout handler
        }

        // Function to close the modal
        function cancelLogout() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('logoutModal').style.display = 'none';
        }
    </script>
</head>

<body>
    <header class="header">
        <div class="logo">
            <a href="../../Back-End/PHP/userHome.php"><img src="../../images/Logo2.png" alt="Company Logo"></a>
        </div>
    </header>

    <?php
    include '../../Back-End/PHP/session.php';

    // Ensure the session is started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Retrieve CompanyID from session
    $CompanyID = $_SESSION['CompanyID'] ?? null;

    if ($CompanyID === null) {
        echo "<script>alert('No company ID found. Please log in again.'); window.location.href='../../Front-End/HTML/login.html';</script>";
        exit();
    }

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

    // Preview profile information
    $query = 'SELECT Email, CompanyName, Logo FROM company WHERE CompanyID=' . $CompanyID;
    $row = mysqli_fetch_assoc(mysqli_query($connection, $query));

    if ($row) {
        $email = $row['Email'];
        $companyName = $row['CompanyName'];
        $logo = $row['Logo'];
    } else {
        echo "<script>alert('Company not found.'); window.location.href='../../Front-End/HTML/login.html';</script>";
        exit();
    }

    // Handle form submission for updating company details
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['Logout'])) {
        if (isset($_FILES['logo'])) {
            // Get form data
            $companyName = $_POST['companyName'];
            $email = $_POST['email'];

            // Handle file upload
            if (file_exists($_FILES['logo']['tmp_name']) && is_uploaded_file($_FILES['logo']['tmp_name'])) {
                $path_parts = pathinfo($_FILES["logo"]["name"]);
                $extension = $path_parts['extension'];
                $filenewname = $companyName . "_" . uniqid() . "." . $extension; // Unique name with company name
                $folder = "../../images/" . $filenewname;

                if (move_uploaded_file($_FILES['logo']['tmp_name'], $folder)) {
                    $logo = $filenewname; // Update the logo variable with the new file name
                } else {
                    $logo = $row['Logo']; // Keep the old logo if upload fails
                }
            }
            // Close the statement
            $stmt->close();
        } 
    }
    ?>

    <div class="main">
        <a id='arrow' href="../../Back-End/PHP/userHome.php"><i class="fa fa-chevron-left" style="color: #003f91; font-size: 30px; justify-self: end;"></i></a>
        <h2 class="title">Profile</h2>
        <button id="EditBtn" onclick="alert('Edit feature is not available yet.');">Edit</button>

        <div class="profileLogo">
            <div class="profile">
                <img src="../../images/<?php echo $logo ?>" alt="Company Logo" class="logo" id="logo">
            </div>
            <h2><?php echo $companyName ?></h2>
        </div>

        <form id='addcam' class="form" method="POST" action="../../Back-End/PHP/accountDetails.php" enctype="multipart/form-data">
            <label for="companyName">Company Name</label>
            <input name="companyName" class="form__input" type="text" value="<?php echo $companyName ?>" required readonly>
            <label for="email">Company Email</label>
            <input name="email" class="form__input" type="email" value="<?php echo $email ?>" required readonly>

            <div class="buttonss">
                <div class="button">
                    <button class="reset" name='submit' id="resetPassword" value='0' onclick="alert('Reset Password feature is not available yet.');">Reset Password</button>
                </div>

                <div class="button">
                    <button class="logout" id="LogoutBtn" onclick="confirmLogout(event)">Logout</button>
                </div>
            </div>
        </form>

        <!-- Logout Confirmation Modal -->
        <div id="overlay"></div>
        <div id="logoutModal">
            <p>Are you sure you want to log out?</p>
            <div class="modal-buttons">
                <button class="logoutC" onclick="logoutConfirmed()">Logout</button>
                <button class="cancelC" onclick="cancelLogout()">Cancel</button>
            </div>
        </div>
    </div>
</body>

</html>
