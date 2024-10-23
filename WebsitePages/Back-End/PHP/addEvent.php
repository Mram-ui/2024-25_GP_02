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
        .error-message {
            color: red;
            font-size: 0.9em;
            margin-top: 3px;
            margin-bottom: 4%;
        }
        .row {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;

        }

        #cameraInputs {
            display: flex;
            flex-direction: column;
        }

        .btn {
            width: 180px;
            height: 50px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 1.15px;
            box-shadow: 5px 5px 10px #d1d9e6, -5px -5px 10px #f9f9f9;
            border: none;
            outline: none;
            transition: 0.5s;
            cursor:pointer;
            color :white;
        }

        .btn-success {
            background-color: seagreen; 
        }
        .btn-danger {
            background-color: #FFD6D6;
            color: #F94141;
        }


        .btn:hover {
            box-shadow: 6px 6px 10px #d1d9e6, -6px -6px 10px #f9f9f9;
            transform: scale(0.985);
            transition: 0.25s;
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
            <form id="addEvent" class="form" method="POST" action="../../Back-End/PHP/addEvent.php" id="add_form" onsubmit="return validateDates()">
                <label for="eventName">Event Name:</label> 
                <input name="eventName" class="form__input" type="text" placeholder="Name" value="<?php echo isset($_POST['eventName']) ? htmlspecialchars($_POST['eventName']) : ''; ?>" required> <br>

                <label for="eventLocation">Event Location:</label> 
                <input name="eventLocation" class="form__input" type="text" placeholder="Exhibition center, Riyadh" value="<?php echo isset($_POST['eventLocation']) ? htmlspecialchars($_POST['eventLocation']) : ''; ?>" required> <br>

                <div id="times">
                    <div class="timeBlocks">
                        <label for="startDate">Start Date:</label>  
                        <input name="startDate" class="form__input time" type="date" value="<?php echo isset($_POST['startDate']) ? $_POST['startDate'] : ''; ?>" required> 
                    </div>
                    <div class="timeBlocks"> 
                        <label for="startTime">Start Time:</label> 
                        <input name="startTime" class="form__input time" type="time" value="<?php echo isset($_POST['startTime']) ? $_POST['startTime'] : ''; ?>" required> 
                    </div> 
                    <br>
                    <div class="timeBlocks">
                        <label for="endDate">End Date:</label>  
                        <input name="endDate" class="form__input time" type="date" value="<?php echo isset($_POST['endDate']) ? $_POST['endDate'] : ''; ?>" required> 
                    </div>
                    <div class="timeBlocks"> 
                        <label for="endTime">End Time:</label>  
                        <input name="endTime" class="form__input time" type="time" value="<?php echo isset($_POST['endTime']) ? $_POST['endTime'] : ''; ?>" required> 
                    </div>
                    <div class="error-message" id="startTimeError"></div>

                </div>



            

                <div class="AllHalls">
                    <div id="hall" class="hall">
                <div id="show_item">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                                <label for="hallName">Hall Name:</label><br>
                                <input name="hallName[]" class="form__input" type="text" placeholder="Main hall" value="<?php echo isset($_POST['hallName']) ? htmlspecialchars($_POST['hallName']) : ''; ?>" required>
                            </div>

                        <div class="col-md-3 mb-3" id="cameraInputs">       
                        <label for="hallCamera">Hall Camera:</label><br>

                        <select name="hallCamera[]" required>
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

                                $companyID = $_SESSION['CompanyID'];

                                $result = $conn->query("SELECT CameraID, CameraName FROM camera WHERE CompanyID='$companyID'");

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $selected = isset($_POST['hallCamera']) && $_POST['hallCamera'] == $row['CameraID'] ? 'selected' : '';
                                        echo "<option value='{$row['CameraID']}' $selected>{$row['CameraName']}</option>";
                                    }
                                }

                                $conn->close();
                            ?>
                        </select> 
                        <p class="addCameraLink">
                            <a href="addCamera.php">Don't have a camera?</a> <!-- would it be better if we write "can't see the camera you want? register a camera here -->
                        </p>
                    </div>
                        

                    <div class="col-md-3 mb-3">

                        <label for="hallThreshold">Hall Max Capacity:</label><br>
                        <input name="hallThreshold[]" class="form__input" type="number" placeholder="00" min="0" value="<?php echo isset($_POST['hallThreshold']) ? $_POST['hallThreshold'] : ''; ?>" required>
                    </div>
                    <br>
                    <div class="col-md-2 mb-3 d-grid">
                        <button class="btn btn-success add_item_btn"> Add new hall</button> 
                    </div>
                    </div>
                </div>
                <div>
                <button type="submit" class="form__button button submit">ADD EVENT</button>
                </div>
                </form>
                <br>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script>
                $(document).ready(function() {
                $(".add_item_btn").click(function(e) {
                e.preventDefault();
                $("#show_item").prepend(
                `   <div class="row">
                        <div class="col-md-4 mb-3">
                                <label for="hallName">Hall Name:</label><br>
                                <input name="hallName[]" class="form__input" type="text" placeholder="Main hall" value="<?php echo isset($_POST['hallName']) ? htmlspecialchars($_POST['hallName']) : ''; ?>" required>
                            </div>

                        <div class="col-md-3 mb-3" id="cameraInputs">       
                        <label for="hallCamera">Hall Camera:</label><br>

                        <select name="hallCamera[]" required>
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

                                $companyID = $_SESSION['CompanyID'];

                                $result = $conn->query("SELECT CameraID, CameraName FROM camera WHERE CompanyID='$companyID'");

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $selected = isset($_POST['hallCamera']) && $_POST['hallCamera'] == $row['CameraID'] ? 'selected' : '';
                                        echo "<option value='{$row['CameraID']}' $selected>{$row['CameraName']}</option>";
                                    }
                                }

                                $conn->close();
                            ?>
                        </select> 
                        <p class="addCameraLink">
                            <a href="addCamera.php">Don't have a camera?</a>
                        </p>
                    </div>
                        

                    <div class="col-md-3 mb-3">
                        <label for="hallThreshold">Hall Max Capacity:</label><br>
                        <input name="hallThreshold[]" class="form__input" type="number" placeholder="00" min="0" value="<?php echo isset($_POST['hallThreshold']) ? $_POST['hallThreshold'] : ''; ?>" required>
                    </div>
                    <br>
                    <div class="col-md-2 mb-3 d-grid">
                        <button class="btn btn-danger remove_item_btn">Remove</button> 
                    </div>
                    </div>`);
                });

                $(document).on('click', '.remove_item_btn', function(e){
                    e.preventDefault();
                    let row_item =$(this).parent().parent();
                    $(row_item).remove();


                });
              
                });

            </script>

        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('addEvent');
                const startDateInput = form.elements.startDate;
                const endDateInput = form.elements.endDate;
                const startTimeInput = form.elements.startTime;
                const startTimeError = document.getElementById('startTimeError');

                let lastStartDate = startDateInput.value;
                let lastEndDate = endDateInput.value;

                startDateInput.addEventListener('input', () => {
                    if (startDateInput.value !== lastStartDate) {
                        startTimeError.innerText = '';
                    }
                    lastStartDate = startDateInput.value;
                });

                endDateInput.addEventListener('input', () => {
                    if (endDateInput.value !== lastEndDate) {
                        if (startTimeError.innerText.trim() === "The event end date and time cannot be earlier than the start date and time! Please ensure the end date and time are after the start date and time.") {
                            startTimeError.innerText = '';
                        }
                    }
                    lastEndDate = endDateInput.value;
                });


                function isLocalStorageSupported() {
                    try {
                        const testKey = '__test__';
                        localStorage.setItem(testKey, testKey);
                        localStorage.removeItem(testKey);
                        return true;
                    } catch (e) {
                        return false;
                    }
                }

                function saveFormData() {
                    if (!isLocalStorageSupported()) return;

                    const formData = {
                        eventName: form.elements.eventName.value,
                        eventLocation: form.elements.eventLocation.value,
                        startDate: form.elements.startDate.value,
                        startTime: form.elements.startTime.value,
                        endDate: form.elements.endDate.value,
                        endTime: form.elements.endTime.value,
                        hallName: form.elements.hallName.value,
                        hallCamera: form.elements.hallCamera.value,
                        hallThreshold: form.elements.hallThreshold.value
                    };

                    localStorage.setItem('formData', JSON.stringify(formData));
                }

                function loadFormData() {
                    if (!isLocalStorageSupported()) return;

                    const savedFormData = localStorage.getItem('formData');
                    if (savedFormData) {
                        const formData = JSON.parse(savedFormData);
                        form.elements.eventName.value = formData.eventName;
                        form.elements.eventLocation.value = formData.eventLocation;
                        form.elements.startDate.value = formData.startDate;
                        form.elements.startTime.value = formData.startTime;
                        form.elements.endDate.value = formData.endDate;
                        form.elements.endTime.value = formData.endTime;
                        form.elements.hallName.value = formData.hallName;
                        form.elements.hallCamera.value = formData.hallCamera;
                        form.elements.hallThreshold.value = formData.hallThreshold;
                    }
                }

                loadFormData();

                window.addEventListener('beforeunload', saveFormData);

                form.addEventListener('submit', () => {
                    localStorage.removeItem('formData');
                });
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

            $CompanyID = $_SESSION['CompanyID'];

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $eventName = $_POST['eventName'];
                $eventLocation = $_POST['eventLocation'];
                $eventStartDate = $_POST['startDate'];
                $eventEndDate = $_POST['endDate'];
                $eventStartTime = $_POST['startTime'];
                $eventEndTime = $_POST['endTime'];
                //$hallName = $_POST['hallName'];
                //$hallThreshold = $_POST['hallThreshold'];
                //$hallCamera = $_POST['hallCamera'];

                $today = date('Y-m-d');
                $now = date('H:i');

                $validationPassed = true;

                if ($eventStartDate == $today) {
                    if ($eventStartTime <= $now) {
                        echo "<script> document.getElementById('startTimeError').innerText= 'For events scheduled today, the start time cannot be in the past! Please choose a valid time for today\'s event.';</script>";
                        $validationPassed = false;
                        exit;
                    }
                } elseif ($eventStartDate < $today) {
                    echo "<script> document.getElementById('startTimeError').innerText ='The event cannot start in the past! Please select a future start date and time.';</script>";
                    $validationPassed = false;
                    exit;
                }

                if ($eventStartDate > $eventEndDate || ($eventStartDate == $eventEndDate && $eventStartTime >= $eventEndTime)) {
                    echo "<script> document.getElementById('startTimeError').innerText ='The event end date and time cannot be earlier than the start date and time! Please ensure the end date and time are after the start date and time.';</script>";
                    $validationPassed = false;
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



                    foreach ($_POST['hallName'] as $key => $value) {
                        $stmt = $conn->prepare("INSERT INTO hall (HallName, HallThreshold, CameraID, EventID) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("siii", $value, $_POST['hallThreshold'][$key], $_POST['hallCamera'][$key], $eventID);
                        if (!$stmt->execute()) {
                            throw new Exception("Error inserting hall(s): " . $stmt->error);
                        }

                        }
                    
                    //$stmt = $conn->prepare("INSERT INTO hall (HallName, HallThreshold, CameraID, EventID) VALUES (?, ?, ?, ?)");
                    //$stmt->bind_param("siii", $hallName, $hallThreshold, $hallCamera, $eventID);

                   // if (!$stmt->execute()) {
                    //    throw new Exception("Error inserting hall(s): " . $stmt->error);
                   // }

                    $conn->commit();

                   echo "<script>
                        alert('New event added successfully!');
                        localStorage.removeItem('formData');

                    </script>";
                    exit;
//                    window.location.href = 'userHome.php';

                } catch (Exception $e) {
                    $conn->rollback();
                    echo "<script>alert('Failed to add event: " . $e->getMessage() . ". Please check the event details and try again.');</script>";
                }

                $stmt->close();
                $conn->close();
            }
        ?>
    </body>
</html>
