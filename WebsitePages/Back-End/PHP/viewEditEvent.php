<?php
    session_start();

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Database connection
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

    // Get EventID from the URL safely
    if (isset($_GET['eventId'])) {
        $eventID = intval($_GET['eventId']); // Cast to integer for safety
    } else {
        echo "<script>alert('No Event ID provided');</script>";
        exit; // Stop further processing if no event ID is found
    }

    // Prepare SQL query to fetch event details
    $eventQuery = $conn->prepare("SELECT EventName, EventLocation, EventStartDate, EventEndDate, EventStartTime, EventEndTime FROM events WHERE EventID = ?");
    $eventQuery->bind_param("i", $eventID);
    $eventQuery->execute();
    $eventResult = $eventQuery->get_result();

    // Check if the event was found
    if ($eventResult->num_rows > 0) {
        // Fetch event data
        $eventData = $eventResult->fetch_assoc();
    } else {
        echo "<script>alert('Event not found');</script>";
        exit; // Stop further processing if no event is found
    }

    // Prepare SQL query to fetch hall data
    $hallQuery = $conn->prepare("SELECT HallName, HallThreshold FROM hall WHERE EventID = ?");
    $hallQuery->bind_param("i", $eventID);
    $hallQuery->execute();
    $hallResult = $hallQuery->get_result();

    $halls = [];
    while ($hallRow = $hallResult->fetch_assoc()) {
        $halls[] = $hallRow;
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

    // Close the database connection
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
            border-radius: 10px;
            margin-right: -290px;
            margin-left: 6%;
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
        
        #main {
            width: -20%;
            padding: 0%;
            margin-left: 20%;
            margin-right: 20%;
        }
        
        #title {
            margin: 10%;
            margin-left: -1250%;
            align-items: center;
            margin-right: 1500%;
            white-space: nowrap;
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
        
        .headerTitle {
            display: flex;
            margin-top: 2%;
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
            margin-top: 0%;
            margin-bottom: 0%;
            display: block; 
        }
        
        label {
            width: 70%;
        }
        
        #label {
            text-align: left;
            font-weight: normal;
        }
        .form__input {
            box-sizing: border-box;
            padding: 10px;
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
        <h2 class="title" id="title">Event Details</h2>
        <button class="EditBtn" onclick="alert('Edit feature is not available yet.');">Edit</button>
    </div>

    <form id="viewEvent" class="form" method="POST" action="#" onsubmit="return validateDates()" style="align-items: center;">
        <label id="lable" for="eventName">Event Name:</label> 
        <input name="eventName" class="form__input" type="text" placeholder="Name" value="<?php echo htmlspecialchars($eventData['EventName']); ?>" required readonly> <br>

        <label id="lable" for="eventLocation">Event Location:</label> 
        <input name="eventLocation" class="form__input" type="text" placeholder="Exhibition center, Riyadh" value="<?php echo htmlspecialchars($eventData['EventLocation']); ?>" required readonly> <br>

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
            <?php foreach ($halls as $hall): ?>
                <div id="hall" class="hall" style="margin-left: -30%">
                    <label for="hallName">Hall Name:</label><br>
                    <input id="HInput" name="hallName" class="form__input" type="text" placeholder="Main hall" value="<?php echo htmlspecialchars($hall['HallName']); ?>" required readonly><br>
                    <label for="cameraName">Camera:</label><br>
                    <input id="HInput" name="cameraName" class="form__input" type="text" value="<?php echo htmlspecialchars($hall['CameraName']); ?>" required readonly><br>
                    <label for="hallThreshold">Hall Threshold:</label><br>
                    <input id="HInput" name="hallThreshold" class="form__input" type="number" placeholder="##" value="<?php echo htmlspecialchars($hall['HallThreshold']); ?>" required readonly>
                </div>
            <?php endforeach; ?>
        </div>
        <br>
    </form>
    <button class="DeleteBtn" onclick="alert('Delete feature is not available yet.');">Delete Event</button>
</div>


    <script>
        function enableEditing() {
        document.querySelectorAll('.form__input').forEach(input => {
            input.removeAttribute('readonly');
        });
    }

    function validateDates() {
        const today = new Date();
        
        // Get Start Date and End Date values from the form
        const startDate = new Date(document.querySelector('input[name="startDate"]').value);
        const endDate = new Date(document.querySelector('input[name="endDate"]').value);
        
        // Validate: Start and End Date cannot be in the past
//        if (startDate < today) {
//            alert("Start Date cannot be in the past!");
//            return false;  // Prevent form submission
//        }

        if (endDate < today) {
            alert("End Date cannot be in the past!");
            return false;  // Prevent form submission
        }

        // Validate: Start Date must be before End Date
        if (startDate >= endDate) {
            alert("Start Date must be before End Date!");
            return false;  // Prevent form submission
        }

        return true;  // Allow form submission
    }
    </script>
</body>
</html>
