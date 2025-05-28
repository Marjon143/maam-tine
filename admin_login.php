<?php
session_start();
require 'admin_connection.php'; // Your DB connection file

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $message = "Please enter email and password.";
    } else {
        $stmt = $conn->prepare("SELECT admin_id, email, password FROM admins WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['pending_admin_otp'] = true;
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_email'] = $admin['email'];
            
            header("Location: send_otp_admin.php?email=" . urlencode($email));
            exit;
        } else {
            $message = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin Login</title>
<style>
    body { font-family: Arial, sans-serif; background: #f0f0f0; padding: 40px; }
    .login-box { max-width: 400px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    input { width: 100%; padding: 12px; margin: 10px 0; border-radius: 6px; border: 1px solid #ccc; }
    button { width: 100%; padding: 12px; border: none; border-radius: 6px; background: #007bff; color: white; font-size: 16px; cursor: pointer; }
    .error { color: red; }
</style>
</head>
<body>

<div class="login-box">
    <h2>Admin Login</h2>
    <?php if ($message): ?>
        <p class="error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <input type="email" name="email" placeholder="Email" required autocomplete="username" />
        <input type="password" name="password" placeholder="Password" required autocomplete="current-password" />
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
