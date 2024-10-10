<?php include '../../Back-End/PHP/session.php'; ?>
<html lang="es" dir="ltr">

<head>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <meta charset="utf-8">
    <title>Add a Camera</title>
    <link rel="stylesheet" type="text/css" href="../../Front-End/CSS/boxes.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

</head>

<body>
    <header class="header">
        <div class="logo">
            <a href="../../Back-End/PHP/userHome.php"><img src="../../images/Logo2.png" alt="Company Logo"></a>
        </div>
    </header>

    <div class="main">
        <a id='arrow' href="../../Back-End/PHP/cameras.php"><i  class="fa fa-chevron-left" style="color: #003f91; font-size: 30px; justify-self: end;"></i></a>
        <h2 class="title" >Add a Camera</h2>
        <form id='addcam' class="form" method="POST" action="../../Back-End/PHP/addCamera.php">
            <label for="cameraName">Camera Name:</label> <input name="cameraName" class="form__input" type="text" placeholder="Camera 1" required>
            <label for="cameraIP">Camera IP Address:</label> <input name="cameraIP" class="form__input" type="text" placeholder="000.000.0.000" required>
            <label for="portNo">Port Number:</label> <input name="portNo" class="form__input" type="text" placeholder="0" required>
            <label for="stream">Streaming Channel:  </label><input name="stream" class="form__input" type="text" placeholder="stream1" required>
            <label for="cameraUsername">Camera Username:</label><input name="cameraUsername" class="form__input" type="text" placeholder="Username" required>
            <label for="cameraPassword">Camera Password:</label><input name="cameraPassword" class="form__input" type="text" placeholder="Password" required>

            <button class="form__button button submit">ADD CAMERA</button>
          </form>


    </div>
</body>

</html>


<?php
    // DB connection
    $servername = "localhost"; 
    $username = "root";
    $password = "";
    $dbname = "raqeebdb";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Assuming CompanyID is stored in the session after user login
    $CompanyID = $_SESSION['CompanyID']; // Change this according to how you store it in the session

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the form data
        $cameraName = $_POST['cameraName'];
        $cameraIP = $_POST['cameraIP'];
        $portNo = $_POST['portNo'];
        $stream = $_POST['stream'];
        $cameraUsername = $_POST['cameraUsername'];
        $cameraPassword = $_POST['cameraPassword'];

        // Hash the camera password

        // Prepare and bind the statement
        $stmt = $conn->prepare("INSERT INTO camera (CameraName, CameraIPAddress, PortNo, StreamingChannel, CameraUsername, CameraPassword, CompanyID) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisssi", $cameraName, $cameraIP, $portNo, $stream, $cameraUsername, $cameraPassword, $CompanyID);

        // Execute the statement
        if ($stmt->execute()) {
            echo "<script>alert('Camera added successfully!'); window.location.href = 'cameras.php';</script>";
        } else {
            echo "<script>alert('Failed to add camera: " . $stmt->error . "'); window.location.href = 'addCameras.php';</script>";
        }

        // Close the statement and connection
        $stmt->close();
        $conn->close();
    }
?>
