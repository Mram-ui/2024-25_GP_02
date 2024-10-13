<?php
    // logout.php
    session_start();
    session_destroy();
    header("Location: ../../Back-End/PHP/index.php");
    exit();
?>
