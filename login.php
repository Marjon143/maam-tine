<?php
session_start();

$host = "localhost";
$dbname = "ecarga";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Registration Logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = $_POST['address'];
    $avatar_url = $_POST['avatar_url'];

    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, address, avatar_url) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashed_password, $address, $avatar_url]);

            // ✅ Set OTP modal flag
            $_SESSION['otp_email'] = $email;

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

// Login Logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT user_id, name, password FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['login_success'] = true; // ✅ Set flag to show modal
                // Redirect to same page to trigger modal
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                echo "Incorrect password.";
            }
        } else {
            echo "Email not found.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login & Register | eCarga</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- FontAwesome & CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/login.css">
    <script src="https://kit.fontawesome.com/2efc16a506.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container" id="container">
        <!-- Registration Form -->
        <div class="form-container sign-up">
            <form method="POST">
                <h1>Sign Up</h1>
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <input type="text" name="address" placeholder="Address" required>
                <input type="text" name="avatar_url" placeholder="Avatar Image URL" required>
                <button type="submit" name="register">Sign Up</button>
            </form>
        </div>

        <!-- Login Form -->
        <div class="form-container sign-in">
            <form method="POST">
                <h1>Login</h1>
                <span>Login using your email & password</span>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>
        </div>

        <!-- Toggle Panels -->
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Welcome Back!</h1>
                    <p>If you already have an account, login here.</p>
                    <button class="hidden" id="login">Sign In</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Hello, Friend!</h1>
                    <p>Don't have an account yet? Register here.</p>
                    <button class="hidden" id="register">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JS for toggling between forms -->
    <script src="assets/login.js"></script>

    <!-- ✅ OTP SENT MODAL -->
    <?php if (isset($_SESSION['otp_email'])): ?>
    <div id="otpModal" style="
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center;
        z-index: 9999;">
        <div style="
            background: #fff; padding: 30px 40px; border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3); text-align: center;">
            <h2>✅ OTP Sent!</h2>
            <p>Please check your email to verify your account.</p>
        </div>
    </div>
    <script>
        // Auto-redirect to send_otp.php after 3 seconds
        setTimeout(function () {
            window.location.href = "send_otp.php?email=<?= urlencode($_SESSION['otp_email']) ?>";
        }, 3000);
    </script>
    <?php unset($_SESSION['otp_email']); endif; ?>

    <!-- ✅ LOGIN SUCCESS MODAL -->
    <?php if (isset($_SESSION['login_success'])): ?>
    <div id="loginSuccessModal" style="
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center;
        z-index: 9999;">
        <div style="
            background: #fff; padding: 30px 40px; border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3); text-align: center;">
            <h2>🎉 Login Successful!</h2>
            <p>Redirecting to your dashboard...</p>
        </div>
    </div>
    <script>
        setTimeout(function () {
            window.location.href = "customer_landing.php";
        }, 2000);
    </script>
    <?php unset($_SESSION['login_success']); endif; ?>

</body>
</html>
