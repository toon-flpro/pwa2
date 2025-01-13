<?php
$config = include(__DIR__ . '/../api/config.php');
include(__DIR__ . '/../api/cors.php');
header("Access-Control-Allow-Origin: *"); // Hide or restrict for production
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT"); // Hide or restrict for production
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Hide or restrict for production

$dbservername = $config['db_host'];
$dbusername = $config['db_username'];
$dbpassword = $config['db_password'];
$dbdatabase = $config['db_database'];
$dbtable = "auth_admin";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['email']) || !isset($_POST['password'])) {
        http_response_code(400);
        echo json_encode(array("error" => "Error: Email and password are required"));
        exit;
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Create connection
    $conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbdatabase);

    // Check connection
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(array("error" => "Connection failed: " . $conn->connect_error));
        exit;
    }

    // Prepare and bind
    $stmt = $conn->prepare("SELECT * FROM $dbtable WHERE email = ?");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(array("error" => "Prepare statement failed: " . $conn->error));
        $conn->close();
        exit;
    }

    $stmt->bind_param("s", $email);

    // Execute the statement
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(array("error" => "Execute statement failed: " . $stmt->error));
        $stmt->close();
        $conn->close();
        exit;
    }

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // Successful login
            $token = bin2hex(random_bytes(16)); 
            
            
            $update_stmt = $conn->prepare("UPDATE $dbtable SET token = ? WHERE email = ?");
            $update_stmt->bind_param("ss", $token, $email);
            if (!$update_stmt->execute()) {
                http_response_code(500);
                echo json_encode(array("error" => "Error updating token: " . $update_stmt->error));
                exit;
            }

            http_response_code(200);
            echo json_encode(array("success" => "Successful", "token" => $token));
        } else {
            http_response_code(400);
            echo json_encode(array("error" => "Invalid email or password"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("error" => "Invalid email or password"));
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(400);
    echo json_encode(array("error" => "Error: Only POST requests are allowed"));
}
?>
