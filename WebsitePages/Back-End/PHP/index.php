<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="with=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../../Front-End/CSS/homeN.css">
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
        <title>Home</title>
        <style>
            .HeaderMain {
                font-family: Poppins;
                color: black;
                margin: 9%;
                margin-top: 11%;
                font-size: 130%;
                font-weight: 10;
            }

            .HeaderMain p {
                font-weight: 300;
            }

            nav ul li{
                list-style: none;
                display: inline-block;
                padding: 8px 12px;
                position: relative;
                margin-top: 3%;
            }

            .navbar.scrolled {
                margin-top: -2%;
            }

            .Header {
                min-height: 80vh;
                width: 100%;
                background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0) 70%, rgba(255, 255, 255, 0.954) 100%), url('../../images/Header1.png');
                background-size: 100%;
                position: relative;
                background-repeat: no-repeat;
            }

            .AboutUs {
                text-align: center;
                font-size: 100%;
                margin-top: 1%;
                margin-left: 10%;
                margin-right: 10%;
            }

            .usfelLinks ul {
                margin: 0px;
                padding: 0px;
            }


            .row-contact {
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
                margin-right: 90%;
                white-space: nowrap;

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

            .google-bg:hover {
                background: #bb5f03;
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

            .footer-widget ul li a:hover {
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

            .copyright-area {
                background: #202020;
                padding: 25px 0;
            }

            .copyright-text p {
                margin: 0;
                font-size: 14px;
                color: #878787;

            }

            .footer-menu li {
                display: inline-block;
                margin-left: 20px;
            }

            .footer-menu li:hover a {
                color: #3B5998;
            }

            .footer-menu li a {
                font-size: 14px;
                color: #878787;
            }

            button {
                width: 80px;
                height: 30px;
                border-radius: 5px;
                margin-top: 10px;
                margin-right: 22px;
                font-weight: 700;
                font-size: 14px;
                letter-spacing: 1.15px;
                background-color: #004aad;
                color: #f9f9f9;
                border: none;
                outline: none;
                align-self: flex-end;
                transition: 0.5s;
            }

            button:hover {
                box-shadow: 6px 6px 10px #d1d9e6, -6px -6px 10px #f9f9f9;
                transform: scale(0.985);
                transition: 0.25s;
                background-color: #013b87;
            }
            /* --------------- FOOTER ------------------ */
        </style>

        <script>
            var navLinks = document.getElementById("navLinks");

            function showMenu() {
                navLinks.style.right = "0";
            }

            function hideMenu() {
                navLinks.style.right = "-200px";
            }

            window.addEventListener("scroll", function() {
                var navbar = document.querySelector(".navbar");
                if (window.scrollY > 100) {
                    navbar.classList.add("scrolled");
                } else {
                    navbar.classList.remove("scrolled");
                }
            });
        </script>
    </head>

    <body>
        <header class="Header">
            <div class="navbar">
                <nav id="navLinks">
                    <i id="fa1" class="fa-solid fa-xmark" onclick="hideMenu()"></i>
                    <ul>
                        <li><a class="Hname" style="text-decoration:none" href="#AboutUs" accesskey="s">About Us</a></li>
                        <li><a class="Hname" style="text-decoration:none" href="#aaaallServices" accesskey="s">Solutions</a></li>
                        <li><a class="Hname" style="text-decoration:none" href="#contact-us" accesskey="y">Contact</a></li>
                        <li></li>
                        <a class="Hname" style="text-decoration:none" href="../../Front-End/HTML/login.html" accesskey="l"><button>Login</button></a>
                    </ul>
                </nav>
                <i id="fa2" class="fa-solid fa-bars" onclick="showMenu()"></i>
            </div>

            <div class="HeaderMain">
                <h1>WELCOME TO OUR COMPANY</h1>
                <a href="../../Back-End/PHP/index.php" accesskey="h"><img src="../../images/Logo2.png" alt="Raqeeb Logo" class="logo"></a>
                <h3>Transforming the way events are managed</h3>
                <p>a cutting-edge crowd management system designed to <br> revolutionize the way indoor events are organized
                    and <br> managed. Make them safer, more enjoyable, and more <br> efficient for both the organizer and
                    the attendees.</p>
                <a class="btn" href="#aaaallServices">Our Solutions</a>
            </div>
        </header>

        <main>
            <div class="mainContainer">
                <div id="AboutUs" class="AboutUs">
                    <h2>About Us</h2>
                    <p>We are dedicated to enhancing event experiences with innovative crowd management solutions. Our web application uses advanced computer vision to track and analyze visitor behavior, providing real-time insights and alerts to event organizers.</p>
                </div>

                <div id="aaaallServices">
                    <h2 class="textServices">Our Solutions</h2>

                    <div id="Service" class="row-Services">
                        <div class="course-col">
                            <img class="Simg" src="../../images/seo.gif" alt="Real-time">
                            <h3>Automated Crowd Monitoring</h2>
                                <p>Real-time tracking and counting of visitors using advanced computer vision.</p>
                        </div>
                        <div class="course-col">
                            <img class="Simg" src="../../images/analytics.gif" alt="Data Analytics">
                            <h3>Data-Driven Insights</h2>
                                <p>Analyze visitor behavior, movement patterns, and peak times for better event planning.</p>
                        </div>
                        <div class="course-col">
                            <img class="Simg" src="../../images/timer.gif" alt="Alerts">
                            <h3>Instant Alerts</h2>
                                <p>Receive real-time notifications on crowd density to ensure a safer and more organized event.</p>
                        </div>
                        <div class="course-col">
                            <img class="Simg" src="../../images/dashboard.gif" alt="Dashboard & Reports">
                            <h3>User-Friendly Dashboard & Reports</h2>
                                <p>Visualize trends, make informed decisions, and receive post-event summaries to improve future events.</p>
                        </div>
                    </div>
                </div>
                <div id="contact-us">
                    <div class="container a-container" id="a-container">
                        <!-- u need to reActivate it when hosted on the web -->
                        <form class="form" id="a-form" action="b0a7d8effe4beb48f2b0dfe6f7d90d9f" method="POST">
                            <h2 class="form_title title">Contact Us</h2>
                            <h4 style="text-align:center">Get in touch</h4>
                            <input name="cname" class="form__input" type="text" placeholder="Company Name" required>
                            <input name="email" class="form__input" type="email" placeholder="Email" required>
                            <input name="password" class="form__input" type="text" placeholder="Phone Number" required>
                            <textarea name="message" class="form__input" type="text" placeholder="Message" required id="result" onchange="auto_grow(this)" oninput="auto_grow(this)"></textarea> <br>
                            <input type="submit" name="submit" class="form__button button submit" id="button" value="Send Message">
                        </form>
                    </div>
                </div>

            <script>
                function auto_grow(element) {
                    element.style.height = "auto";
                    element.style.height = (element.scrollHeight)+"px";
                }

                function onload() {
                    auto_grow(document.getElementById("result"));
                }
            </script>
            </div>
        </main>

    <!-- ------------FOOTER------------- -->
           <footer class="footer-section">
               <div class="container">
                   <div class="footer-content pt-5 pb-5" style="margin-top: 4%;">
                       <div class="row">
                           <div class="col-xl-4 col-lg-4 mb-50">
                               <div class="footer-widget">
                                   <div class="footer-logo">
                                       <a href="../../Back-End/PHP/userHome.php"><img src="../../images/Logo3.png" class="img-fluid" alt="logo"></a>
                                   </div>
                                   <div class="footer-text">
                                       <p>Our system provides real-time crowd management, enabling event <br>organizers to track attendees and streamline operations efficiently.</p>
                                   </div>
                                   <div class="footer-social-icon">
                                   <span>Follow us</span>
                                       <a href="https://www.linkedin.com/company/raqeebai" target="blank_"><i class="fab fa-linkedin linkedin-bg"></i></a>
                                       <a href="https://x.com/Raqeeb_Ai" target="blank_"><i class="fa-brands fa-x-twitter twitter-bg"></i></a>
                                   </div>
                               </div>
                           </div>
                           <div class="col-xl-4 col-lg-4 col-md-6 mb-30" style="margin-left:30%;">
                               <div class="footer-widget">
                                   <div class="footer-widget-heading">
                                       <h3>Useful Links</h3>
                                   </div>
                                   <ul>
                                        <li><a href="#AboutUs">About</a></li>
                                        <li><a href="#aaaallServices">Solutions</a></li>
                                        <li><a href="#contact-us">Contact</a></li>

                                    </ul>
                               </div>
                               <div class="cta-text" style="padding: 0; ">
                                    <h4 style="margin-top: 25%; ">Mail us</h4>
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
