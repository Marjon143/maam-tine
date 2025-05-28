<?php
// notification.php
$host = "localhost";
$dbname = "ecarga";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Mark a specific attempt as read
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['mark_read_id'])) {
            $markReadId = (int)$_POST['mark_read_id'];
            $updateStmt = $pdo->prepare("UPDATE login_attempts SET read_status = 1 WHERE attempt_id = ?");
            $updateStmt->execute([$markReadId]);
        }

        // Mark all as read
        if (isset($_POST['mark_all_read'])) {
            $updateAllStmt = $pdo->prepare("UPDATE login_attempts SET read_status = 1 WHERE read_status = 0");
            $updateAllStmt->execute();
        }

        // Avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Fetch login attempts
    $stmt = $pdo->prepare("SELECT attempt_id, email, ip_address, reason, user_id, attempt_time, read_status FROM login_attempts ORDER BY attempt_time DESC LIMIT 20");
    $stmt->execute();
    $attempts = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Attempts Notifications</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f8;
            padding: 30px;
            color: #333;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #4a90e2;
            color: white;
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .reason-wrong_password {
            color: #e74c3c;
            font-weight: bold;
        }

        .reason-wrong_email {
            color: #e67e22;
            font-weight: bold;
        }

        .mark-read {
            padding: 6px 14px;
            background-color: #28a745;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 6px;
            font-size: 0.9em;
        }

        .mark-read:disabled {
            background-color: #aaa;
            cursor: not-allowed;
        }

        .mark-all-container {
            text-align: right;
            margin-bottom: 10px;
        }

        .mark-all-btn {
            padding: 8px 16px;
            background-color: #007bff;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 6px;
            font-size: 0.95em;
        }

        .mark-all-btn:hover {
            background-color: #0056b3;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #4a90e2;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            th, td {
                padding: 10px;
            }

            .mark-read, .mark-all-btn {
                width: 100%;
            }

            .mark-all-container {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <h1>Login Attempts Notifications</h1>

    <div class="card">
        <?php if (count($attempts) === 0): ?>
            <p style="padding: 20px; text-align: center;">No login attempts found.</p>
        <?php else: ?>
            <div class="mark-all-container">
                <form method="post">
                    <input type="hidden" name="mark_all_read" value="1">
                    <button type="submit" class="mark-all-btn">Mark All as Read</button>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Attempt ID</th>
                        <th>Email</th>
                        <th>IP Address</th>
                        <th>Reason</th>
                        <th>User ID</th>
                        <th>Attempt Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attempts as $attempt): ?>
                        <tr>
                            <td><?= htmlspecialchars($attempt['attempt_id']) ?></td>
                            <td><?= htmlspecialchars($attempt['email']) ?></td>
                            <td><?= htmlspecialchars($attempt['ip_address']) ?></td>
                            <td class="reason-<?= htmlspecialchars($attempt['reason']) ?>">
                                <?= htmlspecialchars(str_replace('_', ' ', ucfirst($attempt['reason']))) ?>
                            </td>
                            <td><?= htmlspecialchars($attempt['user_id'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($attempt['attempt_time']) ?></td>
                            <td>
                                <?php if ($attempt['read_status'] == 0): ?>
                                    <form method="post" style="margin:0;">
                                        <input type="hidden" name="mark_read_id" value="<?= htmlspecialchars($attempt['attempt_id']) ?>">
                                        <button type="submit" class="mark-read">Mark as Read</button>
                                    </form>
                                <?php else: ?>
                                    <button class="mark-read" disabled>Read</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <a href="customer_landing.php" class="back-link">← Back to Home</a>
</body>
</html>
