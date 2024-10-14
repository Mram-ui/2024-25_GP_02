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

    $companyID = $_SESSION['CompanyID'];

    // // Fetch cameras from the database
    // $sql = "SELECT CameraID, CameraName FROM camera";
    // $result = $conn->query($sql);

        // Fetch events from the database for the specific company
    $sql = "SELECT CameraID, CameraName 
            FROM camera 
            WHERE CompanyID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $companyID); 
    $stmt->execute();
    $result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="with=device-width, initial-scale=1.0">
		<!--  Icons logo  -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" 
			integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" 
			crossorigin="anonymous" referrerpolicy="no-referrer">
			<link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
		<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css">
		<!--  Fonts Link  -->
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Acme&family=Tilt+Neon&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css2?family=ABeeZee&display=swap" rel="stylesheet">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500&display=swap" rel="stylesheet">
		<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">      
        <title>Cameras</title>
        <style>  
            /* ---- Html Style ---- */
            *{
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            html, body {
                height: 100%;
                margin: 0; 
            }
            
            body {
                display: flex;
                flex-direction: column;
                margin: 0;
                width: 100%;
                display: flex;
                flex-direction: column; 
            }

            /* ---- new header style */
            
            .header {
                position: fixed;
                z-index: 200;
                display: flex;
                justify-content: space-between;
                align-items: center;
                background-color: white;
                padding: 10px 40px;
                width: 100%;
                font-weight: bold;   
            }
          
            .header .logo img {
                height: 60px; 
                width: auto;  
            }

            /* .header nav img {
                width: 10%;
                margin-left: 85%;
                transition: 0.5s;
            }

            .header nav img:hover {
                width: 10.2%;
            } */

            /* ---- end new header style */

            main {
                background-color: #e9edf3;
                width: 100%;
            }
            
            .mainContainer {
                box-sizing: border-box;
                width: 100%;
                margin-top: 10%;
                margin-bottom: 10%;
                flex: 1; 
                overflow: auto; 
            }

            .Cards {
                display: flex;
                margin-left: 10%; 
            }

            .card {
                display: flex;
                width: 14.5%;
                height: 2%;
                border-radius: 20px;
                margin-bottom: 1%;
                padding: 20px 30px;
                box-sizing: border-box;
                position: relative; 
                overflow: hidden; 
                margin: 1%;
                background-color: #1e52a5; 
                transition: all 0.88s cubic-bezier(0.23, 1, 0.32, 1);
                white-space: nowrap;
                align-items: center;
                font-weight: Bold;

            }

            .card:hover {
                box-shadow: 0rem 6px 13px rgba(10, 60, 255, 0.1),
                    0rem 24px 24px rgba(10, 60, 255, 0.09),
                    0rem 55px 33px rgba(10, 60, 255, 0.05),
                    0rem 97px 39px rgba(10, 60, 255, 0.01), 0rem 152px 43px rgba(10, 60, 255, 0);
                scale: 1.02;
                background-color: #003f91;

            }

            .card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                border-radius: 10px;
                border: 2px solid transparent;
                background-origin: border-box;
                background-clip: content-box, border-box;
                animation: moving-border 3s linear infinite; 
                z-index: 1; 
            }


            @keyframes moving-border {
                0% {
                    background-position: 0% 0%;
                }
                100% {
                    background-position: 200% 0%;
                }
            }

            /* Ensure content is visible */
            .card * {
                position: relative;
                z-index: 2; 
            }
                
            .camera {
                z-index: 1;
                width: 100%;
            }

            .Plus {
                z-index: 100;
                width: 120%;
                margin-left: -40%;
                align-items: center;
                margin-top: 10%;
            }

            .text {
                display: flex;
                font-family: Poppins;
                margin-left: 1%;
                font-size: 130%;
                color: #112f5e;
                margin-left: 12%; 
            }

	    #AddEvent {
                color: white;
                margin-left: 8%;
            }

            .Addcamera {
                margin-left: 6%;
            }

            .listOfEvents h4 {
                font-family: Poppins;
                margin-left: 1%;
                margin-top: 3%;
                font-size: 130%;
                color: #112f5e;
                margin-left: 11%; 
            }

            .listOfEventsPCA {
                font-family: Poppins;
                margin-left: 2%;
                margin-top: 2%;
                font-size: 100%;
                color: #2e62b5;
                display: flex;
                margin-bottom: 2%;
                margin-left: 12%; 
            }

            .listOfEventsPCA .CurrentEvents {
                margin-left: 8%;
            }

            .listOfEventsPCA .UpcomingEvents {
                margin-left: 8%;
            }

            .PastEvents:hover {
                color: #1814F3;
            }

            .CurrentEvents:hover {
                color: #1814F3;
            }

            .UpcomingEvents:hover {
                color: #1814F3;
            }

            .PastEvents::after {
                content: "";
                width: 0%;
                height: 2px;
                background: rgb(53, 65, 242);
                display: block;
                margin: auto;
                left: 0;
                bottom: -10px;
                transition: 0.2s;
            }

            .PastEvents:hover::after {
                width: 100%;
            }

            .CurrentEvents::after {
                content: "";
                width: 0%;
                height: 2px;
                background: rgb(53, 65, 242);
                display: block;
                margin: auto;
                left: 0;
                bottom: -10px;
                transition: 0.2s;
            }

            .CurrentEvents:hover::after {
                width: 100%;
            }

            .UpcomingEvents::after {
                content: "";
                width: 0%;
                height: 2px;
                background: rgb(53, 65, 242);
                display: block;
                margin: auto;
                left: 0;
                bottom: -10px;
                transition: 0.2s;
            }

            .UpcomingEvents:hover::after {
                width: 100%;
            }

            .cardContainerEvents {
                width: 80%;
                border-radius: 20px;
                margin-bottom: 15%;
                padding: 30px 30px;
                box-sizing: border-box;
                position: relative; 
                overflow-y: auto; 
                margin: 1%;
                background-color: #ffffff; 
                transition: all 0.88s cubic-bezier(0.23, 1, 0.32, 1);
                box-shadow: 0rem 6px 13px rgba(10, 60, 255, 0.1);
                margin-left: 10%;
            }

           
            .cardContainerEvents::-webkit-scrollbar {
                width: 10px;
            }

            .cardContainerEvents::-webkit-scrollbar-thumb {
                background: rgba(10, 60, 255, 0.3);
                border-radius: 10px;
            }

            .cardContainerEvents::-webkit-scrollbar-track {
                background: rgba(240, 240, 240, 0.9);
            }


            .BreakLinePCU {
                color: #507abe0e;
                width: 76%;
                margin-top: -1%;
                margin-bottom: 2%;
                margin-left: 2%;
                margin-left: 12%; 
            }

            .EventsDetalis {
                font-family: Poppins;
                margin-left: 0%;
                margin-top: 0%;
                font-size: 100%;
                color: #2e62b5;
                display: flex;
                margin-bottom: 1%;
                font-weight: 300;
            }

            .EventsDetalis .EventDate {
                margin-left: 12%;
            }

            .EventsDetalis .Event {
                margin-left: 66%;
            }

            .BreakLine {
                color: #2e62b52c;
            }

	    .EventsDetalisDes {
                font-family: Poppins;
                margin-left: 1%;
                margin-top: 3%;
                margin-bottom: 3%;
                font-size: 100%;
                color: #232323;
                display: flex;
                font-weight: 300;
                justify-content: space-between;
            }

            .EventsDetalisDes .EventD {
                margin-left: auto;
                color: #232323;
                white-space: nowrap;
                margin-right: 20px
            }

            .EventsDetalisDes .edit {
                margin-left: 120%;
                width: 5%;
                transition: 0.5;
            }

        

         /* ---- Footer Style ---- */
            ul {
                margin: 0px;
                padding: 0px;
            }


            .row-contact
            {
                align-items: center;
                align-content: center;
                display: flex;
            }

            .row {
                display: flex;
                margin-left: 10%;
                margin-right: 10%;
            }

            .footer-section {
            background: #151414;
            position: relative;
            }
            .footer-cta {
            border-bottom: 1px solid #373636;
            margin-top: 5%;
            margin-bottom: 2%;
            display: flex;
            margin-left: 10%;
            margin-right: 10%;
            }

            .single-cta i {
            color: #1e52a5;
            font-size: 30px;
            margin-bottom: 15%;
            }


            .cta-text {
            padding-left: 15px;
            display: inline-block;
            }
            .cta-text h4 {
            color: #fff;
            font-size: 20px;
            font-weight: 600;
            }

            .cta-text span {
            color: #757575;
            font-size: 15px;
            }

            #mail a {
                color: #757575;
                transition: 0.25s;
                text-decoration: none;
            }

            #mail a:hover {
                color: #3B5998;
                transition: 0.25s;
            }


            .footer-content {
            position: relative;
            z-index: 2;
            }
            .footer-pattern img {
            position: absolute;
            top: 0;
            left: 0;
            height: 330px;
            background-size: cover;
            background-position: 100% 100%;
            }
            .footer-logo {
            margin-bottom: 0px;
            }
            .footer-logo img {
                max-width: 300px;
            }
            .footer-text p {
            margin-bottom: 10px;
            font-size: 14px;
            color: #7e7e7e;
            line-height: 28px;
            font-family: Poppins;
            margin-right: 35%;
            }
            .footer-social-icon span {
            color: #fff;
            display: block;
            font-size: 25px;
            font-weight: 700;
            margin-top: 5%;
            margin-bottom: 5px;
            }
            .footer-social-icon a {
            color: #fff;
            font-size: 16px;
            margin-right: 15px;
            }
            .footer-social-icon i {
            height: 40px;
            width: 40px;
            text-align: center;
            line-height: 38px;
            border-radius: 50%;
            margin-top: 1%;
            margin-bottom: 3%;
            transition: 0.25s;
            }

           
            
            .linkedin-bg {
                background: #0e76a8;
            }
           

            .twitter-bg {
                background: #000000;
            }


            .linkedin-bg:hover {
                background: #0c6590;
                width: 42px;
                height: 42px;
                transition: 0.25s;
                line-height: 40px;
            }

            .twitter-bg:hover {
                background: #504f4f;
                width: 42px;
                height: 42px;
                transition: 0.25s;
                line-height: 40px;
            }
           

            .footer-widget-heading h3 {
            color: #fff;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 40px;
            position: relative;
            }
            .footer-widget-heading h3::before {
            content: "";
            position: absolute;
            left: 0;
            bottom: -15px;
            height: 2px;
            width: 50px;
            background: #3B5998;
            }
            .footer-widget ul li {
            display: inline-block;
            width: 50%;
            margin-bottom: 12px;
            transition: 0.25s;
            text-decoration: none;
            }
            .footer-widget ul li a:hover{
            color: #3B5998;
            transition: 0.25s;
            }
            .footer-widget ul li a {
            color: #878787;
            text-transform: capitalize;
            }
            .subscribe-form {
            position: relative;
            overflow: hidden;
            }
            .subscribe-form input {
            width: 100%;
            padding: 14px 28px;
            background: #2E2E2E;
            border: 1px solid #2E2E2E;
            color: #fff;
            }
            .subscribe-form button {
                position: absolute;
                right: 0;
                background: #3B5998;
                padding: 13px 20px;
                border: 1px solid #3B5998;
                top: 0;    
                transition: 0.25s;
            }

            .subscribe-form button:hover {
                background: #264078;
                border: 1px solid #264078;
                transition: 0.25s;
            }

            .subscribe-form button i {
            color: #fff;
            font-size: 22px;
            transform: rotate(-6deg);
            }
            .copyright-area{
            background: #202020;
            padding: 25px 0;
            }
            .copyright-text p {
            margin: 0;
            font-size: 14px;
            color: #878787;
            
            }
            /* .copyright-text p a{
            color: #3B5998;
            } */
            .footer-menu li {
            display: inline-block;
            margin-left: 20px;
            }
            .footer-menu li:hover a{
            color: #3B5998;
            }
            .footer-menu li a {
            font-size: 14px;
            color: #878787;
            }
        /* --------------- FOOTER ------------------ */
           
            body {
                font-family: 'Montserrat', sans-serif;
            }

        /* --------------- USERACCOUNT ------------------ */
            .popup {
                --burger-line-width: 1.125em;
                --burger-line-height: 0.125em;
                --burger-offset: 0.625em;
                --burger-bg: #122fbd;
                --burger-color: #333;
                --burger-line-border-radius: 0.1875em;
                --burger-diameter: 3.125em;
                --burger-btn-border-radius: calc(var(--burger-diameter) / 2);
                --burger-line-transition: 0.3s;
                --burger-transition: all 0.1s ease-in-out;
                --burger-hover-scale: 1.1;
                --burger-active-scale: 0.95;
                --burger-enable-outline-color: var(--burger-bg);
                --burger-enable-outline-width: 0.125em;
                --burger-enable-outline-offset: var(--burger-enable-outline-width);
                /* nav */
                --nav-padding-x: 0.25em;
                --nav-padding-y: 0.625em;
                --nav-border-radius: 0.375em;
                --nav-border-color: #ccc;
                --nav-border-width: 0.0625em;
                --nav-shadow-color: rgba(0, 0, 0, 0.2);
                --nav-shadow-width: 0 1px 5px;
                --nav-bg: #eee;
                --nav-font-family: "Poppins", sans-serif;
                --nav-default-scale: 0.8;
                --nav-active-scale: 1;
                --nav-position-left: 0;
                --nav-position-right: unset;
                --nav-title-size: 0.625em;
                --nav-title-color: #777;
                --nav-title-padding-x: 1rem;
                --nav-title-padding-y: 0.25em;
                /* nav button */
                --nav-button-padding-x: 1rem;
                --nav-button-padding-y: 0.375em;
                --nav-button-border-radius: 0.375em;
                --nav-button-font-size: 17px;
                --nav-button-hover-bg: #2e44b1;
                --nav-button-hover-text-color: #fff;
                --nav-button-distance: 0.875em;
                /* underline */
                --underline-border-width: 0.0625em;
                --underline-border-color: #ccc;
                --underline-margin-y: 0.3125em;
            }

            /* popup settings */

            .popup {
            display: inline-block;
            text-rendering: optimizeLegibility;
            position: relative;
            }

            .popup input {
            display: none;
            }

            .burger {
            display: flex;
            position: relative;
            align-items: center;
            justify-content: center;
            background: var(--burger-bg);
            width: var(--burger-diameter);
            height: var(--burger-diameter);
            border-radius: var(--burger-btn-border-radius);
            border: none;
            cursor: pointer;
            overflow: hidden;
            transition: var(--burger-transition);
            outline: var(--burger-enable-outline-width) solid transparent;
            outline-offset: 0;
            }
            
            .burger:hover {
            transform: scale(var(--burger-hover-scale));
            }

            .burger:active {
            transform: scale(var(--burger-active-scale));
            }

            .burger:focus:not(:hover) {
            outline-color: var(--burger-enable-outline-color);
            outline-offset: var(--burger-enable-outline-offset);
            }

            .popup input:checked + .burger span:nth-child(1) {
            top: 50%;
            transform: translateY(-50%) rotate(45deg);
            }

            .popup input:checked + .burger span:nth-child(2) {
            bottom: 50%;
            transform: translateY(50%) rotate(-45deg);
            }

            .popup input:checked + .burger span:nth-child(3) {
            transform: translateX(
                calc(var(--burger-diameter) * -1 - var(--burger-line-width))
            );
            }

            .popup input:checked ~ nav {
            transform: scale(var(--nav-active-scale));
            visibility: visible;
            opacity: 1;
            }
            
            .NoCameraF {
                margin-top: 3%;
                text-align: center;
                font-family: Poppins;
            }
            
            a .EventD{
                text-decoration: none;
            }
            
            .EventD::after {
                content: "";
                width: 0%;
                height: 2px;
                background: #4a56ff;
                display: block;
                margin: auto;
                left: 0;
                bottom: -10px;
                transition: 0.2s;
            }

            .EventD:hover::after {
                width: 100%;
            }

            .EventD:hover {
                color: #4a56ff;
            }
            
             
         
            .headerlinks li {
                text-align: left;
                margin-left: -135%;
                list-style: none;
                display: inline-block;
                padding: 8px 12px;
                position: relative;
                color: black;
                font-weight: lighter;
            }

            .headerlinks li a {
                color: #504f4f;
                text-decoration: none;
                font-family: 'Poppins', sans-serif;
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


            /* ---- End  style */

        </style> 
        <script>
            var navLinks = document.getElementById("navLinks");

            function showMenu() {
                navLinks.style.right= "0";
            }

            function hideMenu() {
                navLinks.style.right= "-200px";
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
            <?php
            $query = 'SELECT Logo FROM company WHERE CompanyID=' . $companyID;
            $row = mysqli_fetch_assoc(mysqli_query($conn, $query));
            $logo = $row['Logo'];

            // $sql = "SELECT Logo FROM company WHERE CompanyID=' . $CompanyID";
            // $result = $conn->query($sql);

            ?>

            <!-- <a href="../../Back-End/PHP/accountDetails.php"><img src="../../images/user.png" alt="userCompany"></a> -->
            <label class="popup"> <input type="checkbox" />
            <a href="../../Back-End/PHP/accountDetails.php"><img src="../../images/<?php echo $logo ?>" style="width: 60px; height:60px; border-radius: 50%;" ></a>

                <!-- <div tabindex="0" class="burger">
                    <a href="../../Back-End/PHP/accountDetails.php">
                        <svg
                            viewBox="0 0 24 24"
                            fill="white"
                            height="20"
                            width="20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12 2c2.757 0 5 2.243 5 5.001 0 2.756-2.243 5-5 5s-5-2.244-5-5c0-2.758 2.243-5.001 5-5.001zm0-2c-3.866 0-7 3.134-7 7.001 0 3.865 3.134 7 7 7s7-3.135 7-7c0-3.867-3.134-7.001-7-7.001zm6.369 13.353c-.497.498-1.057.931-1.658 1.302 2.872 1.874 4.378 5.083 4.972 7.346h-19.387c.572-2.29 2.058-5.503 4.973-7.358-.603-.374-1.162-.811-1.658-1.312-4.258 3.072-5.611 8.506-5.611 10.669h24c0-2.142-1.44-7.557-5.631-10.647z"></path>
                        </svg>
                    </a>
                </div> -->
            </label>
        </nav>
    </header>
        
	<main>
            <div class="mainContainer">
                <div class="text">
                </div>
                <div class="Cards">
                    <div class="card">
                        <a href="../../Back-End/PHP/addCamera.php"><img class="Plus" src="../../images/plus.png" alt="Plus"></a>
                        <a href="../../Back-End/PHP/addCamera.php" style="text-decoration: none;"><h4 id="AddEvent">ADD CAMERA</h4></a>
                    </div>
                </div>
                <div id="listOfEvents" class="listOfEvents">
                    <h4>Cameras</h4> 
                </div><br>
                <hr class="BreakLinePCU">
                <div class="cardContainerEvents">
                    <div class="EventsDetalis">
                        <p class="EventName">Camera Name</p>
                    </div>
                    <hr class="BreakLine">

                    <?php if ($result->num_rows > 0): ?>
                        <?php 
                            $cameras = $result->fetch_all(MYSQLI_ASSOC); 
                            $totalCameras = count($cameras); 
                            foreach ($cameras as $index => $row):
                        ?>
                            <div class="EventsDetalisDes">
                                <p class="EventNameD"><?= htmlspecialchars($row['CameraName']); ?></p>
                                <div class="eventLinks" style="text-align: right;"> <!-- Added inline style for text alignment -->
                                    <a href="../../Back-End/PHP/viewEditCameras.php?cameraId=<?= $row['CameraID']; ?>" style="text-decoration: none;">
                                        <p class="EventD">View Details</p>
                                    </a>                    
                                </div>
                            </div>
                            <?php if ($index < $totalCameras - 1):?>
                                <hr class="BreakLine">
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="NoCameraF">No cameras have been added yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main> 
       <!-- ------------FOOTER------------- -->
       <footer class="footer-section">
        <div class="container">
            <!-- <div class="footer-cta pt-5 pb-5">
                    <div class="row">
                        <div class="row-contact">
                            <div class="col-xl-4 col-md-4 mb-30">
                                <div class="single-cta">
                                    <i class="far fa-envelope-open"></i>
                                    <div class="cta-text">
                                        <h4>Mail us</h4>
                                        <span id="mail"><a href="mailto:Raqeeb.Project@gmail.com">Raqeeb.Project@gmail.com</a></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
            <div class="footer-content pt-5 pb-5" style="margin-top: 4%;">
                <div class="row">
                    <div class="col-xl-4 col-lg-4 mb-50">
                        <div class="footer-widget">
                            <div class="footer-logo">
                                <a href="../../Back-End/PHP/userHome.php"><img src="../../images/Logo3.png" class="img-fluid" alt="logo"></a>
                            </div>
                            <div class="footer-text">
                                <p>Our system provides real-time crowd management, enabling event organizers to track
                                    attendees and streamline operations efficiently.</p>
                            </div>
                            <div class="footer-social-icon">
                            <span>Follow us</span>
                                <!-- <a href="#"><i class="fab fa-linkedin linkedin-bg"></i></a>
                                <a href="#"><i class="fab fa-twitter twitter-bg"></i></a> -->
                                <a href="https://www.linkedin.com/company/raqeebai" target="blank_"><i class="fab fa-linkedin linkedin-bg"></i></a>
                                <a href="https://x.com/RaqeebProject" target="blank_"><i class="fa-brands fa-x-twitter twitter-bg"></i></a>

                                <!-- <a href="mailto:Raqeeb.Project@gmail.com"><i class="fa fa-envelope" style="background-color: #1e52a5; border-radius: 50%; z-index: 0;"></i></a> -->
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6 mb-30">
                        <div class="footer-widget">
                            <div class="footer-widget-heading">
                                <h3>Useful Links</h3>
                            </div>
                            <ul class="usfelLinks">
                                <li style="white-space: nowrap;"><a href="../../Back-End/PHP/addEvent.php">Add Event</a></li>
                                <li style="white-space: nowrap;"><a href="#listOfEvents">List Of Events</a></li>
                                <!-- <li><a href="#" style="color: #151414;">Contact</a></li>
                                <li><a href="#" style="color: #151414;">Contact</a></li>
                                <li><a href="#" style="color: #151414;">Contact</a></li>
                                <li><a href="#" style="color: #151414;">Contact</a></li> -->

                            </ul>
                        </div>
                        <div class="cta-text" style="padding: 0; ">
                                        <h4 style="margin-top: 43%; ">Mail us</h4>
                                        <span id="mail"><a href="mailto:Raqeeb.Project@gmail.com">Raqeeb.Project@gmail.com</a></span>
                                    </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="copyright-area">
            <div class="container">
                <div class="row">
                    <div class="col-xl-6 col-lg-6 text-center text-lg-left">
                        <div class="copyright-text">
                            <p>Copyright &copy; 2024 Raqeeb, All Right Reserved </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    </body>
</html>

<?php
    $conn->close();
?>
 
