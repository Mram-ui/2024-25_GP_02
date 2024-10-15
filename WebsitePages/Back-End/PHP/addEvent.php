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
                        // DB connection
                        $servername = "localhost"; 
                        $username = "root";
                        $password = "root";
                        $dbname = "raqeebdb";

                        // Create connection
                        $conn = new mysqli($servername, $username, $password, $dbname);

                        // Check connection
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // Fetch cameras from the database
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
    // DB connection
    $servername = "localhost"; 
    $username = "root";
    $password = "root";
    $dbname = "raqeebdb";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Assuming CompanyID is stored in the session after user login
    $CompanyID = $_SESSION['CompanyID']; // Change this according to how you store it in the session

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

        // Get current date and time
        $today = date('Y-m-d');
        $now = date('H:i'); // Current time

        // Check if the event start date is today
        if ($eventStartDate == $today) {
            // If the event is today, compare the start time
            if ($eventStartTime <= $now) {
                echo "<script>alert('The start time for today cannot be in the past!');</script>";
                exit;
            }
        } elseif ($eventStartDate < $today) {
            // If the start date is in the past, show an alert
            echo "<script>alert('Start date cannot be in the past!');</script>";
            exit;
        }

        // Check if start date is after the end date
        if ($eventStartDate > $eventEndDate || ($eventStartDate == $eventEndDate && $eventStartTime >= $eventEndTime)) {
            echo "<script>alert('Start date and time cannot be after the end date and time!');</script>";
            exit;
        }


        // Start a transaction
        $conn->begin_transaction();

        try {
            // Insert into Events table
            $stmt = $conn->prepare("INSERT INTO events (EventName, EventLocation, EventStartDate, EventEndDate, EventStartTime, EventEndTime, CompanyID) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssi", $eventName, $eventLocation, $eventStartDate, $eventEndDate, $eventStartTime, $eventEndTime, $CompanyID);

            // Execute Events insertion
            if (!$stmt->execute()) {
                throw new Exception("Error inserting event: " . $stmt->error);
            }

            // Get the last inserted EventID
            $eventID = $conn->insert_id;

            // Insert into Halls table
            $stmt = $conn->prepare("INSERT INTO hall (HallName, HallThreshold, CameraID, EventID) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siii", $hallName, $hallThreshold, $hallCamera, $eventID);

            // Execute Hall insertion
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
    const now = new Date(); // Get the current date and time
    
    // Get Start Date and End Date values from the form
    const startDate = new Date(document.querySelector('input[name="startDate"]').value);
    const startTime = document.querySelector('input[name="startTime"]').value;
    const endDate = new Date(document.querySelector('input[name="endDate"]').value);
    
    // Validate: Start Date cannot be in the past
    if (startDate.setHours(0, 0, 0, 0) < now.setHours(0, 0, 0, 0)) {
        alert("Start Date cannot be in the past!");
        return false;  // Prevent form submission
    }

    // If the Start Date is today, validate the time
    if (startDate.setHours(0, 0, 0, 0) === now.setHours(0, 0, 0, 0)) {
        const [startHours, startMinutes] = startTime.split(':');
        const selectedStartTime = new Date();
        selectedStartTime.setHours(startHours, startMinutes, 0, 0);

        if (selectedStartTime <= now) {
            alert("Start time for today's event must be in the future!");
            return false;  // Prevent form submission
        }
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
