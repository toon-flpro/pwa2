<?php
$config = include(__DIR__ . '/../api/config.php');
include(__DIR__ . '/../api/cors.php');

$dbservername = $config['db_host'];
$dbusername = $config['db_username'];
$dbpassword = $config['db_password'];
$dbdatabase = $config['db_database'];
$dbtable = "events";
$dbtableauth = "auth_admin";

// Connect to the database
$conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbdatabase);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(array("error" => "Connection failed: " . $conn->connect_error));
    exit;
}

// Validate token for authorization
function validateToken($conn, $token, $dbtableauth) {
    $stmt = $conn->prepare("SELECT email FROM $dbtableauth WHERE token = ?");
    if ($stmt) {
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            return true; // Token is valid
        }
        $stmt->close();
    }
    return false; // Token is invalid or does not exist
}

// Handle different request methods
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // POST: Create a new event
    $token = $_POST['token'] ?? '';

    if (empty($token) || !validateToken($conn, $token, $dbtableauth)) {
        http_response_code(401);
        echo json_encode(array("error" => "Token is required or invalid"));
        exit;
    }

    // Get event details from POST request
    $uuid = $_POST['uuid'] ?? '';
    $title_en = $_POST['title_en'] ?? '';
    $title_ch = $_POST['title_ch'] ?? '';
    $img = $_POST['img'] ?? '';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $host_en = $_POST['host_en'] ?? '';
    $host_ch = $_POST['host_ch'] ?? '';
    $venue_en = $_POST['venue_en'] ?? '';
    $venue_ch = $_POST['venue_ch'] ?? '';
    $description_en = $_POST['description_en'] ?? '';
    $description_ch = $_POST['description_ch'] ?? '';
    $ticket_price = $_POST['ticket_price'] ?? '';
    $enable = $_POST['enable'] ?? '';

    // Insert new event into the events table
    $insertStmt = $conn->prepare("INSERT INTO $dbtable (uuid, title_en, title_ch, img, date, time, host_en, host_ch, venue_en, venue_ch, description_en, description_ch, ticket_price, enable) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insertStmt->bind_param("ssssssssssssss", $uuid, $title_en, $title_ch, $img, $date, $time, $host_en, $host_ch, $venue_en, $venue_ch, $description_en, $description_ch, $ticket_price, $enable);


    if ($insertStmt->execute()) {
        http_response_code(200);
        echo json_encode(array("success" => "Event created successfully"));
    } else {
        http_response_code(500);
        echo json_encode(array("error" => "Error: " . $insertStmt->error));
    }
    $insertStmt->close();

} else {
    http_response_code(400);
    echo json_encode(array("error" => "Error: Only GET, POST, and PUT requests are allowed"));
}

$conn->close();
?>
