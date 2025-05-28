<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Security Bulletin - Phishing Awareness</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .bulletin-container {
            max-width: 700px;
            margin: 50px auto;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 5px;
            padding: 20px 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .bulletin-title {
            color: #856404;
            font-size: 1.8rem;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .bulletin-message {
            color: #856404;
            font-size: 1.1rem;
            line-height: 1.5;
        }
        .bulletin-message ul {
            margin: 15px 0 0 20px;
        }
        .bulletin-message ul li {
            margin-bottom: 8px;
        }
        .important {
            font-weight: bold;
            color: #721c24;
        }
        .footer {
            margin-top: 25px;
            font-size: 0.9rem;
            color: #6c757d;
            text-align: center;
        }
        /* Styled link as a button */
        .test-link-btn {
            display: inline-block;
            margin-top: 20px;
            font-size: 1rem;
            color:rgb(78, 216, 235);
            text-decoration: underline;
            cursor: pointer;
            padding: 0;
            font-family: inherit;
            border: none;
            background: none;
            text-align: center;
        }
        .test-link-btn:hover {
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="bulletin-container">
        <div class="bulletin-title">⚠️ Security Bulletin: Beware of Phishing Attempts</div>
        <div class="bulletin-message">
            <p class="important">Phishing attacks are fraudulent attempts to obtain sensitive information by pretending to be trustworthy entities.</p>
            <p>Please follow these important guidelines to protect yourself:</p>
            <ul>
                <li>Never click on suspicious links or attachments in emails or messages.</li>
                <li>Always verify the sender's email address and website URLs.</li>
                <li>Look for spelling mistakes or unusual requests in emails.</li>
                <li>Use two-factor authentication (2FA) whenever possible.</li>
                <li>Report suspicious emails to your IT department or security team immediately.</li>
            </ul>
            <p>If you receive any suspicious communications, <strong>do not respond or provide any personal information.</strong></p>

            <!-- Link styled as button -->
            <a href="phishing_link.php" class="test-link-btn">Test the suspicious link now</a>
            <a href="customer_news.php" class="test-link-btn" style="margin-left: 400px;">&larr; Go Back</a>
        </div>
        <div class="footer">
            Stay vigilant &mdash; Your security is our priority.
        </div>
    </div>
</body>
</html>
