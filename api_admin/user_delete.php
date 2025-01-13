<?php
$config = include(__DIR__ . '/../api/config.php');
include(__DIR__ . '/../api/cors.php');

$dbservername = $config['db_host'];
$dbusername = $config['db_username'];
$dbpassword = $config['db_password'];
$dbdatabase = $config['db_database'];
$dbtable = "users";
$dbtableauth = "auth";
$dbtableauth_admin = "auth_admin";

// Connect to the database
$conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbdatabase);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(array("error" => "Connection failed: " . $conn->connect_error));
    exit;
}

// Validate token for authorization
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

// Handle different request methods
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $uuid = $_POST['uuid'] ?? '';
    $email = $_POST['email'] ?? '';
    $token = $_POST['token'] ?? '';

    if (empty($uuid) || empty($email) || !validateToken($conn, $token, $dbtableauth_admin)) {
        http_response_code(401);
        echo json_encode(array("error" => "Invalid data or token"));
        exit;
    }

    // Delete the user from the users table
    $stmt = $conn->prepare("DELETE FROM $dbtable WHERE uuid = ?");
    $stmt->bind_param("s", $uuid);

    if ($stmt->execute()) {
        $stmt->close();

        // Delete the user from the auth table using email
        $stmtAuth = $conn->prepare("DELETE FROM $dbtableauth WHERE email = ?");
        $stmtAuth->bind_param("s", $email);

        if ($stmtAuth->execute()) {
            http_response_code(200);
            echo json_encode(array("message" => "User deleted successfully from both tables"));
        } else {
            http_response_code(500);
            echo json_encode(array("error" => "Error deleting user from auth table"));
        }
        $stmtAuth->close();
    } else {
        http_response_code(500);
        echo json_encode(array("error" => "Error deleting user from users table"));
    }

} else {
    http_response_code(400);
    echo json_encode(array("error" => "Only POST requests are allowed for user deletion"));
}

$conn->close();
?>
