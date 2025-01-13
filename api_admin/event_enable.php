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
    // POST: Update an existing event
    $token = $_POST['token'] ?? '';

    if (empty($token) || !validateToken($conn, $token, $dbtableauth)) {
        http_response_code(401);
        echo json_encode(array("error" => "Token is required or invalid"));
        exit;
    }

    // Get uuid and enable details from POST request
    $uuid = $_POST['uuid'] ?? '';
    $enable = $_POST['enable'] ?? '';

    // Update the enable field for the event in the events table
    $updateStmt = $conn->prepare("UPDATE $dbtable SET enable = ? WHERE uuid = ?");
    $updateStmt->bind_param("ss", $enable, $uuid);

    if ($updateStmt->execute()) {
        if ($updateStmt->affected_rows > 0) {
            http_response_code(200);
            echo json_encode(array("success" => "Event updated successfully"));
        } else {
            http_response_code(404);
            echo json_encode(array("error" => "Event not found or no changes made"));
        }
    } else {
        http_response_code(500);
        echo json_encode(array("error" => "Error: " . $updateStmt->error));
    }
    $updateStmt->close();

} else {
    http_response_code(400);
    echo json_encode(array("error" => "Error: Only GET, POST, and PUT requests are allowed"));
}

$conn->close();
?>
