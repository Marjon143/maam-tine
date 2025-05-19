<?php
include 'booking_db.php';

// Handle deletion if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_id'])) {
    $cancel_id = intval($_POST['cancel_id']);
    $delete_sql = "DELETE FROM bookings WHERE booking_id = $cancel_id";
    $conn->query($delete_sql);
}

// Fetch bookings
$sql = "SELECT * FROM bookings ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Info</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f3f3;
            padding: 30px;
        }
        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .booking-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
            width: 300px;
            position: relative;
        }
        .booking-card h3 {
            margin-top: 0;
        }
        .booking-detail {
            margin: 5px 0;
        }
        .status {
            padding: 4px 8px;
            border-radius: 5px;
            display: inline-block;
            font-weight: bold;
        }
        .Pending { background: #ffeb3b; }
        .Confirmed { background: #4caf50; color: #fff; }
        .Cancelled { background: #f44336; color: #fff; }
        .Unpaid { color: red; }
        .Paid { color: green; }
        .Refunded { color: orange; }
        .cancel-button {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .cancel-button:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>

<h2>Booking Information</h2>

<div class="card-container">
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="booking-card">
                <h3><?= htmlspecialchars($row['name']) ?></h3>
                <div class="booking-detail"><strong>Cellphone:</strong> <?= htmlspecialchars($row['cellphone']) ?></div>
                <div class="booking-detail"><strong>Vehicle:</strong> <?= htmlspecialchars($row['vehicle_type']) ?></div>
                <div class="booking-detail"><strong>Date:</strong> <?= htmlspecialchars($row['booking_date']) ?></div>
                <div class="booking-detail"><strong>Time:</strong> <?= htmlspecialchars($row['booking_time']) ?></div>
                <div class="booking-detail"><strong>Pickup:</strong> <?= htmlspecialchars($row['pickup_location']) ?></div>
                <div class="booking-detail"><strong>Dropoff:</strong> <?= htmlspecialchars($row['dropoff_location']) ?></div>
                <div class="booking-detail"><strong>Fare:</strong> ₱<?= number_format($row['fare_amount'], 2) ?></div>
                <div class="booking-detail"><strong>Status:</strong> 
                    <span class="status <?= $row['status'] ?>"><?= $row['status'] ?></span>
                </div>
                <div class="booking-detail"><strong>Payment:</strong> 
                    <span class="<?= $row['payment_status'] ?>"><?= $row['payment_status'] ?></span>
                </div>
                <div class="booking-detail" style="font-size: 12px; color: gray;">
                    <strong>Booked At:</strong> <?= $row['created_at'] ?>
                </div>

                <!-- Cancel Form -->
                <form method="post" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                    <input type="hidden" name="cancel_id" value="<?= $row['booking_id'] ?>">
                    <button type="submit" class="cancel-button">Cancel</button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No bookings found.</p>
    <?php endif; ?>
</div>

</body>
</html>
