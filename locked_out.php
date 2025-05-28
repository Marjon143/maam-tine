<?php
// locked_out.php
$remaining = isset($_GET['remaining']) ? (int)$_GET['remaining'] : 60;

if ($remaining < 0) {
    $remaining = 0;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Account Locked</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .message { background: #f44336; color: white; padding: 20px; border-radius: 5px; display: inline-block; }
        #countdown { font-weight: bold; font-size: 1.5em; }
        a { display: block; margin-top: 20px; text-decoration: none; color: #2196F3; }
    </style>
    <script>
        let timeLeft = <?php echo $remaining; ?>;

        function countdown() {
            if (timeLeft <= 0) {
                clearInterval(timer);
                document.getElementById('countdown').innerText = 'You can now try logging in again.';
            } else {
                document.getElementById('countdown').innerText = timeLeft + ' seconds';
                timeLeft--;
            }
        }
        let timer = setInterval(countdown, 1000);
        window.onload = countdown;
    </script>
</head>
<body>
    <div class="message">
        <h2>Your account is temporarily locked due to multiple failed login attempts.</h2>
        <p>Please wait for <span id="countdown"></span> before trying again.</p>
    </div>
    <a href="login.php">Back to Login</a>
</body>
</html>
