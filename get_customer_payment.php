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

// Check logged-in driver
$driver_id = $_SESSION['driver_id'] ?? null;

if (!$driver_id) {
    echo "Unauthorized access.";
    exit;
}

// Handle "Accept Payment" action
if (isset($_POST['accept_payment'])) {
    $payment_id = $_POST['payment_id'];

    // First, update payments table
    $stmt = $conn->prepare("UPDATE payments SET payment_status = 'Completed', paid_at = NOW() WHERE payment_id = ? AND driver_id = ?");
    $stmt->bind_param("ii", $payment_id, $driver_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Get booking_id related to this payment
        $stmt2 = $conn->prepare("SELECT booking_id FROM payments WHERE payment_id = ?");
        $stmt2->bind_param("i", $payment_id);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $booking_id = null;
        if ($row2 = $result2->fetch_assoc()) {
            $booking_id = $row2['booking_id'];
        }
        $stmt2->close();

        // Update bookings table payment_status to 'Paid'
        if ($booking_id) {
            $stmt3 = $conn->prepare("UPDATE bookings SET payment_status = 'Paid' WHERE booking_id = ?");
            $stmt3->bind_param("i", $booking_id);
            $stmt3->execute();
            $stmt3->close();
        }

        $message = "Payment accepted successfully!";
    } else {
        $message = "Failed to update payment.";
    }

    $stmt->close();
}

// Fetch all payments for the driver
$stmt = $conn->prepare("SELECT * FROM payments WHERE driver_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Driver Payment Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h2 {
            text-align: center;
            margin-top: 30px;
        }

        .container {
            width: 90%;
            margin: auto;
        }

        .message {
            text-align: center;
            color: green;
            font-weight: bold;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #bbb;
            text-align: center;
        }

        button {
            padding: 6px 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        .status-completed {
            color: green;
            font-weight: bold;
        }

        .status-pending {
            color: orange;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Driver Payment Dashboard</h2>

    <?php if (isset($message)) { echo "<div class='message'>{$message}</div>"; } ?>

    <table>
        <thead>
            <tr>
                <th>Payment ID</th>
                <th>Booking ID</th>
                <th>User</th>
                <th>Fare Amount</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Paid At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['payment_id'] ?></td>
                <td><?= $row['booking_id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td>₱<?= number_format($row['fare_amount'], 2) ?></td>
                <td><?= $row['payment_method'] ?></td>
                <td class="<?= $row['payment_status'] === 'Completed' ? 'status-completed' : 'status-pending' ?>">
                    <?= $row['payment_status'] ?>
                </td>
                <td><?= $row['paid_at'] ?? '-' ?></td>
                <td>
                    <?php if ($row['payment_status'] === 'Pending') { ?>
                        <form method="POST" style="margin: 0;">
                            <input type="hidden" name="payment_id" value="<?= $row['payment_id'] ?>">
                            <button type="submit" name="accept_payment">Accept Payment</button>
                        </form>
                    <?php } else {
                        echo "✔";
                    } ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
