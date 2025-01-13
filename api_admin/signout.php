<?php
$config = include(__DIR__ . '/../api/config.php');
include(__DIR__ . '/../api/cors.php');

$dbservername = $config['db_host'];
$dbusername = $config['db_username'];
$dbpassword = $config['db_password'];
$dbdatabase = $config['db_database'];
$dbtable = "auth_admin";

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

    $stmt = $conn->prepare("UPDATE $dbtable SET token = NULL WHERE token = ?");
    if (!$stmt) {
        http_response_code(response_code: 500);
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

    if ($stmt->affected_rows > 0) {
        http_response_code(200);
        echo json_encode(array("success" => "Successful"));
    } else {
        http_response_code(response_code: 401);
        echo json_encode(array("error" => "Invalid token or already signed out"));
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(400);
    echo json_encode(array("error" => "Error: Only POST requests are allowed"));
}
?>
