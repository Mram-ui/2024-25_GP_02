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
    <script>
        var navLinks = document.getElementById("navLinks");

        function showMenu() {
            navLinks.style.right = "0";
        }

        function hideMenu() {
            navLinks.style.right = "-200px";
        }
    </script>
</head>

<body>
    <header class="Header">
        <div class="navbar">
            <nav id="navLinks">
                <i id="fa1" class="fa-solid fa-xmark" onclick="hideMenu()"></i>
                <ul>
                    <li><a class="Hname" style="text-decoration:none" href="#AboutUs" accesskey="s">About Us</a></li>
                    <li><a class="Hname" style="text-decoration:none" href="#Service" accesskey="s">Service</a></li>
                    <li><a class="Hname" style="text-decoration:none" href="#contact-us" accesskey="y">Contact</a></li>
                    <li><a class="Hname" style="text-decoration:none" href="signup.php" accesskey="s">SignUp</a></li>
                </ul>
            </nav>
            <i id="fa2" class="fa-solid fa-bars" onclick="showMenu()"></i>
        </div>

        <div class="HeaderMain">
            <h1>WELCOME TO OUR COMPANY</h1>
            <a href="#" accesskey="h"><img src="../../images/Logo2.png" alt="Raqeeb Logo" class="logo"></a>
            <h3>Transforming the way events are managed</h3>
            <p>a cutting-edge crowd management system designed to <br> revolutionize the way indoor events are organized
                and <br> managed. Make them safer, more enjoyable, and more <br> efficient for both the organizer and
                the attendees.</p>
            <a class="btn" href="#Service">Our Services</a>
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
        <!-- <h3>Real-time Crowd Monitoring</h3>
        <p>AI-powered insights on attendee numbers and movements.</p> -->
        <h3>Automated Crowd Monitoring</h2>
            <p>Real-time tracking and counting of visitors using advanced computer vision.</p>
    </div>
    <div class="course-col">
        <img class="Simg" src="../../images/timer.gif" alt="Alerts">
        <!-- <h3>Alerts and Notifications</h3>
        <p>Immediate alerts for overcrowding <br> and safety risks.</p> -->
        <h3>Data-Driven Insights</h2>
            <p>Analyze visitor behavior, movement patterns, and peak times for better event planning.</p>
    </div>
    <div class="course-col">
        <img class="Simg" src="../../images/analytics.gif" alt="Data Analytics">
        <!-- <h3>Data Analytics</h3>
        <p>Detailed analytics on crowd behavior and event performance.</p> -->
        <h3>Instant Alerts</h2>
            <p>Receive real-time notifications on crowd density to ensure a safer and more organized event.</p>
    </div>
    <div class="course-col">
        <img class="Simg" src="../../images/dashboard.gif" alt="Dashboard & Reports">
        <!-- <h3>Data Analytics</h3>
        <p>Detailed analytics on crowd behavior and event performance.</p> -->
        <h3>User-Friendly Dashboard & Reports</h2>
            <p>Visualize trends, make informed decisions, and receive post-event summaries to improve future events.</p>
    </div>
</div>

            </div>



            <div id="contact-us">
                <div class="container a-container" id="a-container">
                    <form class="form" id="a-form" action="../../Back-End/PHP/signup.php" method="POST">
                        <h2 class="form_title title">Contact Us</h2>
                        <h4 style="text-align:center">Get in touch</h4>
                        <input name="cname" class="form__input" type="text" placeholder="Company Name" required>
                        <input name="email" class="form__input" type="email" placeholder="Email" required>
                        <input name="password" class="form__input" type="text" placeholder="Phone Number" required>
                        <textarea name="message" class="form__input" type="text" placeholder="Message" required></textarea> <br>
                        <input type="submit" name="submit" class="form__button button submit" id="button" value="Send Message">
                    </form>
                </div>
            </div>



        </div>
    </main>

    <!-- ------------FOOTER------------- -->
    <footer class="footer-section">
        <div class="container">
            <div class="footer-cta pt-5 pb-5">
                <div class="row">
                    <div class="row-contact">
                        <div class="col-xl-4 col-md-4 mb-30">
                            <div class="single-cta">
                                <i class="far fa-envelope-open"></i>
                                <div class="cta-text">
                                    <h4>Mail us</h4>
                                    <span><a href="mailto:Raqeeb.Project@gmail.com" style="text-decoration: none; color: #757575;">Raqeeb.Project@gmail.com</a></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-content pt-5 pb-5">
                <div class="row">
                    <div class="col-xl-4 col-lg-4 mb-50">
                        <div class="footer-widget">
                            <div class="footer-logo">
                                <a href="index.php"><img src="../../images/Logo3.png" class="img-fluid" alt="logo"></a>
                            </div>
                            <div class="footer-text">
                                <p>Our system provides real-time crowd management, enabling event organizers to track
                                    attendees and streamline operations efficiently.</p>
                            </div>
                            <div class="footer-social-icon">
                                <span>Follow us</span>
                                <a href="#"><i class="fab fa-facebook-f facebook-bg"></i></a>
                                <a href="#"><i class="fab fa-twitter twitter-bg"></i></a>
                                <a href="href="mailto:Raqeeb.Project@gmail.com"><i class="fab fa-google-plus-g google-bg"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6 mb-30">
                        <div class="footer-widget">
                            <div class="footer-widget-heading">
                                <h3>Useful Links</h3>
                            </div>
                            <ul>
                                <li><a href="#AboutUs">About</a></li>
                                <li><a href="#Service">Services</a></li>
                                <li><a href="#contact-us">Contact</a></li>
                                <li><a href="#" style="color: #151414;">Contact</a></li>
                                <li><a href="#" style="color: #151414;">Contact</a></li>
                                <li><a href="#" style="color: #151414;">Contact</a></li>
                                <li><a href="#" style="color: #151414;">Contact</a></li>

                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6 mb-50">
                        <div class="footer-widget">
                            <div class="footer-widget-heading">
                                <h3>Subscribe</h3>
                            </div>
                            <div class="footer-text mb-25">
                                <p>Don’t miss to subscribe to our new feeds, kindly fill the form below.</p>
                            </div>
                            <div class="subscribe-form">
                                <form action="#">
                                    <input type="text" placeholder="Email Address">
                                    <button><i class="fab fa-telegram-plane"></i></button>
                                </form>
                            </div>
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
                    <!-- <div class="col-xl-6 col-lg-6 d-none d-lg-block text-right">
                            <div class="footer-menu">
                                <ul>
                                    <li><a href="#">Home</a></li>
                                    <li><a href="#">Terms</a></li>
                                    <li><a href="#">Privacy</a></li>
                                    <li><a href="#">Policy</a></li>
                                    <li><a href="#">Contact</a></li>
                                </ul>
                            </div>
                        </div> -->
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
