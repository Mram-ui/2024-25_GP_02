<html lang="es" dir="ltr">

<head>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <meta charset="utf-8">
    <title>Add Event</title>
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
                <input name="hallName" class="form__input" type="text" placeholder="main hall" required><br>
                <label for="hallCamera">Hall Camera:</label><br>

                <!-- Camera Dropdown populated from database -->
                <select name="hallCamera" required>
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

                    // Fetch cameras from the database
                    $cameras = [];
                    $result = $conn->query("SELECT CameraID, CameraName FROM camera");

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $cameras[] = $row; // Add each camera to the array
                        }
                    }

                    // Populate the dropdown with cameras
                    foreach ($cameras as $camera): ?>
                        <option value="<?= $camera['CameraID']; ?>"><?= $camera['CameraName']; ?></option>
                    <?php endforeach; ?>
                </select> <br>

                <label for="hallThreshold">Hall Threshold:</label><br>
                <input name="hallThreshold" class="form__input" type="text" placeholder="##" required>
            </div>
            <div class="card">
                <a href="#"><img class="Plus" src="../../images/plus.png" alt="Plus"></a>
            </div>
        </div>
        <br>
        <button class="form__button button submit">ADD EVENT</button>
    </form>
</div>

<?php 
// DB connection (continued)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $eventName = $_POST['eventName'];
    $eventLocation = $_POST['eventLocation'];
    $eventStartDate = $_POST['startDate'];
    $eventEndDate = $_POST['endDate'];     
    $eventStartTime = $_POST['startTime']; 
    $eventEndTime = $_POST['endTime'];     

    $hallName = $_POST['hallName'];
    $hallThreshold = $_POST['hallThreshold'];
    $hallCamera = $_POST['hallCamera'];

    // Validate date and time
    $today = date('Y-m-d');
    if ($eventStartDate < $today || $eventEndDate < $today) {
        echo "<script>alert('Start date and end date cannot be in the past!');</script>";
        exit;
    }
    if ($eventStartDate > $eventEndDate) {
        echo "<script>alert('Start date cannot be after the end date!');</script>";
        exit;
    }

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Prepare and bind for the Events table
        $stmt = $conn->prepare("INSERT INTO events (EventName, EventLocation, EventStartDate, EventEndDate, EventStartTime, EventEndTime) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $eventName, $eventLocation, $eventStartDate, $eventEndDate, $eventStartTime, $eventEndTime);

        // Execute the statement for Events
        if (!$stmt->execute()) {
            throw new Exception("Error inserting event: " . $stmt->error);
        }

        // Get the last inserted EventID
        $eventID = $conn->insert_id;

        // Prepare and bind for the Halls table
        $stmt = $conn->prepare("INSERT INTO hall (HallName, HallThreshold, CameraID, EventID) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siii", $hallName, $hallThreshold, $hallCamera, $eventID);

        // Execute the statement for Halls
        if (!$stmt->execute()) {
            throw new Exception("Error inserting hall(s): " . $stmt->error);
        }

        // Commit the transaction
        $conn->commit();

        // Success alert
        echo "<script>alert('New event and hall(s) added successfully!');</script>";

        // Redirect to userHome.php
        echo "<script>window.location.href = 'userHome.php';</script>";
        exit;

    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        echo "<script>alert('Failed to add event and hall(s): " . $e->getMessage() . "');</script>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>






<script>
function validateDates() {
    const today = new Date();
    
    // Get Start Date and End Date values from the form
    const startDate = new Date(document.querySelector('input[name="startDate"]').value);
    const endDate = new Date(document.querySelector('input[name="endDate"]').value);
    
    // Validate: Start and End Date cannot be in the past
    if (startDate < today) {
        alert("Start Date cannot be in the past!");
        return false;  // Prevent form submission
    }

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


