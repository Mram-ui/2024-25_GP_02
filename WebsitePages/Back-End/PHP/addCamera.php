<?php
    include '../../Back-End/PHP/session.php';
    $servername = "localhost"; 
    $username = "root";
    $password = "root";
    $dbname = "raqeebdb";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $CompanyID = $_SESSION['CompanyID'] ?? null; 
    $message = '';

    if ($CompanyID) {
        $logoQuery = "SELECT Logo FROM company WHERE CompanyID = ?";
        $logoStmt = $conn->prepare($logoQuery);
        $logoStmt->bind_param("i", $CompanyID);
        $logoStmt->execute();
        $logoResult = $logoStmt->get_result();

        if ($logoResult->num_rows > 0) {
            $logoRow = $logoResult->fetch_assoc();
            $logo = $logoRow['Logo'];
            echo "Logo from DB: " . $logo;
        } else {
            $logo = null;
        }
    } else {
        $logo = null;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $cameraName = $_POST['cameraName'];
        $cameraIP = $_POST['cameraIP'];
        $portNo = $_POST['portNo'];
        $stream = $_POST['stream'];
        $cameraUsername = $_POST['cameraUsername'];
        $cameraPassword = $_POST['cameraPassword'];

        $checkCamera = $conn->prepare("SELECT * FROM camera WHERE CameraName = ? AND CompanyID = ?");
        $checkCamera->bind_param("si", $cameraName, $CompanyID);
        $checkCamera->execute();
        $result = $checkCamera->get_result();

        if ($result->num_rows > 0) {
            $message = 'The camera name is already in your list. Please choose a different name.';
        } else {
            $stmt = $conn->prepare("INSERT INTO camera (CameraName, CameraIPAddress, PortNo, StreamingChannel, CameraUsername, CameraPassword, CompanyID) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisssi", $cameraName, $cameraIP, $portNo, $stream, $cameraUsername, $cameraPassword, $CompanyID);

            if ($stmt->execute()) {
                $message = 'Camera added successfully!';
                echo "<script>
                        localStorage.removeItem('cameraFormData');
                        window.location.href='../../Back-End/PHP/cameras.php'; 
                      </script>";
            } else {
                $message = 'Failed to add camera: ' . $stmt->error;
            }

            $stmt->close();
        }

        $checkCamera->close();
        $conn->close();
    }
?>

<html lang="es" dir="ltr">
    <head>
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
        <meta charset="utf-8">
        <title>Add a Camera</title>
        <link rel="stylesheet" type="text/css" href="../../Front-End/CSS/boxes.css">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
        <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
        <style>
            .header {
                position: fixed;
                z-index: 200;
                display: flex;
                justify-content: space-between;
                align-items: center;
                background-color: white;
                padding: 13px 40px;
                padding-bottom: 14px;
                width: 100%;
                font-weight: bold;  
            }
          
            .header .logo img {
                height: 60px; 
                width: auto;  
            }
            
            #main {
                width: -20%;
                padding: 0%;
                margin-left: 20%;
                margin-right: 20%;
                padding-bottom: 2%;
            }
            
             .popup input {
                display: none;
            }
            
            
           .headerlinks li {
                text-align: left;
                margin-left: -130%;
                list-style: none;
                display: inline-block;
                padding: 8px 12px;
                position: relative;
                color: black;
                font-weight: lighter;
                font-size: 110%;
            }

            .headerlinks li a {
                color: #504f4f;
                text-decoration: none;
            }

            .headerlinks li::after {
                content: "";
                width: 0;
                height: 2px;
                background: #4a56ff;
                display: block;
                margin: auto;
                left: 0;
                bottom: -10px;
                transition: 0.2s;
            }

            .headerlinks li:hover::after {
                width: 100%;
            }

            .headerlinks li a:hover {
                color: #4a56ff;
            }

            .headerlinks li.active a {
                color: #3B5998;
            }

            .headerlinks li.active::after {
                width: 100%;
            }
            
            body {
                background-color: #e9edf3; 
            }
            
            .main {
                background-color: #eaeef2;
                margin-top: 10%;
            }
            
            .headerTitle {
                margin-top: 5%;
            }
            
            .error-message {
                color: red;
                font-size: 0.9em;
            }
        </style>
    </head>

    <body>
        <header class="header">
            <div class="logo">
                <a href="../../Back-End/PHP/userHome.php"><img src="../../images/Logo2.png" alt="Company Logo"></a>
            </div>
            <ul class="headerlinks">
                <li><a href="../../Back-End/PHP/cameras.php">Cameras</a></li>
                <li><a href="../../Back-End/PHP/userHome.php">Events</a></li>
            </ul>
            <script>
                const currentPath = window.location.pathname;

                const menuItems = document.querySelectorAll('ul li');

                menuItems.forEach((item) => {
                    const link = item.querySelector('a');

                    if (link.href.includes(currentPath)) {
                        item.classList.add('active');
                    }
                });
            </script>
            <nav>
                <label class="popup">
                    <input type="checkbox" />
                    <a href="../../Back-End/PHP/accountDetails.php">
                        <?php if (is_null($logo) || empty($logo)): ?>
                            <img src="../../images/CLogo.png" style="width: 60px; height: 60px; border-radius: 50%;" alt="Default User Logo">
                        <?php else: ?>
                            <img src="../../images/<?php echo htmlspecialchars($logo); ?>" style="width: 60px; height: 60px; border-radius: 50%;" alt="User Company Logo">
                        <?php endif; ?>
                    </a>
                </label>
            </nav>
        </header>
        <div class="main">
            <a id='arrow' href="../../Back-End/PHP/cameras.php"><i class="fa fa-chevron-left" style="color: #003f91; font-size: 30px; justify-self: end;"></i></a>
            <h2 class="title">Add a Camera</h2>

            <form id='addcam' class="form" method="POST" action="" onsubmit="return validateForm();">
                <label for="cameraName">Camera Name:</label>
                <input name="cameraName" id="cameraName" class="form__input" type="text" placeholder="Camera 1" required>
                <div class="error-message" id="cameraNameError"><?php echo isset($message) ? ($message === 'The camera name is already in your list. Please choose a different name.' ? $message : '') : ''; ?></div>

                <label for="cameraIP">Camera IP Address:</label>
                <input name="cameraIP" id="cameraIP" class="form__input" type="text" placeholder="000.000.0.000" required>
                <div class="error-message" id="IPError"></div>

                <label for="portNo">Port Number:</label>
                <input name="portNo" id="portNo" class="form__input" type="number" placeholder="0" min="1" required>

                <label for="stream">Streaming Channel: </label>
                <input name="stream" id="stream" class="form__input" type="text" placeholder="stream1" required>

                <label for="cameraUsername">Camera Username:</label>
                <input name="cameraUsername" id="cameraUsername" class="form__input" type="text" placeholder="Username" required>

                <label for="cameraPassword">Camera Password:</label>
                <input name="cameraPassword" id="cameraPassword" class="form__input" type="password" placeholder="Password" required>

                <button class="form__button button submit">ADD CAMERA</button>
            </form>
        </div>

        <script>
            let ipValid = false;
            let ipErrorShown = false;

            function saveFormData() {
                const formData = {
                    cameraName: document.getElementById('cameraName').value,
                    cameraIP: document.getElementById('cameraIP').value,
                    portNo: document.getElementById('portNo').value,
                    stream: document.getElementById('stream').value,
                    cameraUsername: document.getElementById('cameraUsername').value,
                    cameraPassword: document.getElementById('cameraPassword').value
                };
                localStorage.setItem('cameraFormData', JSON.stringify(formData));
            }

            function loadFormData() {
                const savedData = localStorage.getItem('cameraFormData');
                if (savedData) {
                    const formData = JSON.parse(savedData);
                    document.getElementById('cameraName').value = formData.cameraName || '';
                    document.getElementById('cameraIP').value = formData.cameraIP || '';
                    document.getElementById('portNo').value = formData.portNo || '';
                    document.getElementById('stream').value = formData.stream || '';
                    document.getElementById('cameraUsername').value = formData.cameraUsername || '';
                    document.getElementById('cameraPassword').value = formData.cameraPassword || '';
                }
            }

            document.querySelectorAll('.form__input').forEach(input => {
                input.addEventListener('input', saveFormData);
            });

            window.onload = function () {
                loadFormData();
            };

            document.getElementById('cameraName').addEventListener('input', function () {
                document.getElementById('cameraNameError').innerText = '';
            });

            const cameraIPInput = document.getElementById('cameraIP');

            cameraIPInput.addEventListener('blur', function () {
                if (cameraIPInput.value.trim() !== '') {
                    validateIP();
                }
            });

            cameraIPInput.addEventListener('input', function () {
                if (ipErrorShown) {
                    validateIP();
                }
            });

            function validateIP() {
                const ipAddress = cameraIPInput.value.trim();
                const ipPattern = /^(?:\d{1,3}\.){3}\d{1,3}$/;

                if (!ipPattern.test(ipAddress)) {
                    ipValid = false;
                    ipErrorShown = true;
                    document.getElementById('IPError').innerText = "Please enter a valid IP address (e.g., 000.000.0.000).";
                } else {
                    ipValid = true;
                    ipErrorShown = false;
                    document.getElementById('IPError').innerText = "";
                }
            }

            function validateForm() {
                validateIP();

                if (!ipValid) {
                    return false;
                }

                return true;
            }
        </script>

    </body>
</html>
