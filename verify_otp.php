<?php
require 'db.php'; // ✅ Use $conn

$message = "";
$showModal = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $otp_input = $_POST['otp'];

    $stmt = $conn->prepare("SELECT otp_code, otp_expiry FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && $user['otp_code'] === $otp_input && strtotime($user['otp_expiry']) > time()) {
        // ✅ Clear OTP
        $clear = $conn->prepare("UPDATE users SET otp_code=NULL, otp_expiry=NULL WHERE email=?");
        $clear->bind_param("s", $email);
        $clear->execute();

        // ✅ Set modal trigger (don't redirect immediately)
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
    <title>Verify OTP</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .otp-container {
            background: white;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        input[type="email"], input[type="text"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }

        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background: #0056b3;
        }

        .success {
            color: green;
            margin-top: 15px;
        }

        .error {
            color: red;
            margin-top: 15px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 10;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }

        .modal-content {
            background: white;
            padding: 20px 30px;
            border-radius: 8px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .modal-content h3 {
            margin: 0;
            color: green;
        }
    </style>
</head>
<body>

<div class="otp-container">
    <h2>Email OTP Verification</h2>
    <form method="POST">
        <input type="email" name="email" placeholder="Enter your email" required />
        <input type="text" name="otp" placeholder="Enter OTP" required />
        <button type="submit">Verify OTP</button>
    </form>
    <?php if (!empty($message)) echo $message; ?>
</div>

<!-- Modal -->
<div id="successModal" class="modal">
    <div class="modal-content">
        <h3>✅ Registration Successful!</h3>
        <p>Redirecting to login...</p>
    </div>
</div>

<?php if ($showModal): ?>
<script>
    // Show modal and redirect after 2 seconds
    document.getElementById('successModal').style.display = 'block';
    setTimeout(function() {
        window.location.href = "login.php?verified=1";
    }, 2000);
</script>
<?php endif; ?>

</body>
</html>
