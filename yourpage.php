<?php
session_start();

$defaultWait = 120; // fallback default lockout time in seconds

// Reset timer if requested via GET param 'reset=1'
if (isset($_GET['reset']) && $_GET['reset'] === '1') {
    $_SESSION['locked_out_until'] = time() + $defaultWait;
    // Redirect to clean URL (avoid reset loop)
    header("Location: yourpage.php");
    exit();
}

// Calculate remaining time
if (isset($_SESSION['locked_out_until'])) {
    $remaining = $_SESSION['locked_out_until'] - time();
    $wait = max(0, $remaining);
} else {
    // If no lockout set yet, initialize it now
    $_SESSION['locked_out_until'] = time() + $defaultWait;
    $wait = $defaultWait;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Too Many Attempts | eCarga</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #ffecd2, #fcb69f);
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .message-box {
            background: white;
            padding: 40px 50px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        .countdown {
            font-size: 40px;
            font-weight: bold;
            color: #2c3e50;
            background: #f1f1f1;
            padding: 20px 40px;
            border-radius: 12px;
            margin-top: 15px;
            animation: pulse 1s ease-in-out infinite;
        }
        button {
            margin-top: 20px;
            font-size: 16px;
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            background-color: #f76c6c;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #e94b4b;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="message-box">
        <h2>⚠ Too Many Login Attempts</h2>
        <p>Please wait before trying again.</p>
        <div class="countdown" id="countdown"><?= $wait ?></div>

        <!-- Reset button -->
        <form method="get" action="yourpage.php">
            <button type="submit" name="reset" value="1">Reset Timer</button>
        </form>
    </div>

    <script>
        let seconds = <?= $wait ?>;
        const countdown = document.getElementById('countdown');

        if (seconds > 0) {
            const interval = setInterval(() => {
                seconds--;
                countdown.innerText = seconds;
                if (seconds <= 0) {
                    clearInterval(interval);
                    // After countdown ends, redirect to login page or another page
                    window.location.href = "login.php"; 
                }
            }, 1000);
        } else {
            // No wait time - redirect immediately or show message
            window.location.href = "login.php";
        }
    </script>
</body>
</html>
