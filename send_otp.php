<?php
require 'vendor/autoload.php';
require 'db.php'; // ✅ Using $conn from this file
use PHPMailer\PHPMailer\PHPMailer;

$email = $_GET['email'] ?? '';
$otp = rand(100000, 999999);
$expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

// ✅ Save OTP to database
$stmt = $conn->prepare("UPDATE users SET otp_code = ?, otp_expiry = ? WHERE email = ?");
$stmt->bind_param("sss", $otp, $expiry, $email);
$stmt->execute();

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'marjonjudilla143@gmail.com'; // ✅ Replace with actual email
    $mail->Password = 'qxmj cmlw vgos jqrt';         // ✅ App password (never expose in production)
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('marjonjudilla143@gmail.com', 'eCarga OTP System');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Your OTP Code';
    $mail->Body = "Your OTP is <strong>$otp</strong>. It will expire in 10 minutes.";

    $mail->send();

    // ✅ Redirect to OTP input form
    header("Location: verify_otp.php?email=" . urlencode($email));
    exit();
} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
}
?>
