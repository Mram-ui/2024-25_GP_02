<?php
    session_start();
    
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
?>

<html lang="es" dir="ltr">

    <head>
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
        <meta charset="utf-8">
        <title>Add Event</title>
        <link rel="stylesheet" type="text/css" href="../../Front-End/CSS/addEvent.css">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
        <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
        <style>
            body {
                width: 100%;
                height: fit-content;
                display: flex;
                justify-content: center;
                align-items: center;
                font-family: 'Montserrat', sans-serif;
                font-size: 15px;
                background-color: #ecf0f3;
                color: #718089;
                display: inline-flex;
                flex-direction: column;
                text-align:left;
                justify-content: space-between;
                text-align: left;
                background-color: #e9edf3; 
            }

            .main {
                position: relative;
                width: 45%;
                min-width: 80%;
                min-height: fit-content;
                height: 78%;
                padding: 25px;
                background-color: #eaeef2;
                box-shadow: 10px 10px 10px #d1d9e6, -10px -10px 10px #f9f9f9;
                border-radius: 12px;
                overflow: hidden;
                align-self: center;
                margin-bottom: 3%;
                margin-top: 10%;
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                text-align: left;
            }

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
            
            .headerTitle {
                margin-top: 5%;
            }
            
            #show_item {
                display: block;
            }
            
            .error-messageForCamera {
                color: red;
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

        <div class="main">
            <a id='arrow' href="../../Back-End/PHP/userHome.php">
                <i class="fa fa-chevron-left" style="color: #003f91; font-size: 30px;"></i>
            </a>
            <h2 class="title">Add Event</h2>
            <form id="addEvent" class="form" method="POST" action="../../Back-End/PHP/addEvent.php">
                <h3>Event Information</h3>
                <label for="eventName">Event Name:</label>
                <input name="eventName" class="form__input" type="text" placeholder="Name" value="<?php echo isset($_POST['eventName']) ? htmlspecialchars($_POST['eventName']) : ''; ?>" required>

                <label for="eventLocation">Event Location:</label>
                <input style="margin-bottom: 5%;" name="eventLocation" class="form__input" type="text" placeholder="Exhibition center, Riyadh" value="<?php echo isset($_POST['eventLocation']) ? htmlspecialchars($_POST['eventLocation']) : ''; ?>" required>

                <h3>Dates and Time</h3>
                <div id="times">
                    <div id="startTimes">
                        <div class="timeBlocks">
                            <label for="startDate">Start Date:</label>
                            <input name="startDate" class="form__input time" type="date" value="<?php echo isset($_POST['startDate']) ? $_POST['startDate'] : ''; ?>" required>
                        </div>
                        <div class="timeBlocks">
                            <label for="startTime">Start Time:</label>
                            <input name="startTime" class="form__input time" type="time" value="<?php echo isset($_POST['startTime']) ? $_POST['startTime'] : ''; ?>" required>
                        </div>
                    </div>
                    <div id="endTimes">
                        <div class="timeBlocks">
                            <label for="endDate">End Date:</label>
                            <input name="endDate" class="form__input time" type="date" value="<?php echo isset($_POST['endDate']) ? $_POST['endDate'] : ''; ?>" required>
                        </div>
                        <div class="timeBlocks">
                            <label for="endTime">End Time:</label>
                            <input name="endTime" class="form__input time" type="time" value="<?php echo isset($_POST['endTime']) ? $_POST['endTime'] : ''; ?>" required>
                        </div>
                    </div>
                </div>
                <div class="error-message" id="startTimeError"></div>

                <div id="addHalls">
                    <h3>Halls</h3>
                    <button id="addHallButton" class="btn btn-success add_item_btn" type="button">Add New Hall</button>
                </div>

                <div class="AllHalls" id="show_item">
                    <div class="hall row">
                        <div class="mb-3">
                            <label for="hallName">Hall Name:</label><br>
                            <input name="hallName[]" class="form__input" type="text" placeholder="Main hall" required>
                        </div>
                        <div style="margin-right: 1%;" class="mb-3">
                            <label for="hallCamera">Hall Camera:</label>
                            <select class="cameraSelection" name="hallCamera[]" required>
                                <option value="" disabled selected>Select your camera</option>
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
                            </select>
                            <p class="addCameraLink">Can't see the camera you want? <a href="addCamera.php">Register a camera.</a></p>
                        </div>
                        <div style="margin-right: 4.3%;" class="mb-3">
                            <label for="hallThreshold">Hall Max Capacity:</label><br>
                            <input name="hallThreshold[]" class="form__input" type="number" placeholder="00" min="0" required>
                        </div>
                        <div class="mb-3 d-grid">
                            <br>
                        </div>
                    </div>
                </div>
                <div>
                    <br>
                    <div class="error-messageForCamera"></div>
                    <button type="submit" class="form__button button submit">ADD EVENT</button>
                </div>
            </form>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script>
                $(document).ready(function() {
                    $(".add_item_btn").click(function(e) {
                        e.preventDefault();
                        const newHallRow = `
                            <div class="hall row" style="margin-top: 10px;">
                                <div class="mb-3">
                                    <label style="margin-right: 1%;" for="hallName">Hall Name:</label><br>
                                    <input name="hallName[]" class="form__input" type="text" placeholder="Main hall" required>
                                </div>
                                <div style="margin-right: 1%;" class="mb-3">
                                    <label for="hallCamera">Hall Camera:</label>
                                    <select class="cameraSelection" name="hallCamera[]" required>
                                        <option value="" disabled selected>Select your camera</option>
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
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="hallThreshold">Hall Max Capacity:</label><br>
                                    <input name="hallThreshold[]" class="form__input" type="number" placeholder="00" min="0" required>
                                </div>
                                <div class="mb-3 d-grid">
                                    <br>
                                    <input type="image" src="../../images/close.png" class="btn btn-danger remove_item_btn"> 
                                </div>
                            </div>
                        `;
                        $("#show_item").append(newHallRow);
                    });

                    $(document).on('click', '.remove_item_btn', function(e) {
                        e.preventDefault();
                        $(this).closest('.hall').remove();
                    });
                });
            </script>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('addEvent');
                const startDateInput = form.elements.startDate;
                const endDateInput = form.elements.endDate;
                const startTimeError = document.getElementById('startTimeError');
                const cameraError = document.querySelector('.error-messageForCamera');

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

                function saveEventData() {
                    if (!isLocalStorageSupported()) return;

                    const eventData = {
                        eventName: form.elements.eventName.value,
                        eventLocation: form.elements.eventLocation.value,
                        startDate: form.elements.startDate.value,
                        startTime: form.elements.startTime.value,
                        endDate: form.elements.endDate.value,
                        endTime: form.elements.endTime.value,
                    };

                    localStorage.setItem('eventData', JSON.stringify(eventData));
                }

                function saveHallData() {
                    if (!isLocalStorageSupported()) return;

                    const hallName = form.querySelector('input[name="hallName[]"]').value;
                    const hallCamera = form.querySelector('select[name="hallCamera[]"]').value;
                    const hallThreshold = form.querySelector('input[name="hallThreshold[]"]').value;

                    const hallData = {
                        hallName: hallName,
                        hallCamera: hallCamera,
                        hallThreshold: hallThreshold
                    };

                    localStorage.setItem('hallData', JSON.stringify(hallData));
                }

                function loadEventData() {
                    if (!isLocalStorageSupported()) return;

                    const savedEventData = localStorage.getItem('eventData');
                    if (savedEventData) {
                        const eventData = JSON.parse(savedEventData);
                        form.elements.eventName.value = eventData.eventName;
                        form.elements.eventLocation.value = eventData.eventLocation;
                        form.elements.startDate.value = eventData.startDate;
                        form.elements.startTime.value = eventData.startTime;
                        form.elements.endDate.value = eventData.endDate;
                        form.elements.endTime.value = eventData.endTime;
                    }
                }

                function loadHallData() {
                    if (!isLocalStorageSupported()) return;

                    const savedHallData = localStorage.getItem('hallData');
                    if (savedHallData) {
                        const hallData = JSON.parse(savedHallData);
                        form.querySelector('input[name="hallName[]"]').value = hallData.hallName;
                        form.querySelector('select[name="hallCamera[]"]').value = hallData.hallCamera;
                        form.querySelector('input[name="hallThreshold[]"]').value = hallData.hallThreshold;
                    }
                }

                function validateCameraSelection() {
                    const cameraSelects = document.querySelectorAll('.cameraSelection');
                    const selectedCameras = new Set();
                    let hasDuplicate = false;

                    cameraSelects.forEach(select => {
                        if (selectedCameras.has(select.value)) {
                            hasDuplicate = true;
                        } else {
                            selectedCameras.add(select.value);
                        }
                    });

                    if (hasDuplicate) {
                        cameraError.innerText = "The same camera cannot be selected for multiple halls!";
                        return false;
                    } else {
                        cameraError.innerText = "";
                        return true;
                    }
                }

                document.querySelectorAll('select[name="hallCamera[]"]').forEach(select => {
                    select.addEventListener('change', validateCameraSelection);
                });

                loadEventData();
                loadHallData();

                window.addEventListener('beforeunload', () => {
                    saveEventData();
                    saveHallData();
                });

                form.addEventListener('submit', (event) => {
                    if (!validateCameraSelection()) {
                        event.preventDefault();
                    } else {
                        localStorage.removeItem('eventData');
                        localStorage.removeItem('hallData');
                    }
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
                    echo "<script> document.getElementById('startTimeError').innerText = 'The event end date and time cannot be earlier than the start date and time! Please ensure the end date and time are after the start date and time.';</script>";
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
                    
                    $conn->commit();

                    echo "<script type='text/javascript'>                                         
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
                    icon.innerHTML = `<path clip-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z' fill-rule='evenodd'></path>`;
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
                    heading.innerText = 'Event Added';
                    const checkmark = document.createElement('span');
                    checkmark.classList.add('checkmark');
                    checkmark.innerText = '✓';
                    heading.appendChild(checkmark);
                
                    const prompt = document.createElement('p');
                    prompt.style.marginTop = '0.75rem';
                    prompt.style.color = 'rgb(21 128 61)';
                    prompt.style.lineHeight = '1.5';
                    prompt.innerText = 'Event has been added successfully!';
                
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
    
    
                   closeButton.addEventListener('click', () => {
                        localStorage.removeItem('eventData');
                        localStorage.removeItem('hallData');

                        setTimeout(() => {
                            window.location.href = '../../Back-End/PHP/userHome.php';
                        }, 100);    
                    });

                    document.body.appendChild(overlay);
                    document.body.appendChild(notificationsContainer);
                    </script>";
                    
            
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