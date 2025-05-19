<?php
session_start();

if (!isset($_SESSION['driver_id'])) {
  die("Unauthorized access.");
}

$driver_id = $_SESSION['driver_id'];

$conn = new mysqli("localhost", "root", "", "ecarga");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['booking_id'])) {
  $booking_id = intval($_POST['booking_id']);

  // Ensure booking belongs to the driver
  $stmt = $conn->prepare("SELECT name, pickup_location, dropoff_location FROM bookings WHERE booking_id = ? AND driver_id = ?");
  $stmt->bind_param("ii", $booking_id, $driver_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Deny the booking
    $update = $conn->prepare("UPDATE bookings SET status = 'Denied' WHERE booking_id = ?");
    $update->bind_param("i", $booking_id);
    $update->execute();

    // Log transaction
    $log = $conn->prepare("INSERT INTO transactions (booking_id, driver_id, customer_name, pickup_location, dropoff_location, action, transaction_status) 
                           VALUES (?, ?, ?, ?, ?, 'Denied', 'Done')");
    $log->bind_param("iisss", $booking_id, $driver_id, $row['name'], $row['pickup_location'], $row['dropoff_location']);
    $log->execute();

    header("Location: transation.php?denied=1");
    exit();
  } else {
    echo "❌ Unauthorized: You cannot deny a booking not assigned to you.";
  }

  $stmt->close();
}
$conn->close();
?>
