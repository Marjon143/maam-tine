<?php
session_start();

// DB connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "ecarga";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$driver_id = $_SESSION['driver_id'] ?? null;
if (!$driver_id) {
    echo "Unauthorized access.";
    exit;
}

// Accept payment
if (isset($_POST['accept_payment'])) {
    $payment_id = $_POST['payment_id'];

    $stmt = $conn->prepare("UPDATE payments SET payment_status = 'Completed', paid_at = NOW() WHERE payment_id = ? AND driver_id = ?");
    $stmt->bind_param("ii", $payment_id, $driver_id);
    $stmt->execute();
    $success = ($stmt->affected_rows > 0);

    if ($success) {
        $stmt2 = $conn->prepare("SELECT booking_id FROM payments WHERE payment_id = ?");
        $stmt2->bind_param("i", $payment_id);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $booking = $result2->fetch_assoc();
        $booking_id = $booking['booking_id'];

        if ($booking_id) {
            $stmt3 = $conn->prepare("UPDATE bookings SET payment_status = 'Paid' WHERE booking_id = ?");
            $stmt3->bind_param("i", $booking_id);
            $stmt3->execute();
        }

        $message = "✅ Payment accepted successfully!";
    } else {
        $message = "❌ Failed to update payment.";
    }
}

// Fetch driver payments
$stmt = $conn->prepare("SELECT * FROM payments WHERE driver_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Payment Dashboard</title>
    <link rel="stylesheet" href="assets/driver_accept_payment.css">
    
</head>
<body>
    <a href="driver_side_landing.php" class="go-back-btn">← Go Back</a>
<div class="container">
    <h2>Driver Payment Dashboard</h2>

    <?php if (isset($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Payment ID</th>
                <th>Booking ID</th>
                <th>User</th>
                <th>Fare</th>
                <th>Method</th>
                <th>Status</th>
                <th>Paid At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td data-label="Payment ID"><?= $row['payment_id'] ?></td>
                <td data-label="Booking ID"><?= $row['booking_id'] ?></td>
                <td data-label="User"><?= htmlspecialchars($row['name']) ?></td>
                <td data-label="Fare">₱<?= number_format($row['fare_amount'], 2) ?></td>
                <td data-label="Method"><?= $row['payment_method'] ?></td>
                <td data-label="Status" class="<?= $row['payment_status'] === 'Completed' ? 'status-completed' : 'status-pending' ?>">
                    <?= $row['payment_status'] ?>
                </td>
                <td data-label="Paid At"><?= $row['paid_at'] ?? '-' ?></td>
                <td data-label="Action">
                    <?php if ($row['payment_status'] === 'Pending'): ?>
                        <form method="POST" style="margin: 0;">
                            <input type="hidden" name="payment_id" value="<?= $row['payment_id'] ?>">
                            <button type="submit" name="accept_payment">Accept</button>
                        </form>
                    <?php else: ?>
                        ✔
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
