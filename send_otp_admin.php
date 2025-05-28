<?php
session_start();
require 'admin_connection.php';
require 'vendor/autoload.php'; // PHPMailer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['pending_admin_otp']) || !isset($_GET['email'])) {
    header("Location: admin_login.php");
    exit;
}

$email = $_GET['email'];
$otp = rand(100000, 999999);
$expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

// Save OTP to admins table
$stmt = $conn->prepare("UPDATE admins SET otp_code = ?, otp_expiry = ? WHERE email = ?");
$stmt->bind_param("sss", $otp, $expiry, $email);
$stmt->execute();

// Prepare PHPMailer
$mail = new PHPMailer(true);

try {
    // Server settings (adjust these as per your SMTP server)
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';        // SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'marjonjudilla143@gmail.com'; // SMTP username
    $mail->Password = 'qxmj cmlw vgos jqrt';          // SMTP password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('marjonjudilla143@gmail.com', 'Marmyles BookingAdmin System');
    $mail->addAddress($email);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Your Admin Login OTP Code';
    $mail->Body = "<p>Your OTP code is <b>$otp</b>. It is valid for 5 minutes.</p>";

    $mail->send();

    // Redirect to OTP verify page
    header("Location: verify_otp_admin.php?email=" . urlencode($email));
    exit;
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
