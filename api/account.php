<?php
include 'cors.php';
$config = include('config.php');

$dbservername = $config['db_host'];
$dbusername = $config['db_username'];
$dbpassword = $config['db_password'];
$dbdatabase = $config['db_database'];
$dbtable = "users";
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

    $stmt = $conn->prepare("SELECT email FROM $dbtableauth WHERE token = ?");
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
        $stmt->bind_result($email);
        $stmt->fetch();

        $stmt->close();

        // Fetch user information from the account table
        $userStmt = $conn->prepare("SELECT * FROM $dbtable WHERE email = ?");
        if (!$userStmt) {
            http_response_code(500);
            echo json_encode(array("error" => "Prepare statement failed: " . $conn->error));
            $conn->close();
            exit;
        }


        $userStmt->bind_param("s", $email);

        if (!$userStmt->execute()) {
            http_response_code(500);
            echo json_encode(array("error" => "Execute statement failed: " . $userStmt->error));
            $userStmt->close();
            $conn->close();
            exit;
        }

        $result = $userStmt->get_result();
        //echo "email: " . $email;

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            http_response_code(200);
           
            echo json_encode(array("success" => "Successful", "users" => $user));
        } else {
            http_response_code(400);
            echo json_encode(array("error" => "User not found"));
        }

        $userStmt->close();
    } else {
        http_response_code(401);
        echo json_encode(array("error" => "Invalid token or already signed out"));
        $stmt->close();
    }

    $conn->close();
} else {
    http_response_code(400);
    echo json_encode(array("error" => "Error: Only POST requests are allowed"));
}
?>