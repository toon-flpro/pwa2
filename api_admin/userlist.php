<?php
$config = include(__DIR__ . '/../api/config.php');
include(__DIR__ . '/../api/cors.php');

$dbservername = $config['db_host'];
$dbusername = $config['db_username'];
$dbpassword = $config['db_password'];
$dbdatabase = $config['db_database'];
$dbtable = "users";
$dbtableauth = "auth_admin";

// Connect to the database
$conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbdatabase);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(array("error" => "Connection failed: " . $conn->connect_error));
    exit;
}

// Validate token for authorization
function validateToken($conn, $token, $dbtableauth)
{
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
    $token = $_POST['token'] ?? '';

    if (empty($token) || !validateToken($conn, $token, $dbtableauth)) {
        http_response_code(401);
        echo json_encode(array("error" => "Token is required or invalid"));
        exit;
    }

    http_response_code(200);

    // Only select the specified fields from the users table
    $sqlUsers = "SELECT uuid, enable, email, birthday, name_en FROM $dbtable";
    $resultUsers = $conn->query($sqlUsers);

    $users = array();
    if ($resultUsers) {
        while ($row = $resultUsers->fetch_assoc()) {
            $users[] = $row;
        }
    } else {
        echo json_encode(array("error" => "Error fetching users: " . $conn->error));
    }

    echo json_encode(array("users" => $users));
} else {
    http_response_code(400);
    echo json_encode(array("error" => "Error: Only POST request is allowed"));
}

$conn->close();
