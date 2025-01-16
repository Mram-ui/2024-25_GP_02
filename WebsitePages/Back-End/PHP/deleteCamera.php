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

    date_default_timezone_set('Asia/Riyadh');
    $now = new DateTime();

    if (isset($_GET['cameraId'])) {
        $cameraId = $conn->real_escape_string($_GET['cameraId']);

        if (!is_numeric($cameraId)) {
            echo json_encode(array("success" => false, "message" => "Invalid camera ID."));
            exit();
        }

        $checkHallSql = "
            SELECT h.HallID, e.EventEndDate, e.EventEndTime 
            FROM hall h
            INNER JOIN events e ON h.EventID = e.EventID
            WHERE h.CameraID = '$cameraId'";
        $result = $conn->query($checkHallSql);

        if ($result->num_rows > 0) {
            $canBeDeleted = true; 
            while ($row = $result->fetch_assoc()) {
                $endDate = new DateTime($row['EventEndDate'] . ' ' . $row['EventEndTime']);
                if ($endDate >= $now) {
                    $canBeDeleted = false;
                    break;
                }
            }

            if ($canBeDeleted) {
                $sql = "DELETE FROM camera WHERE CameraID = '$cameraId'";
                if ($conn->query($sql) === TRUE) {
                    echo json_encode(array("success" => true, "message" => "Camera deleted successfully."));
                } else {
                    echo json_encode(array("success" => false, "message" => "Error deleting camera: " . $conn->error));
                }
            } else {
                echo json_encode(array("success" => false, "message" => "The camera is connected to a current or upcoming event and cannot be deleted!"));
            }
        } else {
            $sql = "DELETE FROM camera WHERE CameraID = '$cameraId'";
            if ($conn->query($sql) === TRUE) {
                echo json_encode(array("success" => true, "message" => "Camera deleted successfully."));
            } else {
                echo json_encode(array("success" => false, "message" => "Error deleting camera: " . $conn->error));
            }
        }
    } else {
        echo json_encode(array("success" => false, "message" => "Camera ID is missing."));
    }

    $conn->close();
?>
