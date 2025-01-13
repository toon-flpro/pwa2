<?php
include 'cors.php';
$config = include('config.php');

$dbservername = $config['db_host'];
$dbusername = $config['db_username'];
$dbpassword = $config['db_password'];
$dbdatabase = $config['db_database'];
$dbtable = "events";
$dbtableaccount = "users";
$dbtableauth = "auth";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['token'])) {
        http_response_code(400);
        echo json_encode(array("error" => "Error: Token is required"));
        exit;
    }

    $token = $_POST['token'];

    $conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbdatabase);

    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(array("error" => "Connection failed: " . $conn->connect_error));
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM $dbtableauth WHERE token = ?");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(array("error" => "Prepare statement failed: " . $conn->error));
        $conn->close();
        exit;
    }

    $stmt->bind_param("s", $token);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(array("error" => "Execute statement failed: " . $stmt->error));
        $stmt->close();
        $conn->close();
        exit;
    }

    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        http_response_code(200);
        
        // Events query with 'enable' filter
        $sqlEvents = "SELECT * FROM $dbtable WHERE enable = 1";
        $resultEvents = $conn->query($sqlEvents);
        
        $events = array();
        if ($resultEvents) {
            while ($row = $resultEvents->fetch_assoc()) {
                $events[] = $row;
            }
        } else {
            echo json_encode(array("error" => "Error fetching events: " . $conn->error));
        }

        // Get the current month
        $currentMonth = date("m");

        // Users query with 'enable' filter and add birthday field
        $sqlAccount = "SELECT name_en, name_ch, img, ancestralHome_en, ancestralHome_ch, currentLocation_en, currentLocation_ch, birthday 
                       FROM $dbtableaccount WHERE enable = 1";
        $resultAccount = $conn->query($sqlAccount);
        
        $users = array();
        if ($resultAccount) {
            while ($row = $resultAccount->fetch_assoc()) {
                // Convert birthday from Unix timestamp and check month
                $userBirthdayMonth = date("m", $row['birthday']);
                if ($userBirthdayMonth === $currentMonth) {
                    $users[] = $row;
                }
            }
        } else {
            echo json_encode(array("error" => "Error fetching user data: " . $conn->error));
        }

        echo json_encode(array("events" => $events, "users" => $users));

    } else {
        http_response_code(401);
        echo json_encode(array("error" => "Invalid token or already signed out."));
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(400);
    echo json_encode(array("error" => "Error: Only POST requests are allowed"));
}
?>
