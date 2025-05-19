<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ecarga");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check login
if (!isset($_SESSION['driver_id'])) {
    header("Location: login.php");
    exit;
}
$driver_id = $_SESSION['driver_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $transaction_id = $_POST['transaction_id'] ?? null;
    $driver_name = trim($_POST['driver_name'] ?? '');
    $comments = trim($_POST['comments'] ?? '');

    if (empty($transaction_id) || empty($comments)) {
        echo "<script>alert('Please fill all required fields.');</script>";
    } else {
        // Correct way to fetch user_id and customer_name
        $stmt = $conn->prepare("SELECT user_id, name FROM transactions WHERE transaction_id = ? AND driver_id = ?");
        $stmt->bind_param("ii", $transaction_id, $driver_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $user_id = $row['user_id'];
            $customer_name = $row['name'];
            $stmt->close();

            // Insert feedback
            $stmt = $conn->prepare("INSERT INTO driver_feedback (user_id, driver_id, driver_name, name, comments) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisss", $user_id, $driver_id, $driver_name, $customer_name, $comments);
            if ($stmt->execute()) {
                echo "<script>alert('Feedback submitted successfully!'); window.location.href='driver_side_landing.php';</script>";
                exit;
            } else {
                echo "<script>alert('Error submitting feedback. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('Invalid transaction selected.');</script>";
        }
    }
}


// Fetch transactions for the logged-in driver
$sql = "SELECT transaction_id, booking_id, user_id, name, pickup_location, dropoff_location, action, transaction_status, action_time 
        FROM transactions WHERE driver_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Leave Customer Feedback</title>
    <link rel="stylesheet" href="assets/feedback.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; padding: 30px; }
        .form-container { max-width: 600px; margin: auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input, textarea, select { width: 100%; padding: 10px; margin-top: 8px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc; }
        button { padding: 10px 20px; background-color: #007bff; border: none; color: white; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .go-back { background-color: #6c757d; }
        .go-back:hover { background-color: #5a6268; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Leave Feedback About Your Customer</h2>
    <form method="POST" action="">
        <label>Your Name (optional):</label>
        <input type="text" name="driver_name" placeholder="Your name">

        <label>Select Transaction:</label>
        <select name="transaction_id" required>
            <option value="">-- Select Your Transaction --</option>
            <?php while ($row = $result->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($row['transaction_id']) ?>">
                    Transaction #<?= htmlspecialchars($row['transaction_id']) ?> - Customer: <?= htmlspecialchars($row['name']) ?> - Pickup: <?= htmlspecialchars($row['pickup_location']) ?> - Dropoff: <?= htmlspecialchars($row['dropoff_location']) ?> - Status: <?= htmlspecialchars($row['transaction_status']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Your Feedback:</label>
        <textarea name="comments" rows="4" placeholder="Write your feedback here..." required></textarea>

        <button type="submit">Submit Feedback</button>
        <button type="button" class="go-back" onclick="window.location.href='driver_side_landing.php'">Go Back</button>
    </form>
</div>

</body>
</html>
