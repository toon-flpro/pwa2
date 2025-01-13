<?php
include 'cors.php';

$config = include('config.php');

$dbservername = $config['db_host'];
$dbusername = $config['db_username'];
$dbpassword = $config['db_password'];
$dbdatabase = $config['db_database'];
$dbtable = "auth";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if new password is provided
    if (!isset($_POST['new_password'])) {
        http_response_code(500);
        echo json_encode(array("error" => "Error: New password is required"));
        exit;
    }

    // Check for tokens
    $token = isset($_POST['token']) ? $_POST['token'] : null;
    $reset_token = isset($_POST['reset_token']) ? $_POST['reset_token'] : null;

    // Use reset_token if provided, otherwise use token
    $token_to_use = !empty($reset_token) ? $reset_token : $token;

    // Check if the token to use is available
    if (empty($token_to_use)) {
        http_response_code(500);
        echo json_encode(array("error" => "Error: Token or reset_token is required"));
        exit;
    }

    $new_password = $_POST['new_password'];
    $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Create connection
    $conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbdatabase);

    // Check connection
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(array("error" => "Connection failed: " . $conn->connect_error));
        exit;
    }

    // Prepare a statement to fetch the user's email associated with the token or reset_token
    if (!empty($reset_token)) {
        // Use reset_token to fetch
        $stmt = $conn->prepare("SELECT email FROM $dbtable WHERE reset_token = ?");
    } else {
        // Use token to fetch
        $stmt = $conn->prepare("SELECT email FROM $dbtable WHERE token = ?");
    }

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(array("error" => "Prepare statement failed: " . $conn->error));
        $conn->close();
        exit;
    }

    // Bind the appropriate token
    $stmt->bind_param("s", $token_to_use);
    $stmt->execute();
    $stmt->store_result();
    
    // Check if user exists
    if ($stmt->num_rows === 0) {
        http_response_code(401);
        echo json_encode(array("error" => "Error: Invalid token"));
        $stmt->close();
        $conn->close();
        exit;
    }

    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();

    // Prepare a statement to update the password
    //$stmt = $conn->prepare("UPDATE $dbtable SET password = ? WHERE email = ?");
    $stmt = $conn->prepare("UPDATE $dbtable SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE email = ?");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(array("error" => "Prepare statement failed: " . $conn->error));
        $conn->close();
        exit;
    }

    $stmt->bind_param("ss", $hashed_new_password, $email);

    // Execute the statement
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(array("error" => "Execute statement failed: " . $stmt->error));
        $stmt->close();
        $conn->close();
        exit;
    }

    // Check if the password was updated
    if ($stmt->affected_rows > 0) {
        http_response_code(200);
        echo "Password changed successfully";
    } else {
        http_response_code(500);
        echo json_encode(array("error" => "Error: Password change failed"));
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(500);
    echo json_encode(array("error" => "Error: Only POST requests are allowed"));
}
?>
