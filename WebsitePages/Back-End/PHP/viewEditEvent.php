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

            #main {
                width: -20%;
                padding: 0%;
                margin-left: 20%;
                margin-right: 20%;
                padding-bottom: 2%;
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
        </header>

        <div id="main" class="main">
            <div class="headerTitle">
                <a id='arrow' href="../../Back-End/PHP/userHome.php">
                    <i class="fa fa-chevron-left" style="color: #003f91; font-size: 30px; justify-self: end;"></i>
                </a>
                <h2 class="title" id="title">Event Details</h2>

                <?php if (strtotime($eventData['EventEndDate']) > time()): ?>
                    <button class="EditBtn" onclick="alert('Edit feature is not available yet.');">Edit</button>
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

            <?php if (strtotime($eventData['EventEndDate']) <= time() || strtotime($eventData['EventStartDate']) > time()):?>
                <button class="DeleteBtn" onclick="alert('Delete feature is not available yet.');">Delete Event</button>
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
