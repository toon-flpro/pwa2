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
    $birthday = $_POST['birthday'] ?? '';
    $img = $_POST['img'] ?? '';
    $chapterOrigin_en = $_POST['chapterOrigin_en'] ?? '';
    $chapterOrigin_ch = $_POST['chapterOrigin_ch'] ?? '';
    $ancestralHome_en = $_POST['ancestralHome_en'] ?? '';
    $ancestralHome_ch = $_POST['ancestralHome_ch'] ?? '';
    $name_en = $_POST['name_en'] ?? '';
    $name_ch = $_POST['name_ch'] ?? '';
    $industry_en = $_POST['industry_en'] ?? '';
    $industry_ch = $_POST['industry_ch'] ?? '';
    $currentLocation_en = $_POST['currentLocation_en'] ?? '';
    $currentLocation_ch = $_POST['currentLocation_ch'] ?? '';
    $password = $_POST['password'] ?? '';
    $token = $_POST['token'] ?? '';
    $enable = $_POST['enable'] ?? null; // Add enable parameter

    if (empty($uuid) || empty($email) || !validateToken($conn, $token, $dbtableauth_admin)) {
        http_response_code(401);
        echo json_encode(array("error" => "Invalid data or token"));
        exit;
    }

    // Update data in the users table without changing email or uuid
    // Include $enable in the update statement
    $stmt = $conn->prepare("UPDATE $dbtable SET birthday=?, img=?, chapterOrigin_en=?, chapterOrigin_ch=?, ancestralHome_en=?, ancestralHome_ch=?, name_en=?, name_ch=?, industry_en=?, industry_ch=?, currentLocation_en=?, currentLocation_ch=?, enable=? WHERE uuid=? AND email=?");
    $stmt->bind_param("sssssssssssssss", $birthday, $img, $chapterOrigin_en, $chapterOrigin_ch, $ancestralHome_en, $ancestralHome_ch, $name_en, $name_ch, $industry_en, $industry_ch, $currentLocation_en, $currentLocation_ch, $enable, $uuid, $email);

    if ($stmt->execute()) {
        // Update password in the auth table if a new password is provided
        if (!empty($password)) {
            $stmtAuth = $conn->prepare("UPDATE $dbtableauth SET password=? WHERE email=?");
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmtAuth->bind_param("ss", $hashedPassword, $email);

            if ($stmtAuth->execute()) {
                http_response_code(200);
                echo json_encode(array("message" => "User and password updated successfully"));
            } else {
                http_response_code(500);
                echo json_encode(array("error" => "Error updating password in auth table"));
            }
            $stmtAuth->close();
        } else {
            http_response_code(200);
            echo json_encode(array("message" => "User updated successfully"));
        }
    } else {
        http_response_code(500);
        echo json_encode(array("error" => "Error updating user in users table"));
    }
    $stmt->close();

} else {
    http_response_code(400);
    echo json_encode(array("error" => "Only POST requests are allowed for user update"));
}

$conn->close();
?>
