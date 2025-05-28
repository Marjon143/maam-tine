<?php
require 'db_driver.php'; // Use $conn

$message = "";
$showModal = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $otp_input = $_POST['otp'];

    $stmt = $conn->prepare("SELECT otp_code, otp_expiry FROM drivers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $driver = $result->fetch_assoc();

    if ($driver && $driver['otp_code'] === $otp_input && strtotime($driver['otp_expiry']) > time()) {
        // Clear OTP
        $clear = $conn->prepare("UPDATE drivers SET otp_code=NULL, otp_expiry=NULL WHERE email=?");
        $clear->bind_param("s", $email);
        $clear->execute();

        $showModal = true;
    } else {
        $message = "<p class='error'>❌ Invalid or expired OTP.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP - Driver</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 30px; }
        .container { max-width: 400px; margin: auto; background: #fff; padding: 30px; border-radius: 10px; }
        input { width: 100%; padding: 10px; margin: 15px 0; border-radius: 5px; border: 1px solid #ccc; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .error { color: red; }
        .modal { display: none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; 
                 overflow: auto; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fff; margin: 15% auto; padding: 20px; border-radius: 10px; width: 80%; max-width: 300px; text-align: center; }
        .modal-content button { margin-top: 15px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Verify OTP</h2>
    <?php echo $message; ?>
    <form method="POST">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>" required>
        <input type="text" name="otp" placeholder="Enter OTP" required maxlength="6" pattern="\d{6}">
        <button type="submit">Verify</button>
    </form>
</div>

<div id="successModal" class="modal">
  <div class="modal-content">
    <h3>✅ OTP Verified Successfully!</h3>
    <p>You can now <a href="driver_login.php">login</a>.</p>
    <button onclick="closeModal()">Close</button>
  </div>
</div>

<script>
function closeModal() {
    document.getElementById('successModal').style.display = 'none';
    window.location.href = 'driver_auth.php';
}
<?php if ($showModal): ?>
    document.getElementById('successModal').style.display = 'block';
<?php endif; ?>
</script>

</body>
</html>
