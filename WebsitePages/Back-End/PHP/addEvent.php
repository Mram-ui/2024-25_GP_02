<?php
    include '../../Back-End/PHP/session.php';
    $companyID = $_SESSION['CompanyID'];

?>
<html lang="es" dir="ltr">

    <head>
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
        <meta charset="utf-8">
        <title>Add Event</title>
        <link rel="stylesheet" type="text/css" href="../../Front-End/CSS/boxes.css">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
        <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

    </head>
    <style>
        .addCameraLink{
            margin-bottom: 2%;
        }

        .addCameraLink a:hover {
            color: #4a56ff;
        }
    </style>

    <body>
        <header class="header">
            <div class="logo">
                <a href="../../Back-End/PHP/userHome.php"><img src="../../images/Logo2.png" alt="Company Logo"></a>
            </div>
        </header>

        <div class="main">
            <a id='arrow' href="../../Back-End/PHP/userHome.php"><i class="fa fa-chevron-left" style="color: #003f91; font-size: 30px; justify-self: end;"></i></a>
            <h2 class="title">Add Event</h2>
            <form id="addEvent" class="form" method="POST" action="../../Back-End/PHP/addEvent.php" onsubmit="return validateDates()">
                <label for="eventName">Event Name:</label> 
                <input name="eventName" class="form__input" type="text" placeholder="Name" required> <br>

                <label for="eventLocation">Event Location:</label> 
                <input name="eventLocation" class="form__input" type="text" placeholder="Exhibition center, Riyadh" required> <br>

                <div id="times">
                    <div class="timeBlocks">
                        <label for="startDate">Start Date:</label>  
                        <input name="startDate" class="form__input time" type="date" required> 
                    </div>
                    <div class="timeBlocks"> 
                        <label for="startTime">Start Time:</label> 
                        <input name="startTime" class="form__input time" type="time" required> 
                    </div> 
                    <br>
                    <div class="timeBlocks">
                        <label for="endDate">End Date:</label>  
                        <input name="endDate" class="form__input time" type="date" required> 
                    </div>
                    <div class="timeBlocks"> 
                        <label for="endTime">End Time:</label>  
                        <input name="endTime" class="form__input time" type="time" required> 
                    </div>
                </div>

                <div class="AllHalls">
                    <div id="hall" class="hall">
                        <label for="hallName">Hall Name:</label><br>
                        <input name="hallName" class="form__input" type="text" placeholder="Main hall" required><br>

                        <label for="hallCamera">Hall Camera:</label><br>

                        <!-- Camera Dropdown populated from database -->
                        <select name="hallCamera" required>
                        <option value="" disabled selected style="display: none;">Select your camera</option>

                        <?php
                            $servername = "localhost"; 
                            $username = "root";
                            $password = "root";
                            $dbname = "raqeebdb";

                            $conn = new mysqli($servername, $username, $password, $dbname);

                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            }

                            $result = $conn->query("SELECT CameraID, CameraName FROM camera WHERE CompanyID='$companyID'");

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['CameraID']}'>{$row['CameraName']}</option>";
                                }
                            }

                            $conn->close();
                        ?>
                        </select> <br>
                        <p class="addCameraLink">
                            <a href="addCamera.php">Don't have a camera?</a>
                        </p>

                        <label for="hallThreshold">Hall Max Capacity:</label><br>
                        <input name="hallThreshold" class="form__input" type="number" placeholder="00" min="0" required>
                    </div>
                </div>
                <br>
                <button class="form__button button submit">ADD EVENT</button>
            </form>
        </div>

        <?php 
            include '../../Back-End/PHP/session.php';

            date_default_timezone_set('Asia/Riyadh');

            $servername = "localhost"; 
            $username = "root";
            $password = "root";
            $dbname = "raqeebdb";

            $conn = new mysqli($servername, $username, $password, $dbname);

            //For TESTING:
    //        $now = date('H:i');
    //        echo 'The time is '.$now;


            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $CompanyID = $_SESSION['CompanyID'];

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $eventName = $_POST['eventName'];
                $eventLocation = $_POST['eventLocation'];
                $eventStartDate = $_POST['startDate'];
                $eventEndDate = $_POST['endDate'];     
                $eventStartTime = $_POST['startTime']; 
                $eventEndTime = $_POST['endTime'];     

                $hallName = $_POST['hallName'];
                $hallThreshold = $_POST['hallThreshold'];
                $hallCamera = $_POST['hallCamera'];

                $today = date('Y-m-d');
                $now = date('H:i');

                if ($eventStartDate == $today) {
                    if ($eventStartTime <= $now) {
                        echo "<script>alert('For events scheduled today, the start time cannot be in the past! Please choose a valid time for today's event.');</script>";
                        exit;
                    }
                } elseif ($eventStartDate < $today) {
                    echo "<script>alert('The event end date and time cannot be earlier than the start date and time! Please ensure the end date and time are after the start date and time.');</script>";
                    exit;
                }

                if ($eventStartDate > $eventEndDate || ($eventStartDate == $eventEndDate && $eventStartTime >= $eventEndTime)) {
                    echo "<script>alert('Start date and time cannot be after the end date and time!');</script>";
                    exit;
                }


                $conn->begin_transaction();

                try {
                    $stmt = $conn->prepare("INSERT INTO events (EventName, EventLocation, EventStartDate, EventEndDate, EventStartTime, EventEndTime, CompanyID) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssssi", $eventName, $eventLocation, $eventStartDate, $eventEndDate, $eventStartTime, $eventEndTime, $CompanyID);

                    if (!$stmt->execute()) {
                        throw new Exception("Error inserting event: " . $stmt->error);
                    }

                    $eventID = $conn->insert_id;

                    $stmt = $conn->prepare("INSERT INTO hall (HallName, HallThreshold, CameraID, EventID) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("siii", $hallName, $hallThreshold, $hallCamera, $eventID);

                    if (!$stmt->execute()) {
                        throw new Exception("Error inserting hall(s): " . $stmt->error);
                    }

                    $conn->commit();

                    echo "<script>alert('New event added successfully!');</script>";

                    echo "<script>window.location.href = 'userHome.php';</script>";
                    exit;

                } catch (Exception $e) {
                    $conn->rollback();
                    echo "<script>alert('Failed to add event: " . $e->getMessage() . ". Please check the event details and try again.');</script>";
                }

                $stmt->close();
                $conn->close();
            }
        ?>


        <script>
           function validateDates() {
            const now = new Date();

            const startDate = new Date(document.querySelector('input[name="startDate"]').value);
            const startTime = document.querySelector('input[name="startTime"]').value;
            const endDate = new Date(document.querySelector('input[name="endDate"]').value);

            if (startDate.setHours(0, 0, 0, 0) < now.setHours(0, 0, 0, 0)) {
                alert("The event cannot start in the past! Please select a future start date and time.");
                return false;  

            if (startDate.setHours(0, 0, 0, 0) === now.setHours(0, 0, 0, 0)) {
                const [startHours, startMinutes] = startTime.split(':');
                const selectedStartTime = new Date();
                selectedStartTime.setHours(startHours, startMinutes, 0, 0);

                if (selectedStartTime <= now) {
                    alert("For events scheduled today, the start time cannot be in the past! Please choose a valid time for today's event.");
                    return false; 
                }
            }

            if (startDate >= endDate) {
                alert("The event end date and time cannot be earlier than the start date and time! Please ensure the end date and time are after the start date and time.");
                return false;  
            }

            return true; 
        }

        </script>
    </body>
</html>
