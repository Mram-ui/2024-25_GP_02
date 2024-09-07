<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="homeStylesheet.css">
    <script src="scriptHome.js"></script>
</head>

<body>

    <!-- Full-Screen Section with Background Image and Intro Text -->
    <section class="full-screen-section">
        <div class="background"></div>
        <div class="overlay"></div>

        <header class="header">
            <div class="logo">
                <a href="home.html"><img src="images/3.png" alt="Company Logo"></a>
            </div>
            <nav>
                <a href="#about-us">About Us</a>
                <a href="#company-services">Services</a>
                <a href="#contact">Contact Us</a>
                <a href="#signin" class="sign-in-button">Sign In</a>
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
        <h2>Our Solutions</h2>

        <div class="service">
            <h3>Automated Crowd Monitoring</h3>
            <p>Real-time tracking and counting of visitors using advanced computer vision.</p>
        </div>

        <div class="service">
            <h3>Data-Driven Insights</h3>
            <p>Analyze visitor behavior, movement patterns, and peak times for better event planning.</p>
        </div>

        <div class="service">
            <h3>Instant Alerts</h3>
            <p>Receive real-time notifications on crowd density to ensure a safer and more organized event.</p>
        </div>

        <div class="service">
            <h3>User-Friendly Dashboard & Reports</h3>
            <p>Visualize trends, make informed decisions, and receive post-event summaries to improve future events.</p>
        </div>
    </div>


    <div id="contact-us">
        <div class="container">
            <div class="row">
                <h2>Contact Us</h2>
            </div>
            <div class="row">
                <h4 style="text-align:center">Get in touch</h4>
            </div>
            <div class="row input-container">
                <div class="col-xs-12">
                    <div class="styled-input wide">
                        <input type="text" required />
                        <label>Name</label>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="styled-input">
                        <input type="text" required />
                        <label>Email</label>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="styled-input" style="float:right;">
                        <input type="text" required />
                        <label>Phone Number</label>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="styled-input wide">
                        <textarea required></textarea>
                        <label>Message</label>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="btn-lrg submit-btn">Send Message</div>
                </div>
            </div>
        </div>
    </div>






</body>

<footer class="footer">
    <div class="container">
        <a href="home.html"><img src="images/3.png" alt="Company Logo"></a>
        <p>&copy; 2024 Raqeeb. All rights reserved.</p>
        <p><a href="#about-us">About Us</a> | <a href="#company-services">Services</a> | <a href="#contact-us">Contact Us</a></p>
    </div>
</footer>


</html>
