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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = $_POST['address'];
    $avatar_url = $_POST['avatar_url']; // ✅ fix: get avatar_url from form

    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, address, avatar_url) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashed_password, $address, $avatar_url]);
            echo "Registration successful!";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT user_id, name, password FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id']; // ✅ fix: store user_id
                $_SESSION['user_name'] = $user['name'];   // ✅ fix: store name
                header("Location: customer_landing.php");
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
    <input type="text" name="avatar_url" placeholder="Avatar Image URL" required> <!-- ✅ added -->

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

    <!-- JS for toggling between forms (optional) -->
    <script src="assets/login.js"></script>
</body>
</html>
