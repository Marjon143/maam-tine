<?php
session_start();

$host = "localhost";
$dbname = "ecarga";
$username = "root";
$password = "";

// Your reCAPTCHA secret key
$recaptcha_secret = "6LeWCkwrAAAAAMeByd-dNQixtxwwWMsFBO_FW49A";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // Verify reCAPTCHA response
    $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => $recaptcha_secret,
        'response' => $recaptcha_response
    ];

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query($data)
        ]
    ];
    $context = stream_context_create($options);
    $verify = file_get_contents($verify_url, false, $context);
    $captcha_success = json_decode($verify);

    if (!$captcha_success->success) {
        $error = "Please verify you are not a robot.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT driver_id, driver_name, password FROM drivers WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                $driver = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($password, $driver['password'])) {
                    $_SESSION['driver_id'] = $driver['driver_id'];
                    $_SESSION['driver_name'] = $driver['driver_name'];
                    $_SESSION['driver_login_success'] = true;

                    header("Location: driver_side_landing.php");
                    exit();
                } else {
                    $error = "Incorrect password.";
                }
            } else {
                $error = "Email not found.";
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Driver Login | eCarga</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <style>
    body { font-family: Arial, sans-serif; background: #f9f9f9; }
    .container { max-width: 400px; margin: 50px auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
    input { width: 100%; padding: 10px; margin: 8px 0; border-radius: 5px; border: 1px solid #ccc; }
    button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
    button:hover { background: #0056b3; }
    .error { color: red; margin-bottom: 10px; }
    a { color: #007bff; text-decoration: none; }
    a:hover { text-decoration: underline; }
  </style>
</head>
<body>
<div class="container">
  <h1>Driver Login</h1>
  <?php if (!empty($error)) { echo "<p class='error'>$error</p>"; } ?>
  <form method="POST" action="">
    <input type="email" name="email" placeholder="Email" required />
    <input type="password" name="password" placeholder="Password" required />
    
    <!-- Google reCAPTCHA widget -->
    <div class="g-recaptcha" data-sitekey="6LeWCkwrAAAAAHWw40IC-Qqa0UqGFLLJZcKJQAEZ"></div>
    
    <button type="submit">Login</button>
  </form>
  <p>Don't have an account? <a href="driver_register.php">Register here</a>.</p>
</div>
</body>
</html>
