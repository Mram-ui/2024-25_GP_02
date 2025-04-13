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

    if (isset($_GET['eventId'])) {
        $eventID = intval($_GET['eventId']);
    } else {
        echo "<script>alert('No Event ID provided');</script>";
        exit; 
    }

    $eventQuery = $conn->prepare("SELECT EventName, EventLocation, EventStartDate, EventEndDate, EventStartTime, EventEndTime FROM events WHERE EventID = ?");
    $eventQuery->bind_param("i", $eventID);
    $eventQuery->execute();
    $eventResult = $eventQuery->get_result();

    if ($eventResult->num_rows > 0) {
        $eventData = $eventResult->fetch_assoc();
    } else {
        echo "<script>alert('Event not found');</script>";
        exit;
    }
    
    $hallQuery = $conn->prepare("
        SELECT hall.HallID, hall.HallName, hall.HallThreshold, hall.CameraID, camera.CameraName
        FROM hall
        LEFT JOIN camera ON hall.CameraID = camera.CameraID
        WHERE hall.EventID = ?
    ");
    $hallQuery->bind_param("i", $eventID);
    $hallQuery->execute();
    $hallResult = $hallQuery->get_result();

    $halls = [];
    while ($hallRow = $hallResult->fetch_assoc()) {
        $halls[] = $hallRow;
    }
    $conn->close();
?>

<html lang="es" dir="ltr">

    <head>
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
        <meta charset="utf-8">
        <title><?php echo htmlspecialchars($eventData['EventName']); ?></title>
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
                margin-top: 8.3%;
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
            
            .EditBtn {
                width: 90px;
                height: 40px;
                margin-left: 160%;
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
                margin-top: 1%;
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

            #viewEvent {
                width: 100%;
                margin: 0%;
                padding: 0% 
            }

            #times  {
                display: flex;
                width: 100%;
                margin-left: 30%;
            }


            .form__input {
                width: 70%;
            }

            .HInput,.camera-name {
                width: 210%;
                margin-top: 0%;
            }

            #evenl {
                align-items: left;
                margin-left: -29%;
            }

            .form__input time {
                color: black;
            }

            .AllHalls {
                margin-left: -29%;
                display: flex;
                flex-direction: column; 
                gap: 10px;
                display: flex;
            }
            
             .AllHalls input.form__input,
             .AllHalls select.update-camera {
                 width: 215%; 
                 max-width: 215%; 
                 box-sizing: border-box; 
                 margin: 0;
                 padding: 8px 12px;
             }

             .AllHalls {
                 position: relative; 
             }

             .error-messageForCamera {
                 color: red; 
                 font-size: 14px; 
             }

             .error-messageForCamera.active {
                 visibility: visible;
             }
             
            #capacityError {
                 margin-top: 17%;
             }
             
            .capacity-error {
                 color: red; 
                 font-size: 14px; 
                 width: 200%; 
                 position: absolute; 
                 visibility: hidden; 
            }
            .capacity-error.active {
                visibility: visible !important;
            }
            
            .capacity-error2 {
                 color: red; 
                 font-size: 14px; 
                 width: 200%; 
                 position: absolute; 
                 visibility: hidden; 
            }
            .capacity-error.active {
                visibility: visible !important;
            }

            #hall {
                margin-top: 1%;
                margin-bottom: 7%;
                display: block; 
            }

            label {
                width: 70%;
            }

            #label {
                text-align: left;
                font-weight: normal;
                white-space: nowrap; 

            }
            
            .form__input {
                box-sizing: border-box;
                padding: 10px;
            }

            #arrow {
                margin-left: -138%;
            }

            #title {
                margin-left: -10%;
                margin-bottom: 3%;
            }

            .headerTitle {
                margin-top: 3%;
            }

            #HMAX {
                white-space: nowrap; 
            }

            .hall {
                width: 158%;
                margin: 0;
                margin-left: -45%;
            }

            .firefox .hall {
                width: 133.3%;
                margin: 0;
                margin-left: -32%;
            }

            .safari-browser .hall {
                width: 158%;
                margin: 0;
                margin-left: -45%;
            }


            #eventLocation {
                margin-bottom: 5%;
            }

            h3 {
                text-align: left;
                margin-bottom: 1%;
            }

            #times {
                margin-bottom: 5%;
            }

            .firefox #hallName{
                margin-left: -31%;
            }

            .safari-browser #hallName{
                margin-left: -44.3%;
            }

            #hallName{
                margin-left: -44.3%;
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
              
            .button2 {
              display: none;
              transition: all 0.2s ease-in;
              position: relative;
              overflow: hidden;
              z-index: 1;
              color: #090909;
              padding: 0.4em 1.1em;
              cursor: pointer;
              font-size: 12px;
              font-family: 'Montserrat', sans-serif;
              border-radius: 5px;
              background: #e8e8e8;
              border: 1px solid #e8e8e8;
            }

            .button2:active {
              color: #666;
              box-shadow: inset 4px 4px 12px #c5c5c5, inset -4px -4px 12px #ffffff;
            }

            .button2:before {
              content: "";
              position: absolute;
              left: 50%;
              transform: translateX(-50%) scaleY(1) scaleX(1.25);
              top: 100%;
              width: 140%;
              height: 180%;
              background-color: rgba(0, 0, 0, 0.05);
              border-radius: 50%;
              display: block;
              transition: all 0.5s 0.1s cubic-bezier(0.55, 0, 0.1, 1);
              z-index: -1;
            }

            .button2:after {
              content: "";
              position: absolute;
              left: 55%;
              transform: translateX(-50%) scaleY(1) scaleX(1.45);
              top: 180%;
              width: 160%;
              height: 190%;
              background-color: #cc0000;
              border-radius: 50%;
              display: block;
              transition: all 0.5s 0.1s cubic-bezier(0.55, 0, 0.1, 1);
              z-index: -1;
            }

            .button2:hover {
              color: #ffffff;
              border: 1px solid #cc0000;
            }

            .button2:hover:before {
              top: -35%;
              background-color: #cc0000;
              transform: translateX(-50%) scaleY(1.3) scaleX(0.8);
            }

            .button2:hover:after {
              top: -45%;
              background-color: #cc0000;
              transform: translateX(-50%) scaleY(1.3) scaleX(0.8);
            }
                      
            #startTimeError {
                color: red;
                font-size: 0.9em;
                margin-left: 15%;
                margin-top: -4%;
                margin-bottom: 5%;
                margin-right: 15%;
            }  
            
            #startDayError {
                color: red;
                font-size: 0.9em;
                margin-right: 10%;
                margin-top: -7%;
                margin-bottom: 5%;
            }  
            
            #endDayError {
                color: red;
                font-size: 0.9em;
                margin-left: 15%;
                margin-top: -4%;
                margin-bottom: 5%;
                margin-right: 13%;
            } 
            
            #emptyFilelds {
                color: red;
                font-size: 0.9em;
                text-align: center;
            }   
        </style>
        <script>
            function validateDates() {
                return true;
            }
        </script>
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
                <a id='arrow' href="../../Back-End/PHP/userHome.php">
                    <i class="fa fa-chevron-left" style="color: #003f91; font-size: 30px; justify-self: end;"></i>
                </a>
                <h2 class="title" id="title">Event Details</h2>

                <?php 
                    date_default_timezone_set('Asia/Riyadh');
                    $now = new DateTime();
                    $startDate = new DateTime($eventData['EventStartDate'] . ' ' . $eventData['EventStartTime']);
                    $endDate = new DateTime($eventData['EventEndDate'] . ' ' . $eventData['EventEndTime']);
                    if ($endDate >= $now && $startDate <= $now || $startDate > $now):
                ?>
                    <button id="editButton" onclick="enableEditing()" class="button" type="button" 
                      style="position: relative; border-radius: 6px; width: 150px; height: 40px; cursor: pointer; display: flex; align-items: center; border: 1px solid #007bff; background-color: #007bff; overflow: hidden; transition: all 0.3s; margin-left: 135%;"
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
                
                    <button onclick="saveChanges()" id="saveButton" class="button saveButton" type="button" 
                      style="position: relative; border-radius: 6px; width: 150px; height: 40px; cursor: pointer; display: flex; align-items: center; border: 1px solid #2e8b57; background-color: #2e8b57; overflow: hidden; transition: all 0.3s; margin-left: 135%; display: none;"
                      onmouseover="this.style.backgroundColor='#226740'; this.querySelector('.button__text').style.color = 'transparent'; this.querySelector('.button__icon').style.width = '148px'; this.querySelector('.button__icon').style.transform = 'translateX(0)';"
                      onmouseout="this.style.backgroundColor='#2e8b57'; this.querySelector('.button__text').style.color = '#fff'; this.querySelector('.button__icon').style.width = '39px'; this.querySelector('.button__icon').style.transform = 'translateX(109px)';"
                      onmousedown="this.style.border = '1px solid #194f31'; this.querySelector('.button__icon').style.backgroundColor = '#194f31';"
                      onmouseup="this.style.border = '1px solid #2e8b57'; this.querySelector('.button__icon').style.backgroundColor = '#226740'; ">
                      <span class="button__icon" style="position: absolute; left: 0; height: 100%; width: 39px; background-color: #226740; display: flex; align-items: center; justify-content: center; transition: width 0.3s, transform 0.3s;">
                       <svg class="w-[33px] h-[33px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                          <path stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M5 11.917 9.724 16.5 19 7.5"/>
                        </svg> 
                      </span>
                      <span id="edit" class="button__text" style="transform: translateX(35px); color: #fff; font-weight: 600; transition: color 0.3s; margin-left: 5px;">Save</span>
                    </button>
                <?php endif; ?>
            </div>
            
            <div class="error-message" id="emptyFilelds"></div>
            <form id="viewEvent" class="form" method="POST" action="#" onsubmit="return validateDates()" style="align-items: center;">

                <h3 style="margin-right: 50.6%;">Event information</h3> 
                <label id="lable" for="eventName">Event Name:</label> 
                <input name="eventName" class="form__input" type="text" placeholder="Name" value="<?php echo htmlspecialchars($eventData['EventName']); ?>" required readonly>

                <label id="lable" for="eventLocation">Event Location:</label> 
                <input id="eventLocation" name="eventLocation" class="form__input" type="text" placeholder="Exhibition center, Riyadh" value="<?php echo htmlspecialchars($eventData['EventLocation']); ?>" required readonly>

                <h3 style="margin-right: 53.3%;">Dates and Time</h3> 
                <div id="times">
                    <div class="timeBlocks">
                        <label for="startDate">Start Date:</label>  
                        <input name="startDate" class="form__input time" type="date" value="<?php echo htmlspecialchars($eventData['EventStartDate']); ?>" required readonly> 
                    </div>
                    <div class="timeBlocks"> 
                        <label for="startTime">Start Time:</label> 
                        <input name="startTime" class="form__input time" type="time" value="<?php echo htmlspecialchars($eventData['EventStartTime']); ?>" required readonly> 
                    </div> 
                    <br>
                    <div class="timeBlocks">
                        <label for="endDate">End Date:</label>  
                        <input name="endDate" class="form__input time" type="date" value="<?php echo htmlspecialchars($eventData['EventEndDate']); ?>" required readonly> 
                    </div>
                    <div class="timeBlocks"> 
                        <label for="endTime">End Time:</label>  
                        <input name="endTime" class="form__input time" type="time" value="<?php echo htmlspecialchars($eventData['EventEndTime']); ?>" required readonly> 
                    </div>
                </div>
                <div class="error-message" id="startTimeError"></div>
                <div class="error-message" id="endDayError"></div>
                <div class="error-message" id="startDayError"></div>
                <div class="error-messageForCamera"></div>
                <div class="error-messageForRemoveHall"></div>
                
                 <!--   Upcoming events:   -->
                 <?php 
                    date_default_timezone_set('Asia/Riyadh');
                    $now = new DateTime();
                    $startDate = new DateTime($eventData['EventStartDate'] . ' ' . $eventData['EventStartTime']);
                    $endDate = new DateTime($eventData['EventEndDate'] . ' ' . $eventData['EventEndTime']);
                    if ($startDate > $now):
                 ?>              
                    <button id="addHall" class="button add_item_btn" type="button" 
                      style="position: relative; border-radius: 6px; width: 50px; height: 40px; cursor: pointer; display: flex; align-items: center; border: 1px solid #2e8b57; background-color: #0049ac; overflow: hidden; transition: all 0.3s; margin-right: 14%; margin-top: 0%; display: none;"
                      onmouseover="this.style.backgroundColor='#123064'; this.querySelector('.button__text').style.color = 'transparent'; this.querySelector('.button__icon').style.width = '148px'; this.querySelector('.button__icon').style.transform = 'translateX(0)';"
                      onmouseout="this.style.backgroundColor='#0049ac'; this.querySelector('.button__text').style.color = '#fff'; this.querySelector('.button__icon').style.width = '39px'; this.querySelector('.button__icon').style.transform = 'translateX(109px)';"
                      onmousedown="this.style.border = '1px solid #0049ac'; this.querySelector('.button__icon').style.backgroundColor = '#0049ac';"
                      onmouseup="this.style.border = '1px solid #0049ac'; this.querySelector('.button__icon').style.backgroundColor = '#0049ac'; ">
                       <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" style="margin-left: 25%;">;
                        <path stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14m-7 7V5"/>
                      </svg>
                    </button>
               <?php endif; ?>

                <div class="AllHalls" id="show_item">
                    <?php 
                        $hallNumber = 1;
                        foreach ($halls as $hall):
                    ?>
                    <h3 id="hallName">Hall  <?php echo $hallNumber; ?></h3>
                        <div id="hall" class="hall">
                            <label id="HMAX" for="hallName">Hall Name:</label><br>
                            <input style="margin-bottom: 3%;" name="hallName[]" class="form__input" type="text" placeholder="Main hall" value="<?php echo htmlspecialchars($hall['HallName']); ?>" required readonly><br>
                            <label style="margin-bottom: 0%;" for="cameraName">Camera:</label><br>
                            <input style="margin-bottom: -3%; margin-top: 1.5%;" id="cameraName" name="cameraName" class="form__input camera-name" type="text" value="<?php if ($hall['CameraName']==""){echo "No camera is currently connected to this hall";} else {echo htmlspecialchars($hall['CameraName']);} ?>" required readonly><br>
                            <select id="updateCamera" style="display:none; margin-top: -4%; margin-bottom: 4%;" name="hallCamera[]" class="form__input update-camera">
                                <option value="" disabled <?php echo ($hall['CameraName'] == "") ? "selected" : ""; ?>>No camera is connected</option>
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
                                    $result = $conn->query("SELECT CameraID, CameraName FROM camera WHERE CompanyID='$companyID'");

                                    while ($row = $result->fetch_assoc()) {
                                        $selected = ($row['CameraID'] == $hall['CameraID']) ? "selected" : "";
                                        echo "<option value='{$row['CameraID']}' $selected>{$row['CameraName']}</option>";
                                    }

                                    $conn->close();
                                ?>
                            </select><br>
                            <label id="HMAX" for="hallThreshold">Hall Max Capacity:</label><br>
                            <input id="HInput" name="hallThreshold[]" class="form__input capacity" type="text" placeholder="ex:100" value="<?php echo htmlspecialchars($hall['HallThreshold']); ?>" min="0" required readonly><br>
                            <div class="error-messageForCamera capacity-error" id="capacityError"></div>
                            <!--   Upcoming events:   -->
                            <?php 
                               date_default_timezone_set('Asia/Riyadh');
                               $now = new DateTime();
                               $startDate = new DateTime($eventData['EventStartDate'] . ' ' . $eventData['EventStartTime']);
                               $endDate = new DateTime($eventData['EventEndDate'] . ' ' . $eventData['EventEndTime']);
                               if ($startDate > $now):
                            ?>        
                                <input type="hidden" name="hallID[]" value="<?php echo $hall['HallID']; ?>">
                                <input type="hidden" name="removedHalls[]" value="" class="removed-hall">
                                <button style="margin-top: 2%;" type="button" class="button2 delete-hall-btn" onclick="markForRemoval(this, '<?php echo $hall['HallID']; ?>')">
                                    Remove <?php echo htmlspecialchars($hall['HallName']); ?> Hall
                                </button>

                            <?php endif; ?>
                        </div>
                        <div class="error-messageForCamera" id="lastHall"></div>
                    <?php 
                        $hallNumber++;
                        endforeach;
                    ?>
                </div>
                <br>
            </form>
            
            <script>
                $(document).ready(function () {
                    const originalState = $("#show_item").html();
                    let hallCounter = $("#show_item .hall").length; 

                    $("#addHall").click(function (e) {
                        e.preventDefault();
                        hallCounter++; 

                        const newHallId = "new_" + Date.now();
                        const newHallRow = `
                            <div id="${newHallId}" class="hall">
                                <h3 style="margin-bottom: 4%;">New Hall ${hallCounter}</h3>
                                <label for="hallName">Hall Name:</label><br>
                                <input style="margin-bottom: 3%; border: 1px solid white; background-color: #f4f7ff;" name="newHallName[]" class="form__input" type="text" placeholder="Main hall" required><br>
                                <label style="margin-bottom: -1%;" for="cameraName">Camera:</label><br>
                                <input style="display: none; border: 1px solid white; background-color: #f4f7ff;" id="cameraName" name="cameraName" class="form__input camera-name" type="text" placeholder="No camera connected" readonly required><br>
                                <select style="margin-top: -4%; border: 1px solid white; background-color: #f4f7ff;" name="newHallCamera[]" class="form__input update-camera cameraNewHall" required>
                                    <option value="" disabled selected>Select a camera</option>
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
                                        $result = $conn->query("SELECT CameraID, CameraName FROM camera WHERE CompanyID='$companyID'");

                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='{$row['CameraID']}'>{$row['CameraName']}</option>";
                                        }
                                        $conn->close();
                                    ?>
                                </select><br><br>
                                <label for="hallThreshold">Hall Max Capacity:</label><br>
                                <input style="border: 1px solid white; background-color: #f4f7ff;" name="newHallThreshold[]" class="form__input capacity new-hall-capacity" type="number" placeholder="ex:100" min="0" required><br>
                                <div class="capacity-error2" id="capacityError2"></div>
                                <button style="display: inline-block; margin-bottom: 7%; margin-top: 3%;" class="button2 remove_item_btn" type="button">Remove New Hall ${hallCounter}</button>
                            </div>
                        `;

                        $("#show_item").append(newHallRow);
                    });

                    $(document).on("click", ".remove_item_btn, #removeOrignalHall", function (e) {
                        e.preventDefault();

                        if ($(".hall").length === 1) {
                            $(".error-messageForRemoveHall").html('<p style="color: red; font-size: 0.9em;">At least one hall must remain!</p>');
                            return;
                        } else {
                            $(".error-messageForRemoveHall").html("");
                        }

                        const hallElement = $(this).closest(".hall");
                        const hallName = hallElement.find("h3").text() || "A Hall";
                        hallElement.replaceWith(`<p style="margin-left: -30%; margin-bottom: 7%; color: #cc0000;" class="hall-removed-msg"><b>${hallName}</b> has been removed.</p>`);
                    });

                    $("#cancel").click(function (e) {
                        e.preventDefault();
                        location.reload();
                    });
                });

                function markForRemoval(button, hallID) {
                    let hiddenInput = button.previousElementSibling;
                    let allMarkedForRemoval = true;

                    if (hiddenInput.value === "") {
                        hiddenInput.value = hallID;  
                        button.style.backgroundColor = "red";  
                        button.innerText = "Marked for Removal";
                    } else {
                        hiddenInput.value = "";  
                        button.style.backgroundColor = "";  
                        button.innerText = "Remove Hall";
                    }

                    $(".hall input[type='hidden']").each(function () {
                        if ($(this).val() === "") {
                            allMarkedForRemoval = false;
                        }
                    });

                    if (allMarkedForRemoval) {
                        $(".error-messageForRemoveHall").html('<p style="color: red; font-size: 0.9em; margin-top: 2%;">At least one hall must remain!</p>');
                        button.previousElementSibling.value = ""; 
                        button.style.backgroundColor = "";  
                        button.innerText = "Remove Hall";
                    } else {
                        $(".error-messageForRemoveHall").html("");
                    }
                }
            </script>

            <!-- Past and Upcoming events:   -->
            <?php 
                date_default_timezone_set('Asia/Riyadh');
                $now = new DateTime();
                $startDate = new DateTime($eventData['EventStartDate'] . ' ' . $eventData['EventStartTime']);
                $endDate = new DateTime($eventData['EventEndDate'] . ' ' . $eventData['EventEndTime']);
                if ($endDate < $now || $startDate > $now):
                ?>
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
            <?php endif; ?>
            
            <!--  Current and Upcoming events:   -->
            <?php 
                    date_default_timezone_set('Asia/Riyadh');
                    $now = new DateTime();
                    $startDate = new DateTime($eventData['EventStartDate'] . ' ' . $eventData['EventStartTime']);
                    $endDate = new DateTime($eventData['EventEndDate'] . ' ' . $eventData['EventEndTime']);
                    if ($endDate >= $now && $startDate <= $now || $startDate > $now):
            ?>
             <button id="cancel" class="button cancel" type="button" 
                      style="position: relative; border-radius: 6px; width: 150px; height: 40px; cursor: pointer; display: none; align-items: center; border: 1px solid #cc0000; background-color: #e50000; overflow: hidden; transition: all 0.3s; margin-left: 108%;"
                      onmouseover="this.style.backgroundColor='#cc0000'; this.querySelector('.button__text').style.color = 'transparent'; this.querySelector('.button__icon').style.width = '148px'; this.querySelector('.button__icon').style.transform = 'translateX(0)';"
                      onmouseout="this.style.backgroundColor='#e50000'; this.querySelector('.button__text').style.color = '#fff'; this.querySelector('.button__icon').style.width = '39px'; this.querySelector('.button__icon').style.transform = 'translateX(109px)';"
                      onmousedown="this.style.border = '1px solid #b20000'; this.querySelector('.button__icon').style.backgroundColor = '#b20000';"
                      onmouseup="this.style.border = '1px solid #cc0000'; this.querySelector('.button__icon').style.backgroundColor = '#cc0000';">

                      <span class="button__icon" style="position: absolute; left: 0; height: 100%; width: 39px; background-color: #cc0000; display: flex; align-items: center; justify-content: center; transition: width 0.3s, transform 0.3s;">
                       <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18 17.94 6M18 18 6.06 6"/>
                      </svg>

                      </span>
                    <span id="edit" class="button__text" style="transform: translateX(35px); color: #fff; font-weight: 600; transition: color 0.3s; margin-left: -1px;">Cancel</span>
               </button>
            <?php endif; ?>
        </div>
        <script>
            // Browser is Firefox
            if (navigator.userAgent.indexOf("Firefox") !== -1) {
                document.body.classList.add("firefox");
            }

            //  Browser is Safari
            function isSafari() {
                return /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
            }
            if (isSafari()) {
                document.body.classList.add('safari-browser');
            }
        </script>
        
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
              <p style="color: black; font-size: 18px; font-weight: bold; margin: 0;">Delete Event?</p>
              <p style="color: gray; font-size: 14px; text-align: left; margin-top: 4%; margin-bottom: 2%;">Are you sure you want to delete the <b><?php echo htmlspecialchars($eventData['EventName']); ?></b> Event?</p>
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

          const getEventIdFromURL = () => {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('eventId');
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
            const eventId = getEventIdFromURL();

            if (!eventId) {
              alert('No event ID found.');
              closePopupHandler();
              return;
            }

            try {
              const response = await fetch(`../../Back-End/PHP/deleteEvent.php?eventId=${eventId}`, {
                method: 'GET',
              });

            if (response.ok) {
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
                heading.innerText = 'Event Deleted';
                const checkmark = document.createElement('span');
                checkmark.classList.add('checkmark');
                checkmark.innerText = '✓';
                heading.appendChild(checkmark);

                const prompt = document.createElement('p');
                prompt.style.marginTop = '0.75rem';
                prompt.style.color = 'rgb(21 128 61)';
                prompt.style.lineHeight = '1.5';
                prompt.innerText = 'Event <?php echo htmlspecialchars($eventData['EventName']); ?> has been successfully deleted!';

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
                    window.location.href = '../../Back-End/PHP/userHome.php';
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
                headingError.innerText = 'Event Deletion Failed';

                const errorCheckmark = document.createElement('span');
                errorCheckmark.classList.add('error-checkmark');
                errorCheckmark.innerText = '✗';
                headingError.appendChild(errorCheckmark);

                const promptError = document.createElement('p');
                promptError.style.marginTop = '0.75rem';
                promptError.style.color = 'rgb(156 163 175)';
                promptError.style.lineHeight = '1.5';
                promptError.innerText = 'Error message here. Something went wrong!';

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
            } catch (error) {
              console.error('Error deleting Event:', error);
              alert('Error occurred while deleting the Event.');
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
              <p style="color: black; font-size: 18px; font-weight: bold; margin: 0;">Cancel Updates?</p> 
              <p style="color: gray; font-size: 14px; text-align: left; margin-top: 4%; margin-bottom: 2%;">Are you sure you want to remove the updated data?</p> 
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
                 width: 45%;">Conform</button> 
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
            
            
        <!--   Upcoming events:   -->
        <?php 
            date_default_timezone_set('Asia/Riyadh');
            $now = new DateTime();
            $startDate = new DateTime($eventData['EventStartDate'] . ' ' . $eventData['EventStartTime']);
            $endDate = new DateTime($eventData['EventEndDate'] . ' ' . $eventData['EventEndTime']);
            if ($startDate > $now):
        ?>            
        <!--   EDIT SCRIPT for Upcoming Events  -->
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const editButton = document.querySelector("#editButton");
                const saveButton = document.querySelector(".saveButton");
                const deleteButton = document.querySelector("#deleteTrigger");
                const cancelButton = document.querySelector(".cancel");
                const addHallButton = document.querySelector("#addHall");
                const inputs = document.querySelectorAll(".form__input");
                const errorMessageDiv = document.querySelector(".error-messageForCamera");
                const halls = document.querySelectorAll(".hall");
                const capacityInputs = document.querySelectorAll(".capacity");
                const newCapacityInputs = document.querySelectorAll(".new-hall-capacity");
                const capacityErrorDiv = document.getElementById("capacityError");
                const capacityErrorNewDiv = document.getElementById("capacity-error2");
                const emptyFieldsErrorDiv = document.getElementById("emptyFilelds");

                let isEditing = false;
                let originalValues = {};

                function validateCameras() {
                    const cameraSelects = document.querySelectorAll(".update-camera, .cameraNewHall");
                    const selectedCameras = [];

                    cameraSelects.forEach((select) => {
                        if (select.value) {
                            selectedCameras.push(select.value);
                        }
                    });

                    const hasDuplicates = new Set(selectedCameras).size !== selectedCameras.length;

                    if (hasDuplicates) {
                        errorMessageDiv.textContent = "The same camera cannot be selected for multiple halls!";
                        errorMessageDiv.classList.add("active");
                        return false;
                    } else {
                        errorMessageDiv.textContent = "";
                        errorMessageDiv.classList.remove("active");
                        return true;
                    }
                }

                function addCameraValidationListeners() {
                    document.querySelectorAll(".update-camera, .cameraNewHall").forEach((select) => {
                        select.addEventListener("change", function () {
                            validateCameras();
                            checkFormValidity();
                        });
                    });
                }

                document.querySelector('.form').addEventListener('change', function (event) {
                    if (event.target.classList.contains('update-camera') || event.target.classList.contains('cameraNewHall')) {
                        validateCameras();
                        checkFormValidity();
                    }
                });

                function enableEditing() {
                    inputs.forEach((input) => {
                        input.removeAttribute("readonly");
                        input.style.border = "1px solid white";
                        input.style.backgroundColor = "#f4f7ff";
                    });

                    halls.forEach((hall) => {
                        hall.querySelector(".camera-name").style.display = "none";
                        hall.querySelector(".update-camera").style.display = "";
                        hall.querySelector(".button2").style.display = "inline-block";
                    });

                    addCameraValidationListeners();
                    checkFormValidity();
                }

                function disableEditing() {
                    inputs.forEach((input) => {
                        input.setAttribute("readonly", true);
                        input.style.border = "none";
                        input.style.backgroundColor = "#ecf0f3";
                    });

                    halls.forEach((hall) => {
                        hall.querySelector(".camera-name").style.display = "";
                        hall.querySelector(".update-camera").style.display = "none";
                        hall.querySelector(".button2").style.display = "none";
                    });

                    errorMessageDiv.textContent = "";
                    errorMessageDiv.classList.remove("active");
                }

                function toggleButtons(enableEditMode) {
                    deleteButton.style.display = enableEditMode ? "" : "none";
                    editButton.style.display = enableEditMode ? "" : "none";
                    cancelButton.style.display = enableEditMode ? "none" : "";
                    addHallButton.style.display = enableEditMode ? "none" : "";
                    saveButton.style.display = enableEditMode ? "none" : "";
                }

                function validateCapacity(event) {
                    const input = event.target;
                    const value = input.value.trim();
                    const errorDiv = input.closest(".hall").querySelector(".capacity-error");

                    if (isNaN(value) || value < 0 || value === "") {
                        errorDiv.textContent = "Please enter a valid number greater than or equal to 0!";
                        errorDiv.classList.add("active");
                        errorDiv.style.display = "block";
                        return false;
                    } else {
                        errorDiv.textContent = "";
                        errorDiv.classList.remove("active");
                        errorDiv.style.display = "none";
                        return true;
                    }
                }

                function validateNewCapacity(event) {
                    const input = event.target;
                    const value = input.value.trim();
                    const errorDiv2 = input.closest(".hall").querySelector(".capacity-error2");

                    if (isNaN(value) || value < 0 || value === "") {
                        errorDiv2.textContent = "Please enter a valid number greater than or equal to 0!";
                        errorDiv2.classList.add("active");
                        errorDiv2.style.display = "block";
                        return false;
                    } else {
                        errorDiv2.textContent = "";
                        errorDiv2.classList.remove("active");
                        errorDiv2.style.display = "none";
                        return true;
                    }
                }

                function validateAllCapacities() {
                    let allValid = true;
                    capacityInputs.forEach((input) => {
                        if (!validateCapacity({ target: input })) {
                            allValid = false;
                        }
                    });
                    newCapacityInputs.forEach((input) => {
                        if (!validateNewCapacity({ target: input })) {
                            allValid = false;
                        }
                    });
                    return allValid;
                }

                function checkEmptyFields() {
                    let allValid = true;
                    const requiredFields = [...inputs];

                    requiredFields.forEach((input) => {
                        if (!input.value.trim()) {
                            input.style.border = "2px solid red";
                            allValid = false;
                        } else {
                            input.style.border = "none";
                        }
                    });

                    if (!allValid) {
                        emptyFieldsErrorDiv.textContent = "All fields are required.";
                        emptyFieldsErrorDiv.style.display = "block";
                    } else {
                        emptyFieldsErrorDiv.textContent = "";
                        emptyFieldsErrorDiv.style.display = "none";
                    }

                    return allValid;
                }

                function checkFormValidity() {
                    const camerasValid = validateCameras();
                    const capacitiesValid = validateAllCapacities();
                    const emptyFieldsValid = checkEmptyFields();

                    if (camerasValid && capacitiesValid && emptyFieldsValid) {
                        saveButton.disabled = false;
                    } else {
                        saveButton.disabled = true;
                    }
                }

                inputs.forEach((input) => {
                    input.addEventListener("blur", function (event) {
                        checkEmptyFields();
                        checkFormValidity();
                    });

                    input.addEventListener("input", function (event) {
                        checkEmptyFields();
                        checkFormValidity();
                    });
                });

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
                    disableEditing();
                    inputs.forEach((input) => {
                        input.value = originalValues[input.name];
                    });

                    toggleButtons(true);
                    isEditing = false;
                    checkFormValidity();
                });

                saveButton.addEventListener("click", function (e) {
                    e.preventDefault();
                    if (validateCameras() && validateAllCapacities() && checkEmptyFields()) {
                        document.querySelector("form").submit();
                    }
                });

                setTimeout(checkFormValidity, 200);
            });
        </script>

        <?php
            include '../../Back-End/PHP/session.php';

            date_default_timezone_set('Asia/Riyadh');

            $servername = "localhost";
            $username = "root";
            $password = "root";
            $dbname = "raqeebdb";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            if (isset($_GET['eventId'])) {
                $eventID = intval($_GET['eventId']);
            } else {
                die("Event ID is missing in the URL!");
            }

            $sql = "SELECT * FROM events WHERE EventID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $eventID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                die("No event found with the provided ID.");
            }

            $event = $result->fetch_assoc();
            $stmt->close();

            $sqlHalls = "SELECT HallID, HallName, HallThreshold, CameraID FROM hall WHERE EventID = ?";
            $stmtHalls = $conn->prepare($sqlHalls);
            $stmtHalls->bind_param("i", $eventID);
            $stmtHalls->execute();
            $hallsResult = $stmtHalls->get_result();

            $halls = [];
            while ($hall = $hallsResult->fetch_assoc()) {
                $halls[] = $hall;
            }

            $stmtHalls->close();

            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $eventName = $_POST['eventName'];
                $eventLocation = $_POST['eventLocation'];
                $eventStartDate = $_POST['startDate'];
                $eventEndDate = $_POST['endDate'];
                $eventStartTime = $_POST['startTime'];
                $eventEndTime = $_POST['endTime'];

                $today = date('Y-m-d');
                $now = date('H:i');
                $validationPassed = true;

                if ($eventStartDate == $today && $eventStartTime <= $now) {
                    // As text: 
                    // echo "<script> document.getElementById('startTimeError').innerText= 'For events scheduled today, the start time cannot be in the past! Please choose a valid time for today\'s event.';</script>";
                    echo "<script> alert('For events scheduled today, the start time cannot be in the past! Please choose a valid time for today\'s event.');</script>";
                    $validationPassed = false;
                } elseif ($eventStartDate < $today) {
                    // As text: 
                    // echo "<script> document.getElementById('startDayError').innerText ='The event cannot start in the past! Please select a future start date and time.';</script>";
                    echo "<script> alert('The event cannot start in the past! Please select a future start date and time.');</script>";
                    $validationPassed = false;
                }

                if ($eventStartDate > $eventEndDate || ($eventStartDate == $eventEndDate && $eventStartTime >= $eventEndTime)) {
                    // As text: 
                    // echo "<script> document.getElementById('endDayError').innerText = 'The event end date and time cannot be earlier than the start date and time! Please ensure the end date and time are after the start date and time.';</script>";
                    echo "<script> alert('The event end date and time cannot be earlier than the start date and time! Please ensure the end date and time are after the start date and time.');</script>";
                    $validationPassed = false;
                }

                if ($validationPassed) {
                    $stmt = $conn->prepare("UPDATE events SET EventName = ?, EventLocation = ?, EventStartDate = ?, EventEndDate = ?, EventStartTime = ?, EventEndTime = ? WHERE EventID = ?");
                    $stmt->bind_param("ssssssi", $eventName, $eventLocation, $eventStartDate, $eventEndDate, $eventStartTime, $eventEndTime, $eventID);

                    if ($stmt->execute()) {
                
                        if (!empty($_POST['removedHalls'])) {
                            foreach ($_POST['removedHalls'] as $removedHallID) {
                                if (!empty($removedHallID)) {
                                    $stmtDelete = $conn->prepare("DELETE FROM hall WHERE HallID = ?");
                                    $stmtDelete->bind_param("i", $removedHallID);
                                    $stmtDelete->execute();
                                    $stmtDelete->close();
                                }
                            }
                        }

                        if (isset($_POST['hallName']) || isset($_POST['hallThreshold']) || isset($_POST['hallCamera'])) {
                            foreach ($_POST['hallName'] as $key => $value) {
                                $hallID = $halls[$key]['HallID'];
                                $hallThreshold = $_POST['hallThreshold'][$key];
                                $hallCameraID = isset($_POST['hallCamera'][$key]) && !empty($_POST['hallCamera'][$key]) 
                                    ? intval($_POST['hallCamera'][$key]) 
                                    : null;

                                if ($hallCameraID === null) {
                                    $stmtHall = $conn->prepare("UPDATE hall SET HallName = ?, HallThreshold = ? WHERE HallID = ?");
                                    $stmtHall->bind_param("sii", $value, $hallThreshold, $hallID);
                                } else {
                                    $stmtHall = $conn->prepare("UPDATE hall SET HallName = ?, HallThreshold = ?, CameraID = ? WHERE HallID = ?");
                                    $stmtHall->bind_param("siii", $value, $hallThreshold, $hallCameraID, $hallID);
                                }

                                $stmtHall->execute();
                                $stmtHall->close();
                            }
                        }

                        if (isset($_POST['newHallName']) && isset($_POST['newHallThreshold']) && isset($_POST['newHallCamera'])) {
                            foreach ($_POST['newHallName'] as $key => $value) {
                                $newHallName = $value;
                                $newHallThreshold = $_POST['newHallThreshold'][$key];
                                $newHallCameraID = isset($_POST['newHallCamera'][$key]) && !empty($_POST['newHallCamera'][$key]) 
                                    ? intval($_POST['newHallCamera'][$key]) 
                                    : null;

                                if ($newHallCameraID === null) {
                                    $stmtNewHall = $conn->prepare("INSERT INTO hall (EventID, HallName, HallThreshold) VALUES (?, ?, ?)");
                                    $stmtNewHall->bind_param("isi", $eventID, $newHallName, $newHallThreshold);
                                } else {
                                    $stmtNewHall = $conn->prepare("INSERT INTO hall (EventID, HallName, HallThreshold, CameraID) VALUES (?, ?, ?, ?)");
                                    $stmtNewHall->bind_param("isii", $eventID, $newHallName, $newHallThreshold, $newHallCameraID);
                                }

                                $stmtNewHall->execute();
                                $stmtNewHall->close();
                            }
                        }
                        echo "<script>window.location.href = '../../Back-End/PHP/viewEditEvent.php?eventId=$eventID';</script>";
                    } else {
                        echo "<script>alert('Failed to update the event: " . $stmt->error . "');</script>";
                    }
                    
                    $stmt->close();
                }
            }

            $conn->close();
        ?>  
        <?php endif; ?>
        
            
            
        <!--   Current events:   -->
        <?php 
            date_default_timezone_set('Asia/Riyadh');
            $now = new DateTime();
            $startDate = new DateTime($eventData['EventStartDate'] . ' ' . $eventData['EventStartTime']);
            $endDate = new DateTime($eventData['EventEndDate'] . ' ' . $eventData['EventEndTime']);
            if ($startDate <= $now || $startDate > $now):
        ?>            
            <!--   EDIT SCRIPT for Current Events  -->
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const editButton = document.querySelector("#editButton");
                    const saveButton = document.querySelector(".saveButton");
                    const cancelButton = document.querySelector(".cancel");
                    const inputs = document.querySelectorAll(".form__input");
                    const halls = document.querySelectorAll(".hall");
                    const errorMessageDiv = document.querySelector(".error-messageForCamera");

                    let isEditing = false;
                    let originalCameraSelections = {};

                    function validateCameras() {
                        const selectedCameras = new Set();
                        let hasDuplicates = false;

                        halls.forEach((hall) => {
                            const updateCamera = hall.querySelector(".update-camera");
                            if (updateCamera.value) {
                                if (selectedCameras.has(updateCamera.value)) {
                                    hasDuplicates = true;
                                } else {
                                    selectedCameras.add(updateCamera.value);
                                }
                            }
                        });

                        if (hasDuplicates) {
                            errorMessageDiv.textContent = "The same camera cannot be selected for multiple halls!";
                            errorMessageDiv.classList.add("active");
                            saveButton.disabled = true;
                            return false;
                        } else {
                            errorMessageDiv.textContent = "";
                            errorMessageDiv.classList.remove("active");
                            saveButton.disabled = false;
                            return true;
                        }
                    }

                    halls.forEach((hall, index) => {
                        const updateCamera = hall.querySelector(".update-camera");
                        originalCameraSelections[index] = updateCamera.value;

                        updateCamera.addEventListener("change", validateCameras);
                    });

                    editButton.addEventListener("click", function () {
                        if (!isEditing) {
                            enableEditing();
                            toggleButtons(false);
                            isEditing = true;
                        }
                    });

                    cancelButton.addEventListener("click", function () {
                        disableEditing();
                        halls.forEach((hall, index) => {
                            const updateCamera = hall.querySelector(".update-camera");
                            updateCamera.value = originalCameraSelections[index];
                        });
                        toggleButtons(true);
                        isEditing = false;
                    });

                    saveButton.addEventListener("click", function (e) {
                        e.preventDefault();
                        if (validateCameras()) {
                            halls.forEach((hall, index) => {
                                const updateCamera = hall.querySelector(".update-camera");
                                originalCameraSelections[index] = updateCamera.value;
                            });
                            toggleButtons(true);
                            isEditing = false;
                            document.querySelector("form").submit();
                        }
                    });

                    function enableEditing() {
                        halls.forEach((hall) => {
                            const cameraNameInput = hall.querySelector(".camera-name");
                            const updateCamera = hall.querySelector(".update-camera");
                            cameraNameInput.style.display = "none";
                            updateCamera.style.display = "";
                            updateCamera.style.border = "1px solid white";
                            updateCamera.style.backgroundColor = "#f4f7ff";
                        });
                    }

                    function disableEditing() {
                        halls.forEach((hall) => {
                            const cameraNameInput = hall.querySelector(".camera-name");
                            const updateCamera = hall.querySelector(".update-camera");
                            cameraNameInput.style.display = "";
                            updateCamera.style.display = "none";
                        });
                        errorMessageDiv.textContent = "";
                        errorMessageDiv.classList.remove("active");
                    }

                    function toggleButtons(enableEditMode) {
                        editButton.style.display = enableEditMode ? "" : "none";
                        cancelButton.style.display = enableEditMode ? "none" : "";
                        saveButton.style.display = enableEditMode ? "none" : "";
                    }
                });
            </script>    

            <?php
               include '../../Back-End/PHP/session.php';

               date_default_timezone_set('Asia/Riyadh');

               $servername = "localhost";
               $username = "root";
               $password = "root";
               $dbname = "raqeebdb";

               $conn = new mysqli($servername, $username, $password, $dbname);

               if ($conn->connect_error) {
                   die("Connection failed: " . $conn->connect_error);
               }

               if (isset($_GET['eventId'])) {
                   $eventID = intval($_GET['eventId']);
               } else {
                   die("Event ID is missing in the URL!");
               }
               $sqlHalls = "SELECT * FROM hall WHERE EventID = ?";
               $stmtHalls = $conn->prepare($sqlHalls);
               $stmtHalls->bind_param("i", $eventID);
               $stmtHalls->execute();
               $hallsResult = $stmtHalls->get_result();

               $halls = [];
               while ($hall = $hallsResult->fetch_assoc()) {
                   $halls[] = $hall;
               }

               $stmtHalls->close();

               if ($_SERVER["REQUEST_METHOD"] === "POST") {
                   echo '<pre>';
                   print_r($_POST['hallName']);
                   print_r($_POST['hallCamera']);
                   echo '</pre>';

                   if (isset($_POST['hallCamera'])) {
                       foreach ($_POST['hallCamera'] as $key => $hallCameraID) {
                           if (!empty($hallCameraID)) {
                               $stmtHall = $conn->prepare("UPDATE hall SET CameraID = ? WHERE EventID = ? AND HallID = ?");
                               $stmtHall->bind_param("iii", $hallCameraID, $eventID, $halls[$key]['HallID']);
                               $stmtHall->execute();
                               $stmtHall->close();
                           }
                       }
                       echo "<script>window.location.href = '../../Back-End/PHP/viewEditEvent.php?eventId=$eventID';</script>";
                   } else {
                       echo "No camera data to update!";
                   }
               }
               $conn->close();
           ?>
        <?php endif; ?>
    </body>
</html>
