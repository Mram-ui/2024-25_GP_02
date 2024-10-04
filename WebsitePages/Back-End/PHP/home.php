<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="..\..\Front-End\CSS\homeStylesheet.css">
    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- font-->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
    <script src="../../Front-End/JavaScript/scriptHome.js"></script>
</head>

<body>

    <!-- Full-Screen Section with Background Image and Intro Text -->
    <section class="full-screen-section">
        <div class="background"></div>
        <div class="overlay"></div>

        <header class="header">
            <div class="logo">
                <a href="home.html"><img src="../../images/Logo2.png" alt="Company Logo"></a>
            </div>
            <nav>
                <a href="#about-us">About Us</a>
                <a href="#company-services">Services</a>
                <a href="#contact-us">Contact Us</a>
                <a href="signup.php" class="sign-in-button">Sign In</a>
            </nav>
        </header>

        <div class="intro-text">
            <h1>Welcome to Raqeeb</h1>
            <p>Unlocking Value with AI</p>
        </div>
    </section>



    <!-- Content Section below the background -->

    <div id="about-us">
        <h2>About Us</h2>
        <p>We are dedicated to enhancing event experiences with innovative crowd management solutions. Our web application uses advanced computer vision to track and analyze visitor behavior, providing real-time insights and alerts to event organizers.</p>
        <p>Our goal is to help organizers create safer, more organized events by offering automated monitoring, data-driven insights, and user-friendly tools. With our solutions, you can ensure a smoother and more enjoyable experience for all attendees.</p>
    </div>

    <div id="company-services">
        <h2 class="textServices">Our Solutions</h2>

        <div id="all-services">
            <div class="service">
                <img src="../../images/cat.png" alt="service 1">
                <h3>Automated Crowd Monitoring</h2>
                    <p>Real-time tracking and counting of visitors using advanced computer vision.</p>
            </div>
            <div class="service">
                <img src="../../images/cat.png" alt="service 2">
                <h3>Data-Driven Insights</h2>
                    <p>Analyze visitor behavior, movement patterns, and peak times for better event planning.</p>
            </div>
            <div class="service">
                <img src="../../images/cat.png" alt="service 3">
                <h3>Instant Alerts</h2>
                    <p>Receive real-time notifications on crowd density to ensure a safer and more organized event.</p>
            </div>
            <div class="service">
                <img class="Simg" src="images/dashboard.gif" alt="Dashboard & Reports">
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







</body>

<footer class="footer">
    <div class="container">
        <a href="home.html"><img src="../../images/Logo2.png" alt="Company Logo"></a>
        <p>&copy; 2024 Raqeeb. All rights reserved.</p>
        <p><a href="#about-us">About Us</a> | <a href="#company-services">Services</a> | <a href="#contact-us">Contact Us</a></p>
    </div>
    <div class="footerBottom">
        <div class="socialIcons">
            <a href="#" target="_blank"><i class="fa-brands fa-telegram"></i></a>
            <a href="#" target="_blank"><i class="fa-brands fa-twitter"></i></a>
            <a href="#" target="_blank"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" target="_blank"><i class="fa-brands fa-youtube"></i></a>
        </div>
    </div>
</footer>


</html>
