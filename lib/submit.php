<?php
$servername = "localhost"; //<< please change
$username = "root";//<< please change
$password = "fpojh1E#Rg-92SQ2e@#!";//<< please change
$database = "qz_email";

//-------------------------------------------------------------------------

function sanitizeEmail($email)
{
    $data = trim($email);
    $data = htmlspecialchars($data);
    $email = preg_replace('/[^a-zA-Z0-9._@-]/', '', $email);
    return $email;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['token'] != "58d0qskh3_scdh42S21#!@") {
        die ("Error: Something wrong");
    }

    $email = sanitizeEmail($_POST['email']);// Validate 
    $conn = new mysqli($servername, $username, $password, $database);
    if ($conn->connect_error) {
        die ("Connection failed: " . $conn->connect_error);
    }

    //Check the email already exists
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM email WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
       //Email already exists!;
       echo "e001";
    } else {
        //Add new email
        $sql = "INSERT INTO email (email) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            echo "";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $stmt->close();
        $conn->close();
        echo "Successed: " . $email;
    }
} else {
    echo "Error: Only POST requests are allowed";
}
?>