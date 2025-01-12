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
    
    $companyID = $_SESSION['CompanyID'];

    $logoQuery = "SELECT Logo FROM company WHERE CompanyID = ?";
    $logoStmt = $conn->prepare($logoQuery);
    $logoStmt->bind_param("i", $companyID);
    $logoStmt->execute();
    $logoResult = $logoStmt->get_result();
    $logoRow = $logoResult->fetch_assoc();
    $logo = $logoRow['Logo'];

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
                margin-top: 8%;
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
            }
            
            .headerTitle {
                margin-top: 5%;
            }

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
                margin-top: 3%;
                margin-bottom: 0%;
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
            
            .button {
                position: relative;
                border-radius: 6px;
                width: 150px;
                height: 40px;
                cursor: pointer;
                display: flex;
                align-items: center;
                border: 1px solid #cc0000;
                background-color: #e50000;
                overflow: hidden;
                margin-right: 5%;
              }

              .button,
              .button__icon,
              .button__text {
                transition: all 0.3s;
              }

              .button .button__text {
                transform: translateX(35px);
                color: #fff;
                font-weight: 600;
              }

              .button .button__icon {
                position: absolute;
                transform: translateX(109px);
                height: 100%;
                width: 39px;
                background-color: #cc0000;
                display: flex;
                align-items: center;
                justify-content: center;
              }

              .button .svg {
                width: 20px;
              }

              .button:hover {
                background: #cc0000;
              }

              .button:hover .button__text {
                color: transparent;
              }

              .button:hover .button__icon {
                width: 148px;
                transform: translateX(0);
              }

              .button:active .button__icon {
                background-color: #b20000;
              }

              .button:active {
                border: 1px solid #b20000;
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
                            <img src="../../images/<?php echo $logo ?>" style="width: 60px; height: 60px; border-radius: 50%;" alt="User Company Logo">
                        <?php endif; ?>
                    </a>
                </label>
            </nav>
        </header>
        
        <div id="main" class="main">
            <div class="headerTitle">
                <a id='arrow' href="../../Back-End/PHP/cameras.php"><i  class="fa fa-chevron-left" style="color: #003f91; font-size: 30px; justify-self: end;"></i></a>
                <h2 class="title" id="title">Camera Details</h2>
                <button class="button" type="button" 
                      style="position: relative; border-radius: 6px; width: 150px; height: 40px; cursor: pointer; display: flex; align-items: center; border: 1px solid #007bff; background-color: #007bff; overflow: hidden; transition: all 0.3s; margin-left: 108%;"
                      onmouseover="this.style.backgroundColor='#0056b3'; this.querySelector('.button__text').style.color = 'transparent'; this.querySelector('.button__icon').style.width = '148px'; this.querySelector('.button__icon').style.transform = 'translateX(0)';"
                      onmouseout="this.style.backgroundColor='#007bff'; this.querySelector('.button__text').style.color = '#fff'; this.querySelector('.button__icon').style.width = '39px'; this.querySelector('.button__icon').style.transform = 'translateX(109px)';"
                      onmousedown="this.style.border = '1px solid #004085'; this.querySelector('.button__icon').style.backgroundColor = '#004085';"
                      onmouseup="this.style.border = '1px solid #007bff'; this.querySelector('.button__icon').style.backgroundColor = '#0056b3';">

                      <span class="button__icon" style="position: absolute; left: 0; height: 100%; width: 39px; background-color: #0056b3; display: flex; align-items: center; justify-content: center; transition: width 0.3s, transform 0.3s;">
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                        </svg>
                      </span>
                      <span class="button__text" style="transform: translateX(35px); color: #fff; font-weight: 600; transition: color 0.3s; margin-left: 10px; ">Edit</span>
                </button>
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

                <button class="button" type="button">
                    <span class="button__text">Delete</span>
                    <span class="button__icon"
                      ><svg
                        class="svg"
                        height="512"
                        viewBox="0 0 512 512"
                        width="512"
                        xmlns="http://www.w3.org/2000/svg"
                      >
                        <title></title>
                        <path
                          d="M112,112l20,320c.95,18.49,14.4,32,32,32H348c17.67,0,30.87-13.51,32-32l20-320"
                          style="fill:none;stroke:#fff;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"
                        ></path>
                        <line
                          style="stroke:#fff;stroke-linecap:round;stroke-miterlimit:10;stroke-width:32px"
                          x1="80"
                          x2="432"
                          y1="112"
                          y2="112"
                        ></line>
                        <path
                          d="M192,112V72h0a23.93,23.93,0,0,1,24-24h80a23.93,23.93,0,0,1,24,24h0v40"
                          style="fill:none;stroke:#fff;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"
                        ></path>
                        <line
                          style="fill:none;stroke:#fff;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"
                          x1="256"
                          x2="256"
                          y1="176"
                          y2="400"
                        ></line>
                        <line
                          style="fill:none;stroke:#fff;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"
                          x1="184"
                          x2="192"
                          y1="176"
                          y2="400"
                        ></line>
                        <line
                          style="fill:none;stroke:#fff;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"
                          x1="328"
                          x2="320"
                          y1="176"
                          y2="400"
                        ></line></svg
                    ></span>
                </button>
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
