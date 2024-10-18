<?php
    include '../../Back-End/PHP/session.php';
    
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "raqeebdb";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $cameraId = isset($_GET['cameraId']) ? intval($_GET['cameraId']) : 0;

    $cameraQuery = $conn->prepare("SELECT CameraName, CameraIPAddress, PortNo, StreamingChannel, CameraUsername, CameraPassword FROM camera WHERE CameraID = ?");
    $cameraQuery->bind_param("i", $cameraId);
    $cameraQuery->execute();
    $cameraResult = $cameraQuery->get_result();

    if ($cameraResult->num_rows > 0) {
        $cameraData = $cameraResult->fetch_assoc();
        $passwordLength = strlen($cameraData['CameraPassword']);
    } else {
        echo "<script>alert('Camera not found');</script>";
        exit; 
    }

    $conn->close();
?>

<html lang="es" dir="ltr">

<head>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <meta charset="utf-8">
    <title><?= htmlspecialchars($cameraData['CameraName']); ?></title>
    <link rel="stylesheet" type="text/css" href="../../Front-End/CSS/boxes.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

    
    
    <style>
        .EditBtn {
            width: 90px;
            height: 40px;
            margin-left: 110%;
            margin-bottom: 1%;
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
        }
 
        
        .EditBtn:hover {
            box-shadow: 6px 6px 10px #d1d9e6, -6px -6px 10px #f9f9f9;
            transform: scale(0.985);
            transition: 0.25s;
            background-color: #013b87;
        }
        
        .DeleteBtn {
            width: 150px;
            height: 40px;
            margin-right: 5%;
            margin-bottom: 5%;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 1.15px;
            background-color: #FFD6D6;
            color: #F94141;
            box-shadow: 8px 8px 16px #d1d9e6, -8px -8px 16px #f9f9f9;
            border: none;
            outline: none;
            align-self: flex-end;
            transition: 0.5s;
        }
 
        
        .DeleteBtn:hover {
            box-shadow: 6px 6px 10px #d1d9e6, -6px -6px 10px #f9f9f9;
            transform: scale(0.985);
            transition: 0.25s;
            background-color: #FFBDBD;
        }
        
        #arrow {
            margin-left: -70%;
        }
        
        #title {
            margin-left: -10%;
        }
        
        
        
    </style>
</head>
<body>

    <header class="header">
    <div class="logo">
        <a href="../../Back-End/PHP/userHome.php"><img src="../../images/Logo2.png" alt="Company Logo"></a>
    </div>
</header>
    
<div id="main" class="main">
    <div class="headerTitle">
        <a id='arrow' href="../../Back-End/PHP/cameras.php"><i  class="fa fa-chevron-left" style="color: #003f91; font-size: 30px; justify-self: end;"></i></a>
        <h2 class="title" id="title">Camera Details</h2>
        <button class="EditBtn" onclick="alert('Edit feature is not available yet.');">Edit</button>
    </div>

    <form id='addcam' class="form" method="POST" action="../../Back-End/PHP/addCamera.php">
        <label id='lable' for="cameraName">Camera Name:</label>
        <input name="cameraName" class="form__input" type="text" value="<?= htmlspecialchars($cameraData['CameraName']); ?>" required readonly>

        <label id='lable' for="cameraIP">Camera IP Address:</label>
        <input name="cameraIP" class="form__input" type="text" value="<?= htmlspecialchars($cameraData['CameraIPAddress']); ?>" required readonly>

        <label id='lable' for="portNo">Port Number:</label>
        <input name="portNo" class="form__input" type="text" value="<?= htmlspecialchars($cameraData['PortNo']); ?>" required readonly>

        <label id='lable' for="stream">Streaming Channel:</label>
        <input name="stream" class="form__input" type="text" value="<?= htmlspecialchars($cameraData['StreamingChannel']); ?>" required readonly>

        <label id='lable' for="cameraUsername">Camera Username:</label>
        <input name="cameraUsername" class="form__input" type="text" value="<?= htmlspecialchars($cameraData['CameraUsername']); ?>" required readonly>

        <label id='lable' for="cameraPassword">Camera Password:</label>
            <input name="cameraPassword" class="form__input" type="text" value="<?= str_repeat('*' . ' ', $passwordLength); ?>" required readonly>
    </form>

    <button class="DeleteBtn" onclick="alert('Delete feature is not available yet.');">Delete Camera</button>
</div>
    <script>
         function enableEditing() {
        const inputs = document.querySelectorAll('.form__input');
        inputs.forEach(input => {
            input.removeAttribute('readonly');
        });
    }
    </script>

</body>
</html>
