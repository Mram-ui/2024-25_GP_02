<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['CompanyID'])) { 
    // Allow access to login and signup pages
    if (basename($_SERVER['PHP_SELF']) !== 'login.php' && basename($_SERVER['PHP_SELF']) !== 'signup.php') {
        // Redirect to login page
        header('Location: ../../Front-End/HTML/login.html');
        exit();
    }
}
?>
