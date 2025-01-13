<?php
$config = include(__DIR__ . '/../api/config.php');
include(__DIR__ . '/../api/cors.php');

//--------------------
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);
//--------------------

$dbservername = $config['db_host'];
$dbusername = $config['db_username'];
$dbpassword = $config['db_password'];
$dbdatabase = $config['db_database'];
$dbtable = "users"; // Changed from events to users
$dbtableauth = "auth";
$dbtableauth_admin = "auth_admin";

//$baseUrl = 'http://192.168.1.3/';
$baseUrl = 'https://quanzhouyouth.com/';

function generateResetPasswordUrl($baseUrl, $token)
{
  //?token=xxxxxx&ev=reset-password
  return $baseUrl . '?token=' . $token.'&ev=reset-password';
}
function generateToken()
{
  return bin2hex(random_bytes(16));
}

// Connect to the database
$conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbdatabase);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(array("error" => "Connection failed: " . $conn->connect_error));
    exit;
}

// Validate token for authorization
function validateToken($conn, $token, $dbtableauth_admin)
{
    $stmt = $conn->prepare("SELECT email FROM $dbtableauth_admin WHERE token = ?");
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
    $enable = $_POST['enable'] ?? 1; // Default to 1 (enabled) if not provided

    if (empty($uuid) || empty($email) || empty($password) || !validateToken($conn, $token, $dbtableauth_admin)) {
        http_response_code(401);
        echo json_encode(array("error" => "Invalid data or token"));
        exit;
    }

    // Insert data into the users table
    $stmt = $conn->prepare("INSERT INTO $dbtable (uuid, email, birthday, img, chapterOrigin_en, chapterOrigin_ch, ancestralHome_en, ancestralHome_ch, name_en, name_ch, industry_en, industry_ch, currentLocation_en, currentLocation_ch, enable) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssssssi", $uuid, $email, $birthday, $img, $chapterOrigin_en, $chapterOrigin_ch, $ancestralHome_en, $ancestralHome_ch, $name_en, $name_ch, $industry_en, $industry_ch, $currentLocation_en, $currentLocation_ch, $enable);

    if ($stmt->execute()) {
        // Insert email and password into the auth table
        $stmtAuth = $conn->prepare("INSERT INTO $dbtableauth (email, password) VALUES (?, ?)");
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmtAuth->bind_param("ss", $email, $hashedPassword);

        if ($stmtAuth->execute()) {
            http_response_code(200);
            echo json_encode(array("message" => "User created successfully"));







            $ch = '';



            $token = generateToken();
            $resetPasswordUrl = generateResetPasswordUrl($baseUrl, $token);
            $expiryTime = date('Y-m-d H:i:s', strtotime('+14400 minutes'));

            $update_stmt = $conn->prepare("UPDATE auth SET reset_token = ?, reset_expiry = ? WHERE email = ?");
            $update_stmt->bind_param('sss', $token, $expiryTime, $email);

            if (!$update_stmt->execute()) {
                http_response_code(500);
                echo json_encode(array("error" => "Error updating token: " . $update_stmt->error));
                exit;
            }

            if ($update_stmt->affected_rows === 0) {
                // If no rows were updated, the email might not exist
                http_response_code(400);
                echo json_encode(array("error" => "No user found with this email."));
                exit;
            }

            http_response_code(200);
            //echo "Set reset token: " . $token . ", " . $resetPasswordUrl . ", " . $expiryTime;

            $emailbody1 = "";
            $emailbody2 = "";
            $emailbody3 = "";
            $emailbody4 = "";
            $emailbody5 = "";
            $emailbodybtn = "";


            if ($ch != "1") {
                $emailbody1 = "Welcome " . $name_en . ",<br><br>Your account has been successfully created. We welcome you to the exclusive online community of Quanzhou Youth Business Chamber.<br><br>You may access your account to view events and change your password.";
                $emailbody4 = "Thank you,<br>Quanzhou Youth Business";
                $emailbody5 = "Chamber Team";
                $emailbodybtn = "https://quanzhouyouth.com/email/btn_start.png";
            }

            try {
                //Server settings
                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host = 'email-smtp.ap-southeast-1.amazonaws.com'; // Set the SMTP server to send through
                $mail->SMTPAuth = true;                                   // Enable SMTP authentication
                $mail->Username = 'AKIASCHHXCLMGQAFOJV2';                                  // SMTP username
                $mail->Password = 'BO2cDTxKowHYKRjqjTefUuIJ8/oXucWWMtZiseiY2jgF';                                  // SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
                $mail->Port = 587;                                    // TCP port to connect to

                //Recipients

                $mail->addAddress($_POST['email'], $_POST['email']);           // Add a recipient
                //$mail->addReplyTo('info@example.com', 'Information');

                // Content
                $mail->isHTML(true);                                        // Set email format to HTML
                if ($ch != "1") {
                    $mail->Subject = 'Quanzhou Youth Business Chamber - Account Created';
                    $mail->setFrom('noreply@quanzhouyouth.com', 'quanzhouyouth');
                } else {
                    $mail->Subject = '泉州青年商会 - 帐户已创建';
                    $mail->setFrom('noreply@quanzhouyouth.com', '泉州青年商会');
                }
                $mail->CharSet = 'UTF-8'; // ch support
                //$mail->Subject = '(Testing SMTP) Please ignore this email.';
                //$mail->Body = 'test = '.$resetPasswordUrl;
                /*$mail->Body = <<<EOD
          <html>
          <head>
              <title>HTML Email</title>
          </head>
          <body>
              <h1>Welcome!</h1>
              <p>This is the HTML message body <b>in bold!</b></p>
              <p>If you have any questions, please contact us at $resetPasswordUrl.</p>
              <p>Here is another paragraph with more content.</p>
          </body>
          </html>
          EOD;*/
$mail->Body = <<<EOD

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" lang="en">
 <head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <meta name="x-apple-disable-message-reformatting">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="telephone=no" name="format-detection">
  <title>Quanzhou Youth Business Chamber</title><!--[if (mso 16)]>
    <style type="text/css">
    a {text-decoration: none;}
    </style>
    <![endif]--><!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]--><!--[if gte mso 9]>
<noscript>
         <xml>
           <o:OfficeDocumentSettings>
           <o:AllowPNG></o:AllowPNG>
           <o:PixelsPerInch>96</o:PixelsPerInch>
           </o:OfficeDocumentSettings>
         </xml>
      </noscript>
<![endif]-->
  <style type="text/css">
#outlook a {
	padding:0;
}
.es-button {
	mso-style-priority:100!important;
	text-decoration:none!important;
}
a[x-apple-data-detectors] {
	color:inherit!important;
	text-decoration:none!important;
	font-size:inherit!important;
	font-family:inherit!important;
	font-weight:inherit!important;
	line-height:inherit!important;
}
.es-desk-hidden {
	display:none;
	float:left;
	overflow:hidden;
	width:0;
	max-height:0;
	line-height:0;
	mso-hide:all;
}
[data-ogsb] .es-button.es-button-1729568792704 {
	padding:10px 30px 10px 5px!important;
}
@media only screen and (max-width:600px) {p, ul li, ol li, a { line-height:150%!important } h1, h2, h3, h1 a, h2 a, h3 a { line-height:120%!important } h1 { font-size:36px!important; text-align:left } h2 { font-size:26px!important; text-align:left } h3 { font-size:20px!important; text-align:left } .es-header-body h1 a, .es-content-body h1 a, .es-footer-body h1 a { font-size:36px!important; text-align:left } .es-header-body h2 a, .es-content-body h2 a, .es-footer-body h2 a { font-size:26px!important; text-align:left } .es-header-body h3 a, .es-content-body h3 a, .es-footer-body h3 a { font-size:20px!important; text-align:left } .es-menu td a { font-size:12px!important } .es-header-body p, .es-header-body ul li, .es-header-body ol li, .es-header-body a { font-size:14px!important } .es-content-body p, .es-content-body ul li, .es-content-body ol li, .es-content-body a { font-size:14px!important } .es-footer-body p, .es-footer-body ul li, .es-footer-body ol li, .es-footer-body a { font-size:14px!important } .es-infoblock p, .es-infoblock ul li, .es-infoblock ol li, .es-infoblock a { font-size:12px!important } *[class="gmail-fix"] { display:none!important } .es-m-txt-c, .es-m-txt-c h1, .es-m-txt-c h2, .es-m-txt-c h3 { text-align:center!important } .es-m-txt-r, .es-m-txt-r h1, .es-m-txt-r h2, .es-m-txt-r h3 { text-align:right!important } .es-m-txt-l, .es-m-txt-l h1, .es-m-txt-l h2, .es-m-txt-l h3 { text-align:left!important } .es-m-txt-r img, .es-m-txt-c img, .es-m-txt-l img { display:inline!important } .es-button-border { display:inline-block!important } a.es-button, button.es-button { font-size:20px!important; display:inline-block!important } .es-adaptive table, .es-left, .es-right { width:100%!important } .es-content table, .es-header table, .es-footer table, .es-content, .es-footer, .es-header { width:100%!important; max-width:600px!important } .es-adapt-td { display:block!important; width:100%!important } .adapt-img { width:100%!important; height:auto!important } .es-m-p0 { padding:0!important } .es-m-p0r { padding-right:0!important } .es-m-p0l { padding-left:0!important } .es-m-p0t { padding-top:0!important } .es-m-p0b { padding-bottom:0!important } .es-m-p20b { padding-bottom:20px!important } .es-mobile-hidden, .es-hidden { display:none!important } tr.es-desk-hidden, td.es-desk-hidden, table.es-desk-hidden { width:auto!important; overflow:visible!important; float:none!important; max-height:inherit!important; line-height:inherit!important } tr.es-desk-hidden { display:table-row!important } table.es-desk-hidden { display:table!important } td.es-desk-menu-hidden { display:table-cell!important } .es-menu td { width:1%!important } table.es-table-not-adapt, .esd-block-html table { width:auto!important } table.es-social { display:inline-block!important } table.es-social td { display:inline-block!important } .es-m-p5 { padding:5px!important } .es-m-p5t { padding-top:5px!important } .es-m-p5b { padding-bottom:5px!important } .es-m-p5r { padding-right:5px!important } .es-m-p5l { padding-left:5px!important } .es-m-p10 { padding:10px!important } .es-m-p10t { padding-top:10px!important } .es-m-p10b { padding-bottom:10px!important } .es-m-p10r { padding-right:10px!important } .es-m-p10l { padding-left:10px!important } .es-m-p15 { padding:15px!important } .es-m-p15t { padding-top:15px!important } .es-m-p15b { padding-bottom:15px!important } .es-m-p15r { padding-right:15px!important } .es-m-p15l { padding-left:15px!important } .es-m-p20 { padding:20px!important } .es-m-p20t { padding-top:20px!important } .es-m-p20r { padding-right:20px!important } .es-m-p20l { padding-left:20px!important } .es-m-p25 { padding:25px!important } .es-m-p25t { padding-top:25px!important } .es-m-p25b { padding-bottom:25px!important } .es-m-p25r { padding-right:25px!important } .es-m-p25l { padding-left:25px!important } .es-m-p30 { padding:30px!important } .es-m-p30t { padding-top:30px!important } .es-m-p30b { padding-bottom:30px!important } .es-m-p30r { padding-right:30px!important } .es-m-p30l { padding-left:30px!important } .es-m-p35 { padding:35px!important } .es-m-p35t { padding-top:35px!important } .es-m-p35b { padding-bottom:35px!important } .es-m-p35r { padding-right:35px!important } .es-m-p35l { padding-left:35px!important } .es-m-p40 { padding:40px!important } .es-m-p40t { padding-top:40px!important } .es-m-p40b { padding-bottom:40px!important } .es-m-p40r { padding-right:40px!important } .es-m-p40l { padding-left:40px!important } .es-desk-hidden { display:table-row!important; width:auto!important; overflow:visible!important; max-height:inherit!important } }
@media screen and (max-width:384px) {.mail-message-content { width:414px!important } }
</style>
 </head>
 <body style="width:100%;font-family:arial, 'helvetica neue', helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0">
  <div dir="ltr" class="es-wrapper-color" lang="en" style="background-color:#E0E7FF"><!--[if gte mso 9]>
			<v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t">
				<v:fill type="tile" color="#E0E7FF"></v:fill>
			</v:background>
		<![endif]-->
   <table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;background-color:#E0E7FF">
     <tr>
      <td valign="top" style="padding:0;Margin:0">
       <table cellpadding="0" cellspacing="0" class="es-content" align="center" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%">
         <tr>
          <td class="es-info-area" align="center" style="padding:0;Margin:0">
           <table class="es-content-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;width:600px" bgcolor="#00000000" role="none">
             <tr>
              <td align="left" style="padding:20px;Margin:0">
               <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                 <tr>
                  <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                   <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                     <tr>
                      <td align="center" style="padding:0;Margin:0;display:none"></td>
                     </tr>
                   </table></td>
                 </tr>
               </table></td>
             </tr>
           </table></td>
         </tr>
       </table>
       <table cellpadding="0" cellspacing="0" class="es-header" align="center" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top">
         <tr>
          <td align="center" style="padding:0;Margin:0">
           <table bgcolor="#ffffff" class="es-header-body" align="center" cellpadding="0" cellspacing="0" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;width:600px">
             <tr>
              <td align="left" style="padding:0;Margin:0">
               <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                 <tr>
                  <td align="center" valign="top" style="padding:0;Margin:0;width:600px">
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                     <tr>
                      <td align="center" style="padding:0;Margin:0;padding-bottom:25px;font-size:0px"><img src="https://quanzhouyouth.com/email/logo.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="175"></td>
                     </tr>
                   </table></td>
                 </tr>
               </table></td>
             </tr>
           </table></td>
         </tr>
       </table>
       <table cellpadding="0" cellspacing="0" class="es-content" align="center" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%">
         <tr>
          <td align="center" style="padding:0;Margin:0">
           <table class="es-content-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;width:600px" role="none">
             <tr>
              <td align="left" bgcolor="#ffffff" style="Margin:0;padding-bottom:10px;padding-top:40px;padding-left:40px;padding-right:40px;background-color:#ffffff;border-radius:15px 15px 0px 0px">
               <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                 <tr>
                  <td align="center" valign="top" style="padding:0;Margin:0;width:520px">
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                     <tr>
                      <td align="left" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px">$emailbody1</p></td>
                     </tr>
                   </table></td>
                 </tr>
               </table></td>
             </tr>
             <tr>
              <td align="left" bgcolor="#ffffff" style="padding:0;Margin:0;padding-top:10px;padding-left:40px;padding-right:40px;background-color:#ffffff">
               <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                 <tr>
                  <td align="center" valign="top" style="padding:0;Margin:0;width:520px">
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                     <tr>
                      <td align="left" style="padding:0;Margin:0;font-size:0px"><a target="_blank" href=$resetPasswordUrl style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#5C68E2;font-size:14px"><img src=$emailbodybtn alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="173"></a></td>
                     </tr>
                   </table></td>
                 </tr>
               </table></td>
             </tr>
             <tr>
              <td align="left" style="Margin:0;padding-bottom:0px;padding-top:0px;padding-left:40px;padding-right:40px;background-color:#ffffff" bgcolor="#ffffff">
               <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                 <tr>
                  <td align="center" valign="top" style="padding:0;Margin:0;width:520px">
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                     <tr>
                      <td align="left" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px">$emailbody2</p></td>
                     </tr>
                   </table></td>
                 </tr>
               </table></td>
             </tr>
             <tr>
             <tr>
              <td align="left" style="Margin:0;padding-bottom:0px;padding-top:0px;padding-left:40px;padding-right:40px;border-radius:0px 0px 20px 20px;background-color:#ffffff" bgcolor="#ffffff">
               <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                 <tr>
                  <td align="center" valign="top" style="padding:0;Margin:0;width:520px">
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                     <tr>
                      <td align="left" style="padding:0;Margin:0;padding-bottom:20px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px">$emailbody3</p><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px"><br></p><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px">$emailbody4</p><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px">$emailbody5</p></td>
                     </tr>
                   </table></td>
                 </tr>
               </table></td>
             </tr>
           </table></td>
         </tr>
       </table>
       <table cellpadding="0" cellspacing="0" class="es-content" align="center" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%">
         <tr>
          <td class="es-info-area" align="center" style="padding:0;Margin:0">
           <table class="es-content-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;width:600px" bgcolor="#00000000" role="none">
             <tr>
              <td align="left" style="padding:0;Margin:0;padding-top:20px;padding-left:20px;padding-right:20px">
               <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                 <tr>
                  <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                     <tr>
                      <td align="center" class="es-infoblock" style="padding:0;Margin:0;line-height:14.4px;font-size:0px;color:#CCCCCC"><a target="_blank" href="https://revillimite.com/" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#CCCCCC;font-size:12px"><img src="https://quanzhouyouth.com/email/rev.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="82"></a></td>
                     </tr>
                   </table></td>
                 </tr>
               </table></td>
             </tr>
             <tr>
             <!-- sssss -->
              <tr>
                <td align="center" style="padding:0;Margin:0;padding-top:20px;padding-left:80px;padding-right:80px;padding-bottom:80px;">
                  <table cellpadding="0" cellspacing="0" align="center" role="none" style="border-collapse:collapse;border-spacing:0px">
                    <tr>
                      
                      <td class="es-hidden" style="padding:0;Margin:0;width:20px"></td>
                      <td class="es-m-p20b" align="center" style="padding:0;Margin:0;width:30px">
                        <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="border-collapse:collapse;border-spacing:0px">
                          <tr>
                            <td align="center" class="es-infoblock" style="padding:0;Margin:0;line-height:14.4px;font-size:0px;color:#CCCCCC">
                              <a target="_blank" href="https://www.facebook.com/revillimite" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#CCCCCC;font-size:12px">
                                            <img src="https://quanzhouyouth.com/email/fb.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="30">
                                        </a>
                            </td>
                          </tr>
                        </table>
                      </td>
                      <td class="es-hidden" style="padding:0;Margin:0;width:20px"></td>
                      <td class="es-m-p20b" align="center" style="padding:0;Margin:0;width:30px">
                        <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="border-collapse:collapse;border-spacing:0px">
                          <tr>
                            <td align="center" class="es-infoblock" style="padding:0;Margin:0;line-height:14.4px;font-size:0px;color:#CCCCCC">
                              <a target="_blank" href="https://www.instagram.com/revillimite/" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#CCCCCC;font-size:12px">
                                            <img src="https://quanzhouyouth.com/email/in.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="30">
                                        </a>
                            </td>
                          </tr>
                        </table>
                      </td>
                      <td class="es-hidden" style="padding:0;Margin:0;width:20px"></td>
                      <td align="center" class="es-m-p20b" style="padding:0;Margin:0;width:30px">
                        <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="border-collapse:collapse;border-spacing:0px">
                          <tr>
                            <td align="center" class="es-infoblock" style="padding:0;Margin:0;line-height:14.4px;font-size:0px;color:#CCCCCC">
                             <a target="_blank" href="https://www.linkedin.com/company/revillimite/?viewAsMember=true" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#CCCCCC;font-size:12px">
                                            <img src="https://quanzhouyouth.com/email/link.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="30">
                                        </a>
                            </td>
                          </tr>
                        </table>
                      </td>
                      <td class="es-hidden" style="padding:0;Margin:0;width:20px"></td>
                      <td align="left" class="es-m-p20b" style="padding:0;Margin:0;width:30px">
                        <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="border-collapse:collapse;border-spacing:0px">
                          <tr>
                            <td align="center" class="es-infoblock" style="padding:0;Margin:0;line-height:14.4px;font-size:0px;color:#CCCCCC">
                              <a target="_blank" href="mailto:enquiry@revillimite.com" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#CCCCCC;font-size:12px">
                                            <img src="https://quanzhouyouth.com/email/email.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="30">
                                        </a>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <!-- -->    
           </table></td>
         </tr>
       </table></td>
     </tr>
   </table>
  </div>
 </body>
</html>
EOD;
                $mail->AltBody = '';

                $mail->send();
                echo 'Email Sent';
            } catch (Exception $e) {
                http_response_code(500);
                echo "Could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

























































































        } else {
            http_response_code(500);
            echo json_encode(array("error" => "Error creating user in auth table"));
        }
        $stmtAuth->close();
    } else {
        http_response_code(500);
        echo json_encode(array("error" => "Error creating user in users table"));
    }
    $stmt->close();

} else {
    http_response_code(400);
    echo json_encode(array("error" => "Only POST requests are allowed for user creation"));
}

$conn->close();
?>