<?php
session_start();

$host = 'localhost';
$db = 'ecarga';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$showModal = false;
$error = "";

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = [];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Check if user is locked out
    if (isset($_SESSION['login_attempts'][$email])) {
        $attemptData = $_SESSION['login_attempts'][$email];
        if (isset($attemptData['locked_until']) && time() < $attemptData['locked_until']) {
            $remaining = $attemptData['locked_until'] - time();
            header("Location: locked_out.php?remaining=" . $remaining);
            exit();
        }
    }

    // reCAPTCHA secret key
    $secretKey = '6LeWCkwrAAAAAMeByd-dNQixtxwwWMsFBO_FW49A';

    // Verify reCAPTCHA response
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
    $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $response = file_get_contents($verifyUrl . '?secret=' . $secretKey . '&response=' . $recaptchaResponse);
    $responseData = json_decode($response);

    if (!$responseData->success) {
        $error = "Please verify that you are not a robot.";
    } else {
        // Check user credentials
        $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                // Success: reset attempts and show modal
                unset($_SESSION['login_attempts'][$email]);
                $showModal = true;
            } else {
                recordFailedAttempt($email, $ip_address, 'wrong_password', $user_id);
                $error = "Invalid password.";
            }
        } else {
            recordFailedAttempt($email, $ip_address, 'wrong_email', null);
            $error = "Email not found.";
        }
    }
}

function recordFailedAttempt($email, $ip_address, $reason, $user_id = null) {
    global $conn;

    if (!isset($_SESSION['login_attempts'][$email])) {
        $_SESSION['login_attempts'][$email] = [
            'count' => 0,
            'last_attempt' => 0,
            'locked_until' => 0
        ];
    }
    $attempts = &$_SESSION['login_attempts'][$email];

    // Reset count if last attempt was more than 60 seconds ago
    if (time() - $attempts['last_attempt'] > 60) {
        $attempts['count'] = 0;
        $attempts['locked_until'] = 0;
    }

    $attempts['count']++;
    $attempts['last_attempt'] = time();

    // Implement lockout logic
    if ($attempts['count'] >= 3) {
        if ($attempts['count'] == 3) {
            // 4th attempt: lockout 30 seconds
            $lockoutSeconds = 30;
        } else {
            // Subsequent attempts: incremental 1 minute per extra attempt after 4
            $extraAttempts = $attempts['count'] - 1;
            $lockoutSeconds = 30 + ($extraAttempts * 60);
        }
        $attempts['locked_until'] = time() + $lockoutSeconds;
    } else {
        // For attempts 1,2,3 no lockout
        $attempts['locked_until'] = 0;
    }

    // Insert attempt into database
    $insertStmt = $conn->prepare("INSERT INTO login_attempts (email, ip_address, reason, user_id) VALUES (?, ?, ?, ?)");
    $insertStmt->bind_param("sssi", $email, $ip_address, $reason, $user_id);
    $insertStmt->execute();
    $insertStmt->close();
}
?>

<!DOCTYPE html>   
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="assets/login.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
<div class="form-container">
    <h2>Login</h2>

    <?php if (!empty($error)) echo "<div class='message error'>$error</div>"; ?>

    <form method="POST" action="">
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <!-- Google reCAPTCHA widget -->
        <div class="g-recaptcha" data-sitekey="6LeWCkwrAAAAAHWw40IC-Qqa0UqGFLLJZcKJQAEZ"></div>

        <input type="submit" value="Login" style="margin-top:15px;">
    </form>
    <a href="register.php">Don't have an account? Register</a>
</div>

<!-- Success Modal -->
<div id="successModal" class="modal" style="display:none;">
    <div class="modal-content">
        <p>Login successful! Redirecting...</p>
    </div>
</div>

<?php if ($showModal): ?>
<script>
    document.getElementById('successModal').style.display = 'block';
    setTimeout(function() {
        window.location.href = 'customer_landing.php';
    }, 2000);
</script>
<?php endif; ?>

</body>
</html>
