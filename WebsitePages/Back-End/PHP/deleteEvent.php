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

    if (isset($_GET['eventId'])) {
        $eventId = $conn->real_escape_string($_GET['eventId']);

        if (!is_numeric($eventId)) {
            echo json_encode(array("success" => false, "message" => "Invalid Event ID."));
            exit();
        }

        $deleteHallsSQL = "DELETE FROM hall WHERE EventID = '$eventId'";

        if ($conn->query($deleteHallsSQL) !== TRUE) {
            echo json_encode(array("success" => false, "message" => "Error deleting halls: " . $conn->error));
            exit();
        }

        $sql = "DELETE FROM events WHERE EventID = '$eventId'";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(array("success" => true, "message" => "Event and associated halls deleted successfully."));
        } else {
            echo json_encode(array("success" => false, "message" => "Error deleting event: " . $conn->error));
        }
    } else {
        echo json_encode(array("success" => false, "message" => "Event ID is missing."));
    }

    $conn->close();
?>
