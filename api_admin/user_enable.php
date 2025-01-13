<?php
// Connect to the database and include necessary configurations
$config = include(__DIR__ . '/../api/config.php');
include(__DIR__ . '/../api/cors.php');

$dbservername = $config['db_host'];
$dbusername = $config['db_username'];
$dbpassword = $config['db_password'];
$dbdatabase = $config['db_database'];
$dbtable = "users";
$dbtableauth_admin = "auth_admin";

$conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbdatabase);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(array("error" => "Connection failed: " . $conn->connect_error));
    exit;
}

// Function to validate token
function validateToken($conn, $token, $dbtableauth_admin) {
    $stmt = $conn->prepare("SELECT email FROM $dbtableauth_admin WHERE token = ?");
    if ($stmt) {
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            return true;
        }
        $stmt->close();
    }
    return false;
}

// Handle POST request for updating only enable field
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST') {
    $uuid = $_POST['uuid'] ?? '';
    $enable = $_POST['enable'] ?? null;
    $token = $_POST['token'] ?? '';
    echo '>>'.$uuid.' | ';
    echo '>>'.$enable.' | ';
    echo '>>'.$token.' | ';
    if (empty($uuid) || !validateToken($conn, $token, $dbtableauth_admin)) {
        http_response_code(401);
        echo json_encode(array("error" => "Invalid uuid or token"));
        exit;
    }

    // Update only enable field in users table using uuid
    $stmt = $conn->prepare("UPDATE $dbtable SET enable=? WHERE uuid=?");
    $stmt->bind_param("ss", $enable, $uuid);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(array("message" => "User enable status updated successfully"));
    } else {
        http_response_code(500);
        echo json_encode(array("error" => "Error updating enable status in users table"));
    }
    $stmt->close();

} else {
    http_response_code(400);
    echo json_encode(array("error" => "Only POST requests are allowed for user update"));
}

$conn->close();
?>
