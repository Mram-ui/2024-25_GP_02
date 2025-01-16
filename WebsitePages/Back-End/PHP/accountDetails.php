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
?>

<!DOCTYPE html>
<html lang="es" dir="ltr">

    <head>
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="../../Front-End/CSS/accountDetails.css">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
        <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
        <title>Account Details</title>

        <style>
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
                margin-top: 1.5%;
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
                margin-left: -144%;
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
                margin-top: 13%;
            }
            
            #logoutModal {
                display: none;
                position: fixed;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
                background-color: white;
                border: 1px solid #ccc;
                padding: 20px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
                z-index: 1000;
            }

            .modal-buttons {
                display: flex;
                justify-content: space-between;
            }

            .modal-buttons button {
                padding: 10px 20px;
                cursor: pointer;
            }

            #overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 500;
            }

        .popup {
               display: none; 
               position: fixed;
               left: 0;
               top: 0;
               width: 100%;
               height: 100%;
               background-color: rgba(0, 0, 0, 0.5); 
               z-index: 1000; 
           }

           .popup-content {
               background-color: white;
               padding: 20px;
               border-radius: 10px;
               width: 300px;
               margin: 100px auto;
               text-align: center;
           }

           .logoutC, .cancelC {
               padding: 10px 20px;
               margin: 10px;
               cursor: pointer;
               background: blue;
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
               font-family: 'Montserrat', sans-serif;
           }

           .logoutC {
                background-color: #F94141;
                color: white;
           }

           .cancelC {
               background-color: darkgray;
               color: #f9f9f9;
           }

           .logoutC:hover {
                background-color: red;
                color: white;
           }

           .cancelC:hover {
               background-color: gray;
               color: #f9f9f9;
           }
           
            .profileLogo img {
                max-width: 100px;
                max-height: 100px;
                min-width: 100px;
                min-height: 100px;
            }
            
            .profileLogo {
                margin-left: 5%;
            }
                
            .logout {
                --black: #000000;
                --ch-black: #141414;
                --eer-black: #1b1b1b;
                --night-rider: #2e2e2e;
                --white: #ffffff;
                --af-white: #f3f3f3;
                --ch-white: #e1e1e1;
                display: flex;
                align-items: center;
                justify-content: flex-start;
                width: 60px;
                height: 40px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                position: relative;
                overflow: hidden;
                transition-duration: .3s;
                box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.199);
                background-color: #e10000;
              }

              .sign {
                width: 100%;
                transition-duration: .3s;
                display: flex;
                align-items: center;
                justify-content: center;
              }

              .sign svg {
                width: 17px;
              }

              .sign svg path {
                fill: var(--white);
              }
              
              .text {
                position: absolute;
                right: 0%;
                width: 0%;
                opacity: 0;
                color: var(--white);
                font-size: 1.1em;
                font-weight: 600;
                transition-duration: .3s;
                font-family: 'Montserrat', sans-serif;
              }
              
              .logout:hover {
                width: 130px;
                border-radius: 5px;
                transition-duration: .3s;
                background-color: #cc0000;
              }

              .logout:hover .sign {
                width: 30%;
                transition-duration: .3s;
                padding-left: 20px;
              }
              
              .logout:hover .text {
                opacity: 1;
                width: 70%;
                transition-duration: .3s;
                padding-right: 10px;
              }
              
              .logout:active {
                transform: translate(2px ,2px);
              }
       
        </style>

        <script>
            function confirmLogout(event) {
                event.preventDefault();
                document.getElementById('overlay').style.display = 'block';
                document.getElementById('logoutModal').style.display = 'block';
            }

            function logoutConfirmed() {
                console.log("Logging out...");
                window.location.href = '../../Back-End/PHP/logout.php';
            }

            function cancelLogout() {
                document.getElementById('overlay').style.display = 'none';
                document.getElementById('logoutModal').style.display = 'none';
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
            <nav></nav>
        </header>

        <?php
            include '../../Back-End/PHP/session.php';

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $CompanyID = $_SESSION['CompanyID'] ?? null;

            if ($CompanyID === null) {
                echo "<script>alert('No company ID found. Please log in again.'); window.location.href='../../Front-End/HTML/login.html';</script>";
                exit();
            }

            $servername = "localhost";
            $username = "root";
            $password = "root";
            $dbname = "raqeebdb";

            $connection = new mysqli($servername, $username, $password, $dbname);

            if ($connection->connect_error) {
                die("Connection failed: " . $connection->connect_error);
            }

            $query = 'SELECT Email, CompanyName, Logo FROM company WHERE CompanyID=' . $CompanyID;
            $row = mysqli_fetch_assoc(mysqli_query($connection, $query));

            if ($row) {
                $email = $row['Email'];
                $companyName = $row['CompanyName'];
                $logo = $row['Logo'];
            } else {
                echo "<script>alert('Company not found.'); window.location.href='../../Front-End/HTML/login.html';</script>";
                exit();
            }

            if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['Logout'])) {
                if (isset($_FILES['logo'])) {
                    $companyName = $_POST['companyName'];
                    $email = $_POST['email'];

                    if (file_exists($_FILES['logo']['tmp_name']) && is_uploaded_file($_FILES['logo']['tmp_name'])) {
                        $path_parts = pathinfo($_FILES["logo"]["name"]);
                        $extension = $path_parts['extension'];
                        $filenewname = $companyName . "_" . uniqid() . "." . $extension;
                        $folder = "../../images/" . $filenewname;

                        if (move_uploaded_file($_FILES['logo']['tmp_name'], $folder)) {
                            $logo = $filenewname;
                        } else {
                            $logo = $row['Logo'];
                        }
                    }
                    $stmt->close();
                } 
            }
        ?>

        <div class="main">
            <a id='arrow' href="../../Back-End/PHP/userHome.php"><i class="fa fa-chevron-left" style="color: #003f91; font-size: 30px; justify-self: end;"></i></a>
            <h2 class="title">Profile</h2>
            <button id="editButton"
                onclick="enableEditing()"
                type="button" 
                style="position: relative; border-radius: 6px; width: 150px; height: 40px; cursor: pointer; display: flex; align-items: center; border: 1px solid #007bff; background-color: #007bff; overflow: hidden; transition: all 0.3s; margin-left: 70%;"
                onmouseover="this.style.backgroundColor='#0056b3'; this.querySelector('.button__text').style.color='transparent'; this.querySelector('.button__icon').style.width='148px'; this.querySelector('.button__icon').style.transform='translateX(0)';"
                onmouseout="this.style.backgroundColor='#007bff'; this.querySelector('.button__text').style.color='#fff'; this.querySelector('.button__icon').style.width='39px'; this.querySelector('.button__icon').style.transform='translateX(109px)';"
                onmousedown="this.style.border='1px solid #004085'; this.querySelector('.button__icon').style.backgroundColor='#004085';"
                onmouseup="this.style.border='1px solid #007bff'; this.querySelector('.button__icon').style.backgroundColor='#0056b3';">

                <span id="edit"
                    class="button__text" 
                    style="transform: translateX(35px); color: #fff; font-weight: 600; transition: all 0.3s; font-size: 120%; margin-left: 2%; font-family: 'Montserrat', sans-serif;">
                    Edit
                </span>
                <span 
                    class="button__icon" 
                    style="position: absolute; transform: translateX(109px); height: 100%; width: 39px; background-color: #0056b3; display: flex; align-items: center; justify-content: center; transition: all 0.3s;">
                    <svg
                        style="width: 25px; color: white;" 
                        aria-hidden="true" 
                        xmlns="http://www.w3.org/2000/svg" 
                        viewBox="0 0 24 24" 
                        fill="none">
                        <path
                            stroke="currentColor" 
                            stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="1.3" 
                            d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                    </svg>
                </span>
            </button>

            <div class="profileLogo">
                <div class="profile">
                        <?php if (is_null($logo) || empty($logo)): ?>
                            <img src="../../images/CLogo.png" alt="Default User Logo">
                        <?php else: ?>
                            <img src="../../images/<?php echo $logo ?>" alt="User Company Logo">
                        <?php endif; ?>      
                </div>
                <h2><?php echo $companyName ?></h2>
            </div>

            <form id='addcam' class="form" method="POST" enctype="multipart/form-data">
                <label for="companyName">Company Name</label>
                <input name="companyName" class="form__input" type="text" value="<?php echo $companyName ?>" required readonly>
                <label for="email">Company Email</label>
                <input name="email" class="form__input" type="email" value="<?php echo $email ?>" required readonly>

                <div class="buttonss">
                    <div class="button">
                        <a  href="../../Back-End/PHP/changePassword.php"> <button style="width:185px; border-radius: 5px; font-family: 'Montserrat', sans-serif; margin-left: -7%;" class="reset"  id="resetPassword" >Change Password</button> </a>
                    </div>

                    <div class="button">
                        <button class="logout" id="LogoutBtn" onclick="confirmLogout(event)">
                            <div class="sign"><svg viewBox="0 0 512 512"><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path></svg></div>
                                <div class="text">Logout</div>
                       </button>
                    </div>
                </div>

            </form>
            
            

        <div id="logoutModal" style="
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
              <p style="color: black; font-size: 18px; font-weight: bold; margin: 0;">Logout?</p>
              <p style="color: gray; font-size: 14px; text-align: left; margin-top: 4%; margin-bottom: 2%;">Are you sure you want to log out?</p>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 20px;">
              <button id="cancelButton" onclick="cancelLogout()" style="
                background-color: #f0f0f0; 
                color: black; 
                border: none; 
                padding: 10px 20px; 
                border-radius: 6px; 
                cursor: pointer;
                width: 45%;">Cancel</button>
              <button id="confirmDeleteButton" onclick="logoutConfirmed()" style="
                background-color: #e50000; 
                color: white; 
                border: none; 
                padding: 10px 20px; 
                border-radius: 6px; 
                cursor: pointer;
                 width: 45%;">Logout</button>
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
            
            <script>
                const popup = document.getElementById('logoutModal');
                const overlay = document.getElementById('overlay');            
                const closePopup = document.getElementById('closePopup');

                const closePopupHandler = () => {
                  popup.style.display = 'none';
                  overlay.style.display = 'none';
                };
                        
                closePopup.addEventListener('click', closePopupHandler);
            </script>
            
            <!--   EDIT SCRIPT   -->
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const editButton = document.querySelector('#editButton');
                    const logoutButton = document.querySelector('#LogoutBtn');
                    const changePasswordButton = document.querySelector('#resetPassword');
                    const editSpan = document.querySelector('#edit');         
                    const inputs = document.querySelectorAll('.form__input');

                     // Track editing state
                    let isEditing = false;

                    editButton.addEventListener('click', function () {
                        if (!isEditing) {
                            // Enable editing mode
                            enableEditing();

                            // Hide buttons and change edit button text to SAVE
                            logoutButton.style.display = 'none';
                            changePasswordButton.style.display = 'none';
                            editSpan.innerText = 'Save';

                            isEditing = true;
                        } else {
                            // Save the changes (After EDIT)
                            saveChanges();

                            // Revert buttons to original state (DISPLAY)
                            logoutButton.style.display = 'block';
                            changePasswordButton.style.display = 'block';
                            editSpan.innerText = 'Edit';

                            isEditing = false;
                        }
                    });

                    function enableEditing() {
                        inputs.forEach(input => {
                            input.removeAttribute('readonly');
                            input.style.border = '1px solid white';
                            input.style.backgroundColor = '#f4f7ff';
                        });
                    }
                });
            </script>
    </body>
</html>
