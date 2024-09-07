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
        <title>Organizer</title>
        <style>  
            /* ---- Html Style ---- */
            *{
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
                margin: 0;
            }

            /* ---- new header style */
            .Header {
                min-height: 80vh;
                width: 100%;
                background-image: url('images/Header4.png');
                background-size: 100%;
                position: relative;
                background-repeat: no-repeat;
            }

            .navbar {
                display: flex;
                padding: 2% 6% 0% 6%;
                justify-content: space-between;
            }

            .navbar img.logo {
                width: 15%;
                transition: 0.5s;
            }

            /* .navbar img.logo:hover {
                width: 15.4%;
            } */

            nav {
                flex: 1;
                text-align: right;
                margin-top: 1%;
            }

            nav ul li{
                list-style: none;
                display: inline-block;
                padding: 8px 12px;
                position: relative;
            }

            nav ul li a {
                color: #fff;
                text-decoration: none;
                font-family: 'Poppins', sans-serif;
            }

            nav ul li::after {
                content: "";
                width: 0%;
                height: 2px;
                background: #f5574c;
                display: block;
                margin: auto;
                left: 0;
                bottom: -10px;
                transition: 0.2s;
            }

            nav ul li:hover::after {
                width: 100%;
            }

            nav ul li a:hover {
                color: #f7655a;
            }

            .navbar #fa1, #fa2 {
                display: none;
            }

            .HeaderMain {
                font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
                color: white;
                margin: 9%;
                margin-top: 10%;
                font-size: 130%;
            }

            .btn {
                margin-top: 3%;
                display: inline-block;
                padding: 0.6rem 1.5rem;
                font-size: 16px;
                font-weight: 700;
                color: white;
                border: 3px solid rgb(252, 70, 100);
                cursor: pointer;
                position: relative;
                background-color: transparent;
                text-decoration: none;
                overflow: hidden;
                z-index: 1;
                font-family: inherit;
            }

            .btn::before {
                content: "";
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgb(252, 70, 100);
                transform: translateX(-100%);
                transition: all .3s;
                z-index: -1;
            }

            .btn:hover::before {
                transform: translateX(0);
            }

            .mainContainer {
                box-sizing: border-box;
                width: 100%;
                height: 900px; /*---------------------------------BACK---------------*/
            }

            .vision {
                text-align: center;
                font-size: 100%;
                margin-top: 8%;
                margin-left: 15%;
                margin-right: 15%;
            }


            .vision h2 {
                font-size: 200%;
            }

            .vision p {
                padding-top: 1.1%;
                font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
                font-size: 110%;
                color: #777;
            }

            .EventsCard {
                font-family: 'Poppins', sans-serif;
                width: 80%;
                margin-top: 1%;
                text-align: center;
                padding-top: 8%;
                display: flex;
                justify-content: space-between;
            }

            .card {
                    position: relative;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 520px;
                    padding: 2px;
                    border-radius: 24px;
                    overflow: hidden;
                    line-height: 1.6;
                    transition: all 0.48s cubic-bezier(0.23, 1, 0.32, 1);
                    margin-left: 25%;
            }

         

                .content {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 24px;
                    padding: 34px;
                    border-radius: 22px;
                    color: #ffffff;
                    overflow: hidden;
                    background: #ffffff;
                    transition: all 0.48s cubic-bezier(0.23, 1, 0.32, 1);
                }

                .content .heading {
                    font-weight: 700;
                    font-size: 36px;
                    line-height: 1.3;
                    z-index: 1;
                    transition: all 0.48s cubic-bezier(0.23, 1, 0.32, 1);
                }

                .content .para {
                    z-index: 1;
                    opacity: 0.8;
                    font-size: 18px;
                    transition: all 0.48s cubic-bezier(0.23, 1, 0.32, 1);
                }

                .card::before {
                    content: "";
                    position: absolute;
                    height: 160%;
                    width: 160%;
                    border-radius: inherit;
                    background: #4942ce;
                    background: linear-gradient(to right, #4942ce, #4942ce   );
                    transform-origin: center;
                    animation: moving 4.8s linear infinite paused;
                    transition: all 0.88s cubic-bezier(0.23, 1, 0.32, 1);
                }

                .card:hover::before {
                    animation-play-state: running;
                    z-index: -1;
                    width: 20%;
                }

                .card:hover .content .heading,
                    .card:hover .content .para {
                    color: #000000;
                }

                .card:hover {
                    box-shadow: 0rem 6px 13px rgba(10, 60, 255, 0.1),
                        0rem 24px 24px rgba(10, 60, 255, 0.09),
                        0rem 55px 33px rgba(10, 60, 255, 0.05),
                        0rem 97px 39px rgba(10, 60, 255, 0.01), 0rem 152px 43px rgba(10, 60, 255, 0);
                    scale: 1.05;
                    color: #000000;
                }

                @keyframes moving {
                    0% {
                        transform: rotate(0);
                    }

                    100% {
                        transform: rotate(360deg);
                }
                }
                

            /* ---- Footer Style ---- */
            .footerContainer {
                box-sizing: border-box;
                background-color: #343434;
                width: 100%;
                margin: auto;
                padding: 70px 30px 20px;
            }

            .socialIcons{
                display: flex;
                justify-content: center;
            }

            .socialIcons a{
                text-decoration: none;
                padding: 10px;
                color: #111;
                background-color: white;
                margin: 10px;
                border-radius: 50%;
            } 

            .socialIcons a i{
                font-size: 2em;
                opacity: 0.9;
            }

            /* Hover Effects On Social Media Icons */
            .socialIcons a:hover{
                background-color: #111;
            }

            .socialIcons a:hover i{
                color: white;
            }

            .row {
                display: flex;
                flex-wrap: wrap;
            }

            footer {
                flex: 0 0 auto;
            }
            

            .footerNav ul {
                list-style: none;
            }

            .footerNav {
                margin: 30px;
                width: 25%;
                padding: 0 15px;
                padding-left: 25%;
            }

            .footerNav h4{
                font-size: 18px;
                color: #000;
                text-transform: capitalize;
                margin-bottom: 30px;    
                line-height: 1.5;
                position: relative;
                white-space: nowrap;
            }


            /* Hover Effects footerNav */
            .footerNav h4::before{
                content: '';
                position: absolute;
                left: 0;
                bottom: -10px;
                background-color: #a3a2a2;
                height: 2px;
                box-sizing: border-box;
                width: 50px;
            }


            .footerNav ul{
                justify-content: center;
                color: #bbbbbb;
                display: list-item;
                white-space: nowrap;
                margin: 20px;
                letter-spacing: 0.5px;
            }

            .footerNav ul li:not(:last-child){
                margin-bottom: 10px;
            }


            .footerNav ul li a{
                color: white;
                text-decoration: none;
                opacity: 0.7;
                text-transform: capitalize;
                color: #bbbbbb;
                display: block;
            }

            .footerNav a{
                position: relative;
                text-decoration: none;
                color: #bbbbbb;;
            }

            .footerNav a:after{
                content: '';
                position: absolute;
                background-color: white;
                height: 3px;
                width: 0;
                left: 0;
                bottom: -10px;
                transition: 0.3s;
            }

            .footerNav a:hover{
                color: white;
            }

            .footerNav a:hover:after {
                width: 100%;
            }

            .footerBottom{
                background-color: #111;
                padding: 20px;
                text-align: center;
            }

            .footerBottom p{
                color: white;
            }

            /* Responsive adjustments for Footer */
            @media (max-width: 768px) {
                .footerNav {
                    width: 100%;
                    padding-left: 0;
                    margin: 0;
                    text-align: center;
                }

                .footerNav ul {
                    margin: 0;
                }

                .footerNav h4::before {
                    width: 30px;
                }

                .footerNav a:after {
                    width: 50%;
                }

                .footerNav ul li {
                    margin-bottom: 15px;
                }

                .socialIcons a {
                    margin: 5px;
                }

                .footerBottom p {
                    font-size: 80%;
                }
            }
            /* ---- end footer style ---- */

    
           
            @media (max-width: 700px){
                nav ul li {
                    display: block;
                }

                nav {
                    position: absolute;
                    background-color: #f44336;
                    height: -200vh;
                    width: 200px;
                    top: 0;
                    right: -200px;
                    text-align: left;
                    z-index: 2;
                    transition: 1s;
                }

                .navbar #fa1, #fa2 {
                    display: block;
                    color: #fff;
                    margin: 10px;
                    font-size: 22px;
                    cursor: pointer;
                }

                nav ul {
                    padding: 30px;
                }

                .navbar img.logo {
                    width: 12%
                }
            }

            /* ---- end new header style */
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
        <header class="Header">
			<div class="navbar">
				<a href = "orgnaizer.php" accesskey="h"><img src= "images/Logo..png" alt= "Raqeeb Logo" class="logo"></a>
				<nav id="navLinks">
					<i id="fa1" class="fa-solid fa-xmark" onclick="hideMenu()"></i>
					<ul>
						<li><a class ="Hname" style="text-decoration:none" href = "orgnaizer.php" accesskey="s">COMPANY</a></li>
						<li><a class ="Hname" style="text-decoration:none" href = "orgnaizer.php" accesskey="y">LOGOUT</a></li>	
					</ul>
				</nav>
				<i id="fa2"class="fa-solid fa-bars" onclick="showMenu()"></i>
			</div>
            <div class="HeaderMain">
                <h1>Welcome to your <br> Event Dashboard!</h1>
                <p>Here, you can easily manage and add new events <br> to ensure a seamless experience for your attendees.</p>
                <a class="btn" href="#">ADD EVENT</a>
            <div/>
		</header>
		<main>
            <div class="mainContainer">
            <div class="vision">
					<h2>SOMETHING</h2>
					<p>Explore and utilize our intuitive tools to keep your event details organized and up-to-date, <br> ensuring smooth operations and a seamless experience for your attendees.</p>	
            </div>
            <div class="EventsCard">
                <a href="#" style="text-decoration: none;">
                    <div class="card">
                        <div class="content">
                            <!-- <img src="images/PastEvent.png" alt="Past Events Icon"> -->
                            <p class="heading">PAST EVENTS</p>
                            <p class="para">Review and analyze previous events with detailed summaries and historical data to inform future planning and improvements.<br><br></p>
                        </div>
                    </div>
                </a>
                <a href="#" style="text-decoration: none;">
                    <div class="card">
                        <div class="content">
                            <!-- <img src="images/CurrentEvent.png" alt="Current Event Icon"> -->
                            <p class="heading">CURRENT EVENTS</p>
                            <p class="para">Monitor and manage your ongoing event in real-time, with tools to oversee live data and make adjustments as needed for a successful experience.</p>
                        </div>
                    </div>
                </a>
            </div>

        </main>
        <footer>
			<div class="footerContainer">
			  <div class="row">
				<div class="footerNav">
				  <h4>EDUCATIONAL TOOLS</h4>
					<ul>
					  <li>Interactive Worksheets</li>
					  <li>Worksheets Generator</li>
					  <li>Guided Lessons</li>
					  <li>Progress Tracker</li>
					 </ul>
			  
				</div>
				<div class="footerNav">
                    <h4>CONTACT US</h4>
                        <ul>
                        <li>+996533310022</li>
                        <li>+99653331111</li>
                        <li>+0116767877</li>
                        <li><a href = "#">Raqeeb Team</a></li>
                        </ul>
                    </div>
                </div>	
                </div>
                <div class="footerBottom">
                <div class="socialIcons">
                    <a href="#" target="_blank"><i class="fa-brands fa-telegram"></i></a>
                    <a href="#" target="_blank"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" target="_blank"><i class="fa-brands fa-youtube"></i></a>
                </div>		
                <p>© 2024 Raqeeb. All rights reserved</p>	
                </div>	
		</footer>
    </body>
</html>
 
