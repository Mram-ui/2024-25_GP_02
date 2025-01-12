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
        SELECT hall.HallName, hall.HallThreshold, hall.CameraID, camera.CameraName
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

            #HInput {
                width: 210%;
                margin-top: 0%;
            }

            .AllHalls {
                margin-left: -29%;
            }

            #evenl {
                align-items: left;
                margin-left: -29%;
            }

            .form__input time {
                color: black;
            }

            .AllHalls {
                display: flex;
                flex-direction: column; 
                gap: 10px;
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


            .AllHalls {
                display: flex;
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

                <?php if (strtotime($eventData['EventEndDate']) > time()): ?>
                    <button class="button" type="button" 
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
                      <span class="button__text" style="transform: translateX(35px); color: #fff; font-weight: 600; transition: color 0.3s; margin-left: 10px;">Edit</span>
                    </button>
                <?php endif; ?>
            </div>

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
                <div class="AllHalls">
                    <?php 
                        $hallNumber = 1;
                        foreach ($halls as $hall):
                    ?>
                    <h3 id="hallName">Hall  <?php echo $hallNumber; ?></h3>
                        <div id="hall" class="hall">
                            <label id="HMAX" for="hallName">Hall Name:</label><br>
                            <input id="HInput" name="hallName" class="form__input" type="text" placeholder="Main hall" value="<?php echo htmlspecialchars($hall['HallName']); ?>" required readonly><br>
                            <label for="cameraName">Camera:</label><br>
                            <input id="HInput" name="cameraName" class="form__input" type="text" value="<?php echo htmlspecialchars($hall['CameraName']); ?>" required readonly><br>
                            <label id="HMAX" for="hallThreshold">Hall Max Capacity:</label><br>
                            <input id="HInput" name="hallThreshold" class="form__input" type="text" placeholder="ex:100" value="<?php echo htmlspecialchars($hall['HallThreshold']); ?>" required readonly>
                        </div>
                    <?php 
                        $hallNumber++;
                        endforeach;
                    ?>
                </div>
                <br>
            </form>

            <?php if (strtotime($eventData['EventEndDate']) < time() || strtotime($eventData['EventStartDate']) > time()):?>
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
    </body>
</html>
