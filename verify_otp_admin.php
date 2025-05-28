<?php
session_start();
require 'admin_connection.php';

$message = "";
$showModal = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $otp_input = $_POST['otp'] ?? '';

    $stmt = $conn->prepare("SELECT otp_code, otp_expiry FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && $admin['otp_code'] === $otp_input && strtotime($admin['otp_expiry']) > time()) {
        // Clear OTP in DB
        $clear = $conn->prepare("UPDATE admins SET otp_code = NULL, otp_expiry = NULL WHERE email = ?");
        $clear->bind_param("s", $email);
        $clear->execute();

        // Set logged-in session and remove pending OTP flag
        $_SESSION['admin_logged_in'] = true;
        unset($_SESSION['pending_admin_otp']);

        $showModal = true;
    } else {
        $message = "❌ Invalid or expired OTP.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Verify OTP - Admin</title>
<style>
    body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 40px; }
    .container { max-width: 400px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);}
    input { width: 100%; padding: 12px; margin: 10px 0; border-radius: 6px; border: 1px solid #ccc; font-size: 16px;}
    button { width: 100%; padding: 12px; border: none; border-radius: 6px; background: #28a745; color: white; font-size: 16px; cursor: pointer;}
    .error { color: red; margin-bottom: 15px; }
    .modal { display: none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%;
             overflow: auto; background-color: rgba(0,0,0,0.4);}
    .modal-content { background-color: #fff; margin: 15% auto; padding: 30px; border-radius: 10px; width: 80%; max-width: 300px; text-align: center; }
</style>
</head>
<body>

<div class="container">
    <h2>Verify OTP</h2>
    <?php if ($message): ?>
        <p class="error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <input type="hidden" name="email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>" required />
        <input type="text" name="otp" placeholder="Enter 6-digit OTP" maxlength="6" pattern="\d{6}" required autofocus />
        <button type="submit">Verify</button>
    </form>
</div>

<div id="successModal" class="modal">
    <div class="modal-content">
        <h3>✅ OTP Verified Successfully!</h3>
        <p>Redirecting to dashboard...</p>
    </div>
</div>

<script>
<?php if ($showModal): ?>
    document.getElementById('successModal').style.display = 'block';
    setTimeout(() => { window.location.href = 'dashboard.php'; }, 2000);
<?php endif; ?>
</script>

</body>
</html>
