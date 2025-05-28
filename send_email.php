<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$host = "localhost";
$dbname = "ecarga";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$email = $_GET['email'] ?? '';

if (!$email) {
    die("No email provided.");
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format.");
}

$stmt = $pdo->prepare("SELECT name FROM users WHERE email = ?");
$stmt->execute([$email]);
$name = $stmt->fetchColumn();

if (!$name) {
    die("Email not found.");
}

$mail = new PHPMailer(true);

try {
    // Enable SMTP debugging (for testing, comment out in production)
    $mail->SMTPDebug = 2; 
    $mail->Debugoutput = 'html';

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = '20191336@nbsc.edu.ph';
    $mail->Password = 'iblh wcoj umuh ughm';  // Your app password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('marjonjudilla143@gmail.com', 'Marmyles Booking Alert');
    $mail->addAddress($email, $name);
    $mail->isHTML(true);
    $mail->Subject = 'Security Alert: Account Login Notification';
    $mail->Body = "
        <p>Hello {$name},</p>
        <p>Someone logged in using your account. If this was you, you can safely ignore this message.</p>
        <p>If you did not log in, please change your password immediately.</p>
        <br>
        <p>Best regards,<br>eCarga Team</p>
    ";

    $mail->send();
    echo "Login alert sent successfully to {$email}.";
} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
}
