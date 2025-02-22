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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

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
              
              #cancelButton:hover {
                  background-color: #e0e0e0;
                  transition: 0.25s;
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
                <button id="editButton" onclick="enableEditing()" class="button" type="button submit" 
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
                    <span id="edit" class="button__text" style="transform: translateX(35px); color: #fff; font-weight: 600; transition: color 0.3s; margin-left: 5px;">Edit</span>
                </button>
                
                <button onclick="saveChanges()" id="saveButton" class="button submit" type="submit" 
                      style="position: relative; border-radius: 6px; width: 150px; height: 40px; cursor: pointer; display: flex; align-items: center; border: 1px solid #2e8b57; background-color: #2e8b57; overflow: hidden; transition: all 0.3s;  margin-left: 108%; display: none;"
                      onmouseover="this.style.backgroundColor='#226740'; this.querySelector('.button__text').style.color = 'transparent'; this.querySelector('.button__icon').style.width = '148px'; this.querySelector('.button__icon').style.transform = 'translateX(0)';"
                      onmouseout="this.style.backgroundColor='#2e8b57'; this.querySelector('.button__text').style.color = '#fff'; this.querySelector('.button__icon').style.width = '39px'; this.querySelector('.button__icon').style.transform = 'translateX(109px)';"
                      onmousedown="this.style.border = '1px solid #194f31'; this.querySelector('.button__icon').style.backgroundColor = '#194f31';"
                      onmouseup="this.style.border = '1px solid #2e8b57'; this.querySelector('.button__icon').style.backgroundColor = '#226740'; ">
                      <span class="button__icon" style="position: absolute; left: 0; height: 100%; width: 39px; background-color: #226740; display: flex; align-items: center; justify-content: center; transition: width 0.3s, transform 0.3s;">
                       <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 15v2a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-2m-8 1V4m0 12-4-4m4 4 4-4"/>
                       </svg>
                      </span>
                    <span id="edit" class="button__text" style="transform: translateX(35px); color: #fff; font-weight: 600; transition: color 0.3s; margin-left: 5px;">Save</span>
                </button>
            </div>
            
            <div class="error-message" id="emptyFilelds"></div>
            <form id='cameraForm' class="form" method="POST" action="" onsubmit="return validateForm();">
                <label id='lable' for="cameraName">Camera Name:</label>
                <input name="cameraName" id="cameraName" class="form__input" type="text" value="<?= htmlspecialchars($cameraData['CameraName']); ?>" required readonly>
                <div class="error-message" id="cameraNameError"><?php echo isset($message) ? ($message === 'The camera name is already in your list. Please choose a different name.' ? $message : '') : ''; ?></div>

                <label id='lable' for="cameraIP">Camera IP Address:</label>
                <input name="cameraIP" id="cameraIP" class="form__input" type="text" value="<?= htmlspecialchars($cameraData['CameraIPAddress']); ?>" required readonly>
                <div class="error-message" id="IPError"></div>

                <label id='lable' for="portNo">Port Number:</label>
                <input name="portNo" class="form__input" type="text" value="<?= htmlspecialchars($cameraData['PortNo']); ?>" min="1" required readonly>
                <div class="error-message" id="portError"></div>

                <label id='lable' for="stream">Streaming Channel:</label>
                <input name="stream" class="form__input" type="text" value="<?= htmlspecialchars($cameraData['StreamingChannel']); ?>" required readonly>

                <label id='lable' for="cameraUsername">Camera Username:</label>
                <input name="cameraUsername" class="form__input" type="text" value="<?= htmlspecialchars($cameraData['CameraUsername']); ?>" required readonly>

                <label id='lable' for="cameraPassword">Camera Password:</label>
                <input id="cameraPassword" name="cameraPassword" class="form__input" type="password" value="<?= htmlspecialchars($cameraData['CameraPassword']); ?>" required readonly>
            </form>

                <button id="deleteTrigger" class="button" type="button">
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
                <button id="cancel" class="button" type="button" 
                      style="position: relative; border-radius: 6px; width: 150px; height: 40px; cursor: pointer; display: none; align-items: center; border: 1px solid #cc0000; background-color: #e50000; overflow: hidden; transition: all 0.3s; margin-left: 108%;"
                      onmouseover="this.style.background='#cc0000'; this.querySelector('.button__text').style.color = 'transparent'; this.querySelector('.button__icon').style.width = '148px'; this.querySelector('.button__icon').style.transform = 'translateX(0)';"
                      onmouseout="this.style.background='#e50000'; this.querySelector('.button__text').style.color = '#fff'; this.querySelector('.button__icon').style.width = '39px'; this.querySelector('.button__icon').style.transform = 'translateX(109px)';"
                      onmousedown="this.style.border = '1px solid #b20000'; this.querySelector('.button__icon').style.backgroundColor = '#b20000';"
                      onmouseup="this.style.border = '1px solid #cc0000'; this.querySelector('.button__icon').style.backgroundColor = '#cc0000';">

                      <span class="button__icon" style="position: absolute; left: 0; height: 100%; width: 39px; background-color: #cc0000; display: flex; align-items: center; justify-content: center; transition: width 0.3s, transform 0.3s;">
                       <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18 17.94 6M18 18 6.06 6"/>
                      </svg>

                      </span>
                    <span id="edit" class="button__text" style="transform: translateX(35px); color: #fff; font-weight: 600; transition: color 0.3s; margin-left: -1px;">Cancel</span>
               </button>
        </div>
        
        <div id="popup" style="
            display: none; 
            position: fixed; 
            top: 50%; 
            left: 50%; 
            transform: translate(-50%, -50%); 
            background-color: white; 
            border-radius: 10px; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
            width: 300px; 
            z-index: 1000; 
            padding: 20px;">
            <div style="text-align: center;">
              <p style="color: black; font-size: 18px; font-weight: bold; margin: 0;">Delete Camera?</p>
              <p style="color: gray; font-size: 14px; text-align: left; margin-top: 4%; margin-bottom: 2%;">Are you sure you want to delete the <b><?= htmlspecialchars($cameraData['CameraName']); ?></b> camera?</p>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 20px;">
              <button id="cancelButton" style="
                background-color: #f0f0f0; 
                color: black; 
                border: none; 
                padding: 10px 20px; 
                border-radius: 6px; 
                cursor: pointer;
                width: 45%;">Cancel</button>
              <button id="confirmDeleteButton" style="
                background-color: #e50000; 
                color: white; 
                border: none; 
                padding: 10px 20px; 
                border-radius: 6px; 
                cursor: pointer;
                 width: 45%;">Delete</button>
            </div>
            <button id="closePopup" style="
              position: absolute; 
              top: 10px; 
              right: 10px; 
              background: none; 
              border: none; 
              cursor: pointer;">
              <svg height="20px" viewBox="0 0 384 512" style="fill: #ccc;">
                <path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path>
              </svg>
            </button>
          </div>

          <div id="overlay" style="
            display: none; 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(0, 0, 0, 0.4); 
            z-index: 999;"></div>

        <!--   DELETE SCRIPT   -->
        <script>
          const deleteTrigger = document.getElementById('deleteTrigger');
          const popup = document.getElementById('popup');
          const overlay = document.getElementById('overlay');
          const cancelButton = document.getElementById('cancelButton');
          const confirmDeleteButton = document.getElementById('confirmDeleteButton');
          const closePopup = document.getElementById('closePopup');          
          

          const getCameraIdFromURL = () => {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('cameraId');
          };

          deleteTrigger.addEventListener('click', () => {
            popup.style.display = 'block';
            overlay.style.display = 'block';
          });

          const closePopupHandler = () => {
            popup.style.display = 'none';
            overlay.style.display = 'none';
          };
          cancelButton.addEventListener('click', closePopupHandler);
          closePopup.addEventListener('click', closePopupHandler);

          confirmDeleteButton.addEventListener('click', async () => {
            const cameraId = getCameraIdFromURL();

            if (!cameraId) {
              alert('No camera ID found.');
              closePopupHandler();
              return;
            }

            try {
              const response = await fetch(`../../Back-End/PHP/deleteCamera.php?cameraId=${cameraId}`, {
                method: 'GET',
              });
              
              
            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    const overlay = document.createElement('div');
                    overlay.style.position = 'fixed';
                    overlay.style.top = '0';
                    overlay.style.left = '0';
                    overlay.style.width = '100vw';
                    overlay.style.height = '100vh';
                    overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)'; 
                    overlay.style.zIndex = '999'; 

                    const notificationsContainer = document.createElement('div');
                    notificationsContainer.classList.add('notifications-container');
                    notificationsContainer.style.position = 'fixed';
                    notificationsContainer.style.top = '50%';
                    notificationsContainer.style.left = '50%';
                    notificationsContainer.style.transform = 'translate(-50%, -50%)';
                    notificationsContainer.style.zIndex = '1000';
                    notificationsContainer.style.width = '320px';
                    notificationsContainer.style.fontSize = '0.875rem';
                    notificationsContainer.style.lineHeight = '1.25rem';

                    const successNotification = document.createElement('div');
                    successNotification.style.padding = '1.25rem';
                    successNotification.style.borderRadius = '0.75rem';
                    successNotification.style.backgroundColor = 'rgb(240 253 244)';
                    successNotification.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.1)';
                    successNotification.style.transition = 'all 0.3s ease';
                    successNotification.style.border = '1px solid rgba(74, 222, 128, 0.2)';

                    const flexContainer = document.createElement('div');
                    flexContainer.style.display = 'flex';

                    const iconContainer = document.createElement('div');
                    iconContainer.style.flexShrink = '0';

                    const icon = document.createElement('svg');
                    icon.setAttribute('aria-hidden', 'true');
                    icon.setAttribute('fill', 'currentColor');
                    icon.setAttribute('viewBox', '0 0 20 20');
                    icon.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
                    icon.style.width = '1.5rem';
                    icon.style.height = '1.5rem';
                    icon.style.color = 'rgb(74 222 128)';
                    icon.style.filter = 'drop-shadow(0 0 8px rgba(74, 222, 128, 0.4))';
                    icon.innerHTML = `
                        <path
                            clip-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            fill-rule="evenodd"
                        ></path>
                    `;

                    iconContainer.appendChild(icon);

                    const textContainer = document.createElement('div');
                    textContainer.style.marginLeft = '1rem';

                    const heading = document.createElement('p');
                    heading.style.fontWeight = '700';
                    heading.style.color = 'rgb(22 101 52)';
                    heading.style.fontSize = '1.05rem';
                    heading.style.display = 'flex';
                    heading.style.alignItems = 'center';
                    heading.style.gap = '0.5rem';
                    heading.innerText = 'Camera Deleted';
                    const checkmark = document.createElement('span');
                    checkmark.classList.add('checkmark');
                    checkmark.innerText = '✓';
                    heading.appendChild(checkmark);

                    const prompt = document.createElement('p');
                    prompt.style.marginTop = '0.75rem';
                    prompt.style.color = 'rgb(21 128 61)';
                    prompt.style.lineHeight = '1.5';
                    prompt.innerText = 'Camera <?= htmlspecialchars($cameraData['CameraName']); ?> has been successfully deleted!';

                    textContainer.appendChild(heading);
                    textContainer.appendChild(prompt);

                    const buttonContainer = document.createElement('div');
                    buttonContainer.style.display = 'flex';
                    buttonContainer.style.justifyContent = 'flex-end';
                    buttonContainer.style.marginTop = '1rem';
                    buttonContainer.style.gap = '0.75rem';

                    const closeButton = document.createElement('button');
                    closeButton.classList.add('success-button-main');
                    closeButton.type = 'button';
                    closeButton.innerText = 'OK';
                    closeButton.style.padding = '0.5rem 1rem';
                    closeButton.style.backgroundColor = 'rgb(22 101 52)';
                    closeButton.style.color = 'white';
                    closeButton.style.fontSize = '0.875rem';
                    closeButton.style.fontWeight = '600';
                    closeButton.style.borderRadius = '0.5rem';
                    closeButton.style.border = 'none';
                    closeButton.style.transition = 'all 0.2s ease';
                    closeButton.style.boxShadow = '0 2px 8px rgba(22, 101, 52, 0.2)';
                    closeButton.addEventListener('mouseenter', () => {
                        closeButton.style.backgroundColor = 'rgb(21 128 61)';
                        closeButton.style.transform = 'translateY(-1px)';
                        closeButton.style.boxShadow = '0 4px 12px rgba(22, 101, 52, 0.3)';
                    });
                    closeButton.addEventListener('mouseleave', () => {
                        closeButton.style.backgroundColor = 'rgb(22 101 52)';
                        closeButton.style.transform = 'none';
                        closeButton.style.boxShadow = '0 2px 8px rgba(22, 101, 52, 0.2)';
                    });

                    closeButton.addEventListener('click', () => {
                        document.body.removeChild(overlay);  
                        document.body.removeChild(notificationsContainer); 
                        window.location.href = '../../Back-End/PHP/cameras.php'; 
                    });

                    buttonContainer.appendChild(closeButton);

                    flexContainer.appendChild(iconContainer);
                    flexContainer.appendChild(textContainer);
                    successNotification.appendChild(flexContainer);
                    successNotification.appendChild(buttonContainer);
                    notificationsContainer.appendChild(successNotification);

                    document.body.appendChild(overlay);
                    document.body.appendChild(notificationsContainer);
                } else {
                    const overlayError = document.createElement('div');
                    overlayError.style.position = 'fixed';
                    overlayError.style.top = '0';
                    overlayError.style.left = '0';
                    overlayError.style.width = '100vw';
                    overlayError.style.height = '100vh';
                    overlayError.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                    overlayError.style.zIndex = '999';

                    const errorAlert = document.createElement('div');
                    errorAlert.classList.add('notifications-container');
                    errorAlert.style.position = 'fixed';
                    errorAlert.style.top = '50%';
                    errorAlert.style.left = '50%';
                    errorAlert.style.transform = 'translate(-50%, -50%)';
                    errorAlert.style.zIndex = '1000';
                    errorAlert.style.width = '320px';
                    errorAlert.style.fontSize = '0.875rem';
                    errorAlert.style.lineHeight = '1.25rem';

                    const errorNotification = document.createElement('div');
                    errorNotification.style.padding = '1.25rem';
                    errorNotification.style.borderRadius = '0.75rem';
                    errorNotification.style.backgroundColor = 'rgb(255 239 240)';
                    errorNotification.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.1)';
                    errorNotification.style.transition = 'all 0.3s ease';
                    errorNotification.style.border = '1px solid rgba(222, 74, 74, 0.2)';

                    const flexContainerError = document.createElement('div');
                    flexContainerError.style.display = 'flex';

                    const iconContainerError = document.createElement('div');
                    iconContainerError.style.flexShrink = '0';

                    const errorIcon = document.createElement('svg');
                    errorIcon.setAttribute('aria-hidden', 'true');
                    errorIcon.setAttribute('fill', 'currentColor');
                    errorIcon.setAttribute('viewBox', '0 0 20 20');
                    errorIcon.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
                    errorIcon.style.width = '1.5rem';
                    errorIcon.style.height = '1.5rem';
                    errorIcon.style.color = 'rgb(222 74 74)';
                    errorIcon.style.filter = 'drop-shadow(0 0 8px rgba(222, 74, 74, 0.4))';
                    errorIcon.innerHTML = `
                      <path
                        clip-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        fill-rule="evenodd"
                      ></path>
                    `;

                    iconContainerError.appendChild(errorIcon);

                    const textContainerError = document.createElement('div');
                    textContainerError.style.marginLeft = '1rem';

                    const headingError = document.createElement('p');
                    headingError.style.fontWeight = '700';
                    headingError.style.color = 'rgb(182 32 32)';
                    headingError.style.fontSize = '1.05rem';
                    headingError.style.display = 'flex';
                    headingError.style.alignItems = 'center';
                    headingError.style.gap = '0.5rem';
                    headingError.innerText = 'Camera Deletion Failed';

                    const errorCheckmark = document.createElement('span');
                    errorCheckmark.classList.add('error-checkmark');
                    errorCheckmark.innerText = '✗';
                    headingError.appendChild(errorCheckmark);

                    const promptError = document.createElement('p');
                    promptError.style.marginTop = '0.75rem';
                    promptError.style.color = 'rgb(156 163 175)';
                    promptError.style.lineHeight = '1.5';
                    promptError.innerText = "The camera <?= htmlspecialchars($cameraData['CameraName']); ?> is connected to a hall and cannot be deleted!";

                    textContainerError.appendChild(headingError);
                    textContainerError.appendChild(promptError);

                    const buttonContainerError = document.createElement('div');
                    buttonContainerError.style.display = 'flex';
                    buttonContainerError.style.justifyContent = 'flex-end';
                    buttonContainerError.style.marginTop = '1rem';
                    buttonContainerError.style.gap = '0.75rem';

                    const closeButtonError = document.createElement('button');
                    closeButtonError.classList.add('error-button-main');
                    closeButtonError.type = 'button';
                    closeButtonError.innerText = 'OK';
                    closeButtonError.style.padding = '0.5rem 1rem';
                    closeButtonError.style.backgroundColor = 'rgb(182 32 32)';
                    closeButtonError.style.color = 'white';
                    closeButtonError.style.fontSize = '0.875rem';
                    closeButtonError.style.fontWeight = '600';
                    closeButtonError.style.borderRadius = '0.5rem';
                    closeButtonError.style.border = 'none';
                    closeButtonError.style.transition = 'all 0.2s ease';
                    closeButtonError.style.boxShadow = '0 2px 8px rgba(182, 32, 32, 0.2)';
                    closeButtonError.addEventListener('mouseenter', () => {
                        closeButtonError.style.backgroundColor = 'rgb(239 68 68)';
                        closeButtonError.style.transform = 'translateY(-1px)';
                        closeButtonError.style.boxShadow = '0 4px 12px rgba(182, 32, 32, 0.3)';
                    });
                    closeButtonError.addEventListener('mouseleave', () => {
                        closeButtonError.style.backgroundColor = 'rgb(182 32 32)';
                        closeButtonError.style.transform = 'none';
                        closeButtonError.style.boxShadow = '0 2px 8px rgba(182, 32, 32, 0.2)';
                    });

                    closeButtonError.addEventListener('click', () => {
                        document.body.removeChild(overlayError);  
                        document.body.removeChild(errorAlert);    
                    });

                    buttonContainerError.appendChild(closeButtonError);

                    flexContainerError.appendChild(iconContainerError);
                    flexContainerError.appendChild(textContainerError);
                    errorNotification.appendChild(flexContainerError);
                    errorNotification.appendChild(buttonContainerError);
                    errorAlert.appendChild(errorNotification);

                    document.body.appendChild(overlayError);
                    document.body.appendChild(errorAlert);
                } 
            } else {
                alert('Failed to delete the camera. Server responded with an error.');
            }
                
            } catch (error) {
              console.error('Error deleting camera:', error);
              alert('Error occurred while deleting the camera.');
            }

            closePopupHandler();
          });
        </script>
        
            <div id="popupCancel" style=" 
            display: none; 
            position: fixed; 
            top: 50%; 
            left: 50%; 
            transform: translate(-50%, -50%); 
            background-color: white; 
            border-radius: 10px; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
            width: 300px; 
            z-index: 1000; 
            padding: 20px;">
            <div style="text-align: center;">
              <p style="color: black; font-size: 18px; font-weight: bold; margin: 0;">Cancel Changes?</p> 
              <p style="color: gray; font-size: 14px; text-align: left; margin-top: 4%; margin-bottom: 2%;">Are you sure you want to discard the changes?</p> 
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 20px;">
              <button id="cancelButtonForCancel" style=" 
                background-color: #f0f0f0; 
                color: black; 
                border: none; 
                padding: 10px 20px; 
                border-radius: 6px; 
                cursor: pointer;
                width: 45%;">Cancel</button>
              <button id="confirmRemoveUpdatesButton" style=" 
                background-color: #e50000; 
                color: white; 
                border: none; 
                padding: 10px 20px; 
                border-radius: 6px; 
                cursor: pointer;
                 width: 45%;">Confirm</button> 
            </div>
            <button id="closePopupForCancel" style="
              position: absolute; 
              top: 10px; 
              right: 10px; 
              background: none; 
              border: none; 
              cursor: pointer;">
              <svg height="20px" viewBox="0 0 384 512" style="fill: #ccc;">
                <path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path>
              </svg>
            </button>
           </div>
          <div id="overlayForCancel" style="
            display: none; 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(0, 0, 0, 0.4); 
            z-index: 999;"></div>
               
        
         <!--   EDIT SCRIPT   -->
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const form = document.getElementById("cameraForm");
                const editButton = document.querySelector("#editButton");
                const saveButton = document.querySelector("#saveButton");
                const deleteButton = document.querySelector("#deleteTrigger");
                const cancelButton = document.querySelector("#cancel");
                const cancelPopup = document.querySelector("#popupCancel");
                const overlay = document.querySelector("#overlayForCancel");
                const closePopupButton = document.querySelector("#closePopupForCancel");
                const confirmRemoveUpdatesButton = document.querySelector("#confirmRemoveUpdatesButton");
                const cancelButtonForCancel = document.querySelector("#cancelButtonForCancel");
                const popupContent = document.querySelector("#popupCancel");
                const inputs = document.querySelectorAll(".form__input");
                const cameraIPInput = document.getElementById("cameraIP");
                const passwordInput = document.getElementById("cameraPassword");
                const portInput = document.querySelector("input[name='portNo']");
                const ipError = document.getElementById("IPError");
                const portError = document.getElementById("portError");
                const emptyFieldsError = document.getElementById("emptyFilelds");

                let isEditing = false;
                let originalValues = {};
                let ipValid = false;
                let ipErrorShown = false;
                let portValid = false;
                let portErrorShown = false;

                editButton.addEventListener("click", function () {
                    if (!isEditing) {
                        enableEditing();
                        inputs.forEach((input) => {
                            originalValues[input.name] = input.value;
                        });
                        toggleButtons(false);
                        isEditing = true;
                    }
                });

                cancelButton.addEventListener("click", function () {
                    if (isEditing && hasChanges()) {
                        showCancelPopup();
                    } else {
                        resetState();
                    }
                });

                closePopupButton.addEventListener("click", hideCancelPopup);
                cancelButtonForCancel.addEventListener("click", hideCancelPopup);

                confirmRemoveUpdatesButton.addEventListener("click", function () {
                    resetState();
                    hideCancelPopup();
                });

                overlay.addEventListener("click", function (e) {
                    if (!popupContent.contains(e.target)) {
                        e.stopPropagation();
                    }
                });

                popupContent.addEventListener("click", function (e) {
                    e.stopPropagation();
                });

                saveButton.addEventListener("click", function (event) {
                    if (isEditing) {
                        validateIP();
                        validatePort();
                        if (!validateForm() || !ipValid || !portValid) {
                            event.preventDefault();
                        } else {
                            form.submit();
                        }
                    }
                });

                cameraIPInput.addEventListener("blur", function () {
                    if (cameraIPInput.value.trim() !== "") {
                        validateIP();
                    }
                });

                cameraIPInput.addEventListener("input", function () {
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
                        ipError.innerText = "Please enter a valid IP address (e.g., 192.168.1.1).";
                    } else {
                        ipValid = true;
                        ipErrorShown = false;
                        ipError.innerText = "";
                    }
                }

                portInput.addEventListener("blur", function () {
                    if (portInput.value.trim() !== "") {
                        validatePort();
                    }
                });

                portInput.addEventListener("input", function () {
                    if (portErrorShown) {
                        validatePort();
                    }
                });

                function validatePort() {
                    const portValue = parseInt(portInput.value.trim(), 10);

                    if (isNaN(portValue) || portValue < 1) {
                        portValid = false;
                        portErrorShown = true;
                        portError.innerText = "Port number must be 1 or greater.";
                    } else {
                        portValid = true;
                        portErrorShown = false;
                        portError.innerText = "";
                    }
                }

                function validateForm() {
                    let allFilled = true;
                    let errorMessage = "All fields are required.";

                    inputs.forEach(input => {
                        if (input.value.trim() === "") {
                            allFilled = false;
                            input.style.border = "2px solid red";
                        } else {
                            input.style.border = "1px solid #ccc";
                        }
                    });

                    if (!allFilled) {
                        emptyFieldsError.innerText = errorMessage;
                        emptyFieldsError.style.color = "red";
                        return false; 
                    }

                    emptyFieldsError.innerText = ""; 
                    return true; 
                }

                function showCancelPopup() {
                    overlay.style.display = "block";
                    cancelPopup.style.display = "block";
                }

                function hideCancelPopup() {
                    overlay.style.display = "none";
                    cancelPopup.style.display = "none";
                }

                function enableEditing() {
                    inputs.forEach((input) => {
                        input.removeAttribute("readonly");
                        input.style.border = "1px solid white";
                        input.style.backgroundColor = "#f4f7ff";
                    });
                    if (passwordInput) {
                        passwordInput.type = "text";
                    }
                }

                function disableEditing() {
                    inputs.forEach((input) => {
                        input.setAttribute("readonly", true);
                        input.style.border = "none";
                        input.style.backgroundColor = "#ecf0f3";
                    });
                    if (passwordInput) {
                        passwordInput.type = "password";
                    }
                }

                function toggleButtons(enableEditMode) {
                    deleteButton.style.display = enableEditMode ? "" : "none";
                    editButton.style.display = enableEditMode ? "" : "none";
                    cancelButton.style.display = enableEditMode ? "none" : "";
                    saveButton.style.display = enableEditMode ? "none" : "";
                }

                function hasChanges() {
                    for (let input of inputs) {
                        if (input.value !== originalValues[input.name]) {
                            return true;
                        }
                    }
                    return false;
                }

                function resetState() {
                    disableEditing();
                    inputs.forEach((input) => {
                        input.value = originalValues[input.name];
                    });
                    ipError.innerText = ""; 
                    portError.innerText = "";
                    emptyFieldsError.innerText = "";
                    toggleButtons(true);
                    isEditing = false;
                }

                form.addEventListener("submit", function (event) {
                    if (!validateForm()) {
                        event.preventDefault(); 
                    }
                });
            });
        </script>


        
        <?php
        include '../../Back-End/PHP/session.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $servername = "localhost";
            $username = "root";
            $password = "root";
            $dbname = "raqeebdb";

            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $companyID = $_SESSION['CompanyID'];
            $cameraId = isset($_GET['cameraId']) ? intval($_GET['cameraId']) : 0;

            $cameraName = $conn->real_escape_string($_POST['cameraName']);
            $cameraIP = $conn->real_escape_string($_POST['cameraIP']);
            $portNo = intval($_POST['portNo']);
            $stream = $conn->real_escape_string($_POST['stream']);
            $cameraUsername = $conn->real_escape_string($_POST['cameraUsername']);
            $cameraPassword = $conn->real_escape_string($_POST['cameraPassword']);

            $checkCamera = $conn->prepare("
                SELECT * 
                FROM camera 
                WHERE LOWER(CameraName) = LOWER(?) 
                AND CompanyID = ? 
                AND CameraID != ?
            ");
            $checkCamera->bind_param("sii", $cameraName, $companyID, $cameraId);
            $checkCamera->execute();
            $result = $checkCamera->get_result();

            if ($result->num_rows > 0) {
                echo "<script>
                    document.getElementById('cameraNameError').innerText = 'The camera name is already in your list. Please choose a different name.';
                </script>";
                exit;
            }

            $currentPasswordQuery = $conn->prepare("SELECT CameraPassword FROM camera WHERE CameraID = ?");
            $currentPasswordQuery->bind_param("i", $cameraId);
            $currentPasswordQuery->execute();
            $currentPasswordResult = $currentPasswordQuery->get_result();
            $currentPasswordRow = $currentPasswordResult->fetch_assoc();
            $currentPassword = $currentPasswordRow['CameraPassword'];

            if ($cameraPassword === $currentPassword) {
                $updateSql = "
                    UPDATE camera
                    SET CameraName = '$cameraName',
                        CameraIPAddress = '$cameraIP',
                        PortNo = $portNo,
                        StreamingChannel = '$stream',
                        CameraUsername = '$cameraUsername'
                    WHERE CameraID = $cameraId
                ";
            } else {
                $updateSql = "
                    UPDATE camera
                    SET CameraName = '$cameraName',
                        CameraIPAddress = '$cameraIP',
                        PortNo = $portNo,
                        StreamingChannel = '$stream',
                        CameraUsername = '$cameraUsername',
                        CameraPassword = '$cameraPassword'
                    WHERE CameraID = $cameraId
                ";
            }

            if ($conn->query($updateSql) === TRUE) {
                echo "<script>window.location.href = '../../Back-End/PHP/viewEditCameras.php?cameraId=$cameraId';</script>";
            } else {
                echo "<script>alert('Error updating camera: " . $conn->error . "'); history.back();</script>";
            }

            $conn->close();
        }
        ?>
    </body>
</html>
