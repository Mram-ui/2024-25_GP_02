<?php
    include '../../Back-End/PHP/session.php'; 

    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "raqeebdb";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        echo json_encode(array("success" => false, "message" => "Database connection failed: " . $conn->connect_error));
        exit();
    }

    if (isset($_GET['cameraId'])) {
        $cameraId = $conn->real_escape_string($_GET['cameraId']);

        if (!is_numeric($cameraId)) {
            echo json_encode(array("success" => false, "message" => "Invalid camera ID."));
            exit();
        }

        $sql = "DELETE FROM camera WHERE CameraID = '$cameraId'";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("success" => false, "message" => "Error deleting camera: " . $conn->error));
        }
    } else {
        echo json_encode(array("success" => false, "message" => "Camera ID is missing."));
    }

    $conn->close();
?>
