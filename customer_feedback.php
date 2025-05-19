<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ecarga");

// Assume logged-in user ID is stored here:
$user_id = $_SESSION['user_id'] ?? 1; // Replace 1 with real session logic or redirect if not logged in

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $driver_id = $_POST['driver_id'];
    $booking_id = $_POST['booking_id'];
    $customer_name = trim($_POST['customer_name']);
    $comments = trim($_POST['comments']);

    if (!empty($driver_id) && !empty($comments) && !empty($booking_id)) {
        $stmt = $conn->prepare("INSERT INTO customer_feedback (booking_id, driver_id, customer_name, comments) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $booking_id, $driver_id, $customer_name, $comments);
        $stmt->execute();
        echo "<script>alert('Feedback submitted successfully!');</script>";
    } else {
        echo "<script>alert('Please fill all required fields.');</script>";
    }
}

// Fetch bookings for the logged-in user
$sql = "SELECT booking_id, driver_id, booking_date, vehicle_type FROM bookings WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Leave Driver Feedback</title>
    <link rel="stylesheet" href="assets/feedback.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; padding: 30px; }
        .form-container { max-width: 600px; margin: auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input, textarea, select { width: 100%; padding: 10px; margin-top: 8px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc; }
        button { padding: 10px 20px; background-color: #28a745; border: none; color: white; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #218838; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Leave Feedback for Your Driver</h2>
    <form method="POST">
        <label>Your Name (optional):</label>
        <input type="text" name="customer_name" placeholder="Your name">

        <label>Select Booking:</label>
        <select name="booking_id" id="bookingSelect" required onchange="updateDriverId()">
            <option value="">-- Select Your Booking --</option>
            <?php while ($row = $result->fetch_assoc()): ?>
                <option 
                    value="<?= $row['booking_id'] ?>" 
                    data-driver-id="<?= $row['driver_id'] ?>">
                    Booking #<?= $row['booking_id'] ?> - <?= htmlspecialchars($row['vehicle_type']) ?> - <?= $row['booking_date'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Driver ID:</label>
        <input type="text" name="driver_id" id="driverIdInput" readonly placeholder="Driver ID auto-filled">

        <label>Your Feedback:</label>
        <textarea name="comments" rows="4" placeholder="Write your feedback here..." required></textarea>

        <button type="submit">Submit Feedback</button>
        <button type="button" class="go-back" onclick="window.location.href='customer_landing.php'">Go Back</button>


    </form>
</div>

<script>
function updateDriverId() {
    const select = document.getElementById('bookingSelect');
    const driverIdInput = document.getElementById('driverIdInput');
    const selectedOption = select.options[select.selectedIndex];
    driverIdInput.value = selectedOption.getAttribute('data-driver-id') || '';
}
</script>

</body>
</html>
