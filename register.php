<?php
$host = 'localhost';
$db = 'ecarga';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $contact = $_POST['contact_number'];
    $avatar_url = $_POST['avatar_url'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $checkStmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, address, avatar_url, otp_code, otp_expiry) VALUES (?, ?, ?, ?, ?, NULL, NULL)");
            $stmt->bind_param("sssss", $name, $email, $hashed_password, $contact, $avatar_url);

            if ($stmt->execute()) {
                // ✅ Redirect to OTP sender
                header("Location: send_otp.php?email=" . urlencode($email));
                exit();
            } else {
                $error = "Error: " . $stmt->error;
            }
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="assets/register.css">
    
</head>
<body>
<div class="form-container">
    <h2>Register</h2>

    <?php if (!empty($error)) echo "<div class='message error'>$error</div>"; ?>
    <?php if (!empty($success)) echo "<div class='message success'>$success</div>"; ?>

    <form method="POST" action="">
        <label>Full Name</label>
        <input type="text" name="full_name" required>

        <label>Contact Number</label>
        <input type="text" name="contact_number" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>

        <label>Avatar URL</label>
        <input type="text" name="avatar_url">

        <input type="submit" value="Register">
    </form>
</div>
</body>
</html>
