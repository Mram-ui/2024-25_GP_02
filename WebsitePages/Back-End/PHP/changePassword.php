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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <title>Change Password</title>

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
                text-align: center;
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

              #forgetpass{
                height: 500px;
            }

            #logo {
                width: 30%;
                margin-top: 5%;
            }

            #newPassNotValid #oldPassNotValid {
                 color: #003f91;
                 font-size: 0.9em;
                 margin-right: 5%;
            }

            #passerr, #oldPassNotValid {
               color: red;
               font-size: 0.9em;
               margin-right: 7%; 
            }

            .button {
                width: 200px;
                height: 40px;
                border-radius: 8px;
                margin-top: 50px;
                font-weight: 600;
                font-size: 14px;
                letter-spacing: 1.15px;
                background-color: #004aad;
                color: #f9f9f9;
                box-shadow: 8px 8px 16px #d1d9e6, -8px -8px 16px #f9f9f9;
                border: none;
                outline: none;
                transition: 0.5s;
                align-self: center;
                cursor: pointer;
                font-family: 'Montserrat', sans-serif;
            }
            
            .button:hover {
                box-shadow: 6px 6px 10px #d1d9e6, -6px -6px 10px #f9f9f9;
                transform: scale(0.985);
                transition: 0.25s;
                background-color: #003f91;
            }

            .modal-container{
                background-color: rgba(0, 0, 0, 0.3) ;
                display: flex;
                align-items: center;
                justify-content: center;
                position: fixed;
                opacity: 0;
                pointer-events: none;
                top: 0;
                left: 0;
                height: 100vh;
                width: 100Vw;
                transition: opacity 0.3s ease;
            }

            .modal-container.show{
                pointer-events: auto;
                opacity: 1;
            }

            .modal {
                background-color: #fff;
                border-radius: 10px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) ;
                padding: 30px 50px;
                width: 500px;
                height: fit-content;
                padding: 2%;
                max-width: 100%;
                display: flex;
                flex-direction: column;
                text-align:center;
                align-items: center;
                justify-content: center;
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
        ?>

        <div class="main">
            <a id='arrow' href="../../Back-End/PHP/accountDetails.php"><i class="fa fa-chevron-left" style="color: #003f91; font-size: 30px; justify-self: end;"></i></a>
            <h2 class="title">Change Password</h2>

            <p style="font-size: 17px; text-align: center;">Please fill the form below to change your password</p>
            <br>
            <form class="form" id="resetPasswordForm">
                <input name="oldpass" id="oldpass" class="form__input"  type="password" placeholder="Enter Current Password" required>
                <div class="errorMessage" id="oldPassNotValid"></div>
                <br>
                <input name="newpass" id="newpass" class="form__input"  type="password" placeholder="Enter New Password" required>
                <div style="color: #0f4a91;" class="errorMessage" id="newPassNotValid"></div>
                <br>
                <input name="confirmpass" id="confirmpass" class="form__input"  type="password" placeholder="Re-Type New Password" required>
                <div id="passerr"></div>   
                <br>         
                <button type="submit" class="form__button button submit" style="margin-top: 2%" id="open">Update Password</button>
                <br>
            </form>
            <div id="changePassMsg"></div>

            <div class="modal-container" id="modal_container">
                <div class="modal">
                    <h3>Password Changed Successfully!</h3>
                    <br>
                    <a href='../../Back-End/PHP/accountDetails.php'> <button id="close" class="button"> OK </button> </a>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            const queryString = window.location.search;
            console.log(queryString);
            const urlParams = new URLSearchParams(queryString);
            var email = urlParams.get('email');

            const passwordInput = document.getElementById('newpass');
            const currentPassInput = document.getElementById('oldpass');
            const confirmPassInput = document.getElementById('confirmpass');

            currentPassInput.addEventListener('blur', function() {
                const oldPass = currentPassInput.value;
                $.ajax({
                    type: 'POST',
                    url: "checkCurrentPassword.php",
                    data: { oldPass: oldPass },
                    success: function(response) {
                        if (response === 'incorrect') {
                            document.getElementById('oldPassNotValid').innerText = 'Current password entered is incorrect, please try again';
                        } else {
                            document.getElementById('oldPassNotValid').innerText = ''; 
                        }
                    }
                });
            });

            currentPassInput.addEventListener('input', function() {
                document.getElementById('oldPassNotValid').innerText = '';
            });

            passwordInput.addEventListener('input', function() {
                const passwordValue = passwordInput.value;
                if (passwordValue.length < 8) {
                    document.getElementById('newPassNotValid').innerText = 'Password must be at least 8 characters long!';
                } else {
                    document.getElementById('newPassNotValid').innerText = ''; 
                }
            });

            confirmPassInput.addEventListener('blur', function() {
                const newPass = passwordInput.value;
                const confirmPass = confirmPassInput.value;
                if (newPass !== confirmPass) {
                    document.getElementById('passerr').innerText = 'Passwords do not match! Please try again';
                } else {
                    document.getElementById('passerr').innerText = '';  
                }
            });

            confirmPassInput.addEventListener('input', function() {
                document.getElementById('passerr').innerText = '';  
            });

            $(document).ready(function() {
                $("#resetPasswordForm").on('submit', function(e) {
                    e.preventDefault();
                    var oldpass = $("#oldpass").val();
                    var newpass = $("#newpass").val();
                    var confirmpass = $("#confirmpass").val();

                    if (newpass !== confirmpass) {
                        document.getElementById('passerr').innerText = 'Passwords do not match! Please try again';
                        return;  
                    }

                    if (document.getElementById('oldPassNotValid').innerText === '' && document.getElementById('newPassNotValid').innerText === '') {
                        $.ajax({
                            type: 'POST',
                            url: "resetpassword.php",
                            data: { oldpass: oldpass, newpass: newpass },
                            success: function(data) {
                                switch (data) {
                                    case '1':            
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
                                        icon.innerHTML = `
                                            <path
                                                clip-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                fill-rule="evenodd"
                                            ></path>
                                        `;
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
                                        heading.innerText = 'Password Changed';
                                        const checkmark = document.createElement('span');
                                        checkmark.classList.add('checkmark');
                                        checkmark.innerText = '✓';
                                        heading.appendChild(checkmark);

                                        const prompt = document.createElement('p');
                                        prompt.style.marginTop = '0.75rem';
                                        prompt.style.color = 'rgb(21 128 61)';
                                        prompt.style.lineHeight = '1.5';
                                        prompt.innerText = 'Password has been changed successfully!';

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
                                        closeButton.addEventListener('click', () => {
                                            document.body.removeChild(overlay);
                                            document.body.removeChild(notificationsContainer);  
                                            window.location.href = '../../Back-End/PHP/accountDetails.php';
                                        });

                                        buttonContainer.appendChild(closeButton);

                                        flexContainer.appendChild(iconContainer);
                                        flexContainer.appendChild(textContainer);
                                        successNotification.appendChild(flexContainer);
                                        successNotification.appendChild(buttonContainer);
                                        notificationsContainer.appendChild(successNotification);

                                        document.body.appendChild(overlay);
                                        document.body.appendChild(notificationsContainer);
                                        break;
                                    case '2':
                                        $("#changePassMsg").html('Could not change password, please try again');
                                        break;
                                    case '3':
                                        $("#oldPassNotValid").html('Current password entered is incorrect, please try again');
                                        break;
                                    default:
                                        $("#changePassMsg").html('Could not change password, please try again');
                                        break;
                                }
                            }
                        });
                    } else {
                        $("#changePassMsg").html('Please fill in all the fields correctly');
                    }
                });
            });
        </script>
    </body>
</html>
