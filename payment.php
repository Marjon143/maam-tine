<?php
// DB connection
$conn = new mysqli("localhost", "root", "", "ecarga");

$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
$user_id = '';
$user_name = '';
$driver_id = '';
$driver_name = '';
$amount = '';

// Fetch bookings for dropdown
$bookings_result = $conn->query("SELECT booking_id FROM bookings ORDER BY booking_id ASC");

// Fetch booking info if booking_id is selected
if ($booking_id > 0) {
   $sql = "SELECT b.booking_id, b.user_id, u.name AS user_name, b.driver_id, d.driver_name AS driver_name, b.amount 
        FROM bookings b
        LEFT JOIN users u ON b.user_id = u.user_id
        LEFT JOIN drivers d ON b.driver_id = d.driver_id
        WHERE b.booking_id = $booking_id
        LIMIT 1";

    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $booking_id = $row['booking_id'];
        $user_id = $row['user_id'];
        $user_name = $row['user_name'];
        $driver_id = $row['driver_id'];
        $driver_name = $row['driver_name'];
        $amount = $row['amount'];
    } else {
        echo "<script>alert('Booking ID not found.');</script>";
        $booking_id = 0;
    }
}

// Handle payment submission
if (isset($_POST['pay'])) {
    $booking_id = $_POST['booking_id'];
    $user_id = $_POST['user_id'];
    $driver_id = $_POST['driver_id'];
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $transaction_reference = $_POST['transaction_reference'];
    $paid_at = date('Y-m-d H:i:s');

    // Fetch user name for security
    $user_name = '';
    $user_result = $conn->query("SELECT name FROM users WHERE user_id = $user_id");
    if ($user_result && $user_result->num_rows > 0) {
        $user_name = $user_result->fetch_assoc()['name'];
    }

    // Fetch driver name
    $driver_name = '';
    $driver_result = $conn->query("SELECT name FROM drivers WHERE driver_id = $driver_id");
    if ($driver_result && $driver_result->num_rows > 0) {
        $driver_name = $driver_result->fetch_assoc()['name'];
    }

    // Insert payment
    $sql = "INSERT INTO payments (booking_id, user_id, driver_id, name, drivers_name, amount, payment_method, payment_status, transaction_reference, paid_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Completed', ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiissssss", $booking_id, $user_id, $driver_id, $user_name, $driver_name, $amount, $payment_method, $transaction_reference, $paid_at);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Payment successful!');</script>";
    } else {
        echo "<script>alert('❌ Payment failed: " . $stmt->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ECARGA | Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }
        .payment-card {
            max-width: 600px;
            margin: 40px auto;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            background: white;
        }
        .payment-header {
            text-align: center;
            margin-bottom: 25px;
        }
        .payment-header h2 {
            font-weight: bold;
        }
        .form-control, .form-select {
            border-radius: 10px;
        }
        .btn-primary {
            width: 100%;
            border-radius: 10px;
            padding: 10px;
        }
    </style>
</head>
<body>

<div class="payment-card">
    <div class="payment-header">
        <img src="https://cdn-icons-png.flaticon.com/512/891/891419.png" width="60" alt="Payment Icon">
        <h2>ECARGA Payment</h2>
        <p class="text-muted">Select your Booking ID to load details</p>
    </div>

    <!-- Booking ID selector -->
    <form method="GET" class="mb-4">
        <label for="booking_id_select" class="form-label">Select Booking ID</label>
        <select id="booking_id_select" name="booking_id" class="form-select" onchange="this.form.submit()" required>
            <option value="" disabled <?= $booking_id == 0 ? 'selected' : '' ?>>-- Choose Booking ID --</option>
            <?php
            if ($bookings_result && $bookings_result->num_rows > 0) {
                while ($booking = $bookings_result->fetch_assoc()) {
                    $id = $booking['booking_id'];
                    $selected = ($id == $booking_id) ? 'selected' : '';
                    echo "<option value='$id' $selected>$id</option>";
                }
            }
            ?>
        </select>
    </form>

    <?php if ($booking_id > 0): ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Booking ID</label>
            <input type="number" name="booking_id" class="form-control" value="<?= htmlspecialchars($booking_id) ?>" readonly required>
        </div>

        <div class="mb-3">
            <label class="form-label">User ID</label>
            <input type="number" name="user_id" class="form-control" value="<?= htmlspecialchars($user_id) ?>" readonly required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="users_name" class="form-control" value="<?= htmlspecialchars($user_name) ?>" readonly required>
        </div>

        <div class="mb-3">
            <label class="form-label">Driver ID</label>
            <input type="number" name="driver_id" class="form-control" value="<?= htmlspecialchars($driver_id) ?>" readonly required>
        </div>

        <div class="mb-3">
            <label class="form-label">Drivers Name</label>
            <input type="text" name="drivers_name" class="form-control" value="<?= htmlspecialchars($driver_name) ?>" readonly required>
        </div>

        <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" value="<?= htmlspecialchars($amount) ?>" readonly required>
        </div>

        <div class="mb-3">
            <label class="form-label">Payment Method</label>
            <select name="payment_method" class="form-select" required>
                <option value="" selected disabled>-- Select Method --</option>
                <option value="Cash">Cash</option>
                <option value="GCash">GCash</option>
                <option value="Credit Card">Credit Card</option>
                <option value="Debit Card">Debit Card</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Transaction Reference (optional)</label>
            <input type="text" name="transaction_reference" class="form-control" placeholder="e.g., GCash Ref No.">
        </div>

        <button type="submit" name="pay" class="btn btn-primary">
            Pay Now
        </button>
    </form>
    <?php else: ?>
        <p class="text-center text-muted">Please select a booking ID above to load the payment form.</p>
    <?php endif; ?>
</div>

</body>
</html>
