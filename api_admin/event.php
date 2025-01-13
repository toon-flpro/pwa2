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
    $uuid = $_POST['uuid'] ?? '';
    $token = $_POST['token'] ?? '';

    if (empty($uuid) || empty($token) || !validateToken($conn, $token, $dbtableauth)) {
        http_response_code(401);
        echo json_encode(array("error" => "Invalid UUID or token"));
        exit;
    }
    $stmt = $conn->prepare("SELECT * FROM $dbtable WHERE uuid = ?");
    $stmt->bind_param("s", $uuid);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $event = $result->fetch_assoc();
            http_response_code(200);
            echo json_encode($event);
        } else {
            http_response_code(404);
            echo json_encode(array("error" => "Event not found"));
        }
    } else {
        http_response_code(500);
        echo json_encode(array("error" => "Error retrieving event"));
    }
    $stmt->close();

} else {
    http_response_code(400);
    echo json_encode(array("error" => "Error: Only GET, POST, and PUT requests are allowed"));
}

$conn->close();
?>
