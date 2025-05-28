<?php
require 'vendor/autoload.php';
require 'db_driver.php'; // Use $conn from this file
use PHPMailer\PHPMailer\PHPMailer;

$email = $_GET['email'] ?? '';
if (!$email) {
    die("Email is required");
}

$otp = rand(100000, 999999);
$expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

// Save OTP in drivers table
$stmt = $conn->prepare("UPDATE drivers SET otp_code = ?, otp_expiry = ? WHERE email = ?");
$stmt->bind_param("sss", $otp, $expiry, $email);
$stmt->execute();

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'marjonjudilla143@gmail.com'; // your email
    $mail->Password = 'qxmj cmlw vgos jqrt';         // app password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('marjonjudilla143@gmail.com', 'eCarga OTP System');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Your OTP Code';
    $mail->Body = "Your OTP is <strong>$otp</strong>. It will expire in 10 minutes.";

    $mail->send();

    header("Location: verify_otp_driver.php?email=" . urlencode($email));
    exit();
} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
}
?>
