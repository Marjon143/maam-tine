<?php
session_start();

if (!isset($_SESSION['driver_id'])) {
  die("Unauthorized access.");
}

$conn = new mysqli("localhost", "root", "", "ecarga");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $driver_id = $_SESSION['driver_id'];
  $booking_id = $_POST['booking_id'];

  $booking_query = $conn->prepare("SELECT name, pickup_location, dropoff_location FROM bookings WHERE booking_id = ?");
  $booking_query->bind_param("i", $booking_id);
  $booking_query->execute();
  $booking_result = $booking_query->get_result();

  if ($booking_result && $booking_result->num_rows > 0) {
    $row = $booking_result->fetch_assoc();

    $stmt = $conn->prepare("UPDATE bookings SET status = 'Denied' WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();

    $stmt2 = $conn->prepare("INSERT INTO transactions (booking_id, driver_id, customer_name, pickup_location, dropoff_location, action, transaction_status) VALUES (?, ?, ?, ?, ?, 'Denied', 'Done')");
    $stmt2->bind_param("iisss", $booking_id, $driver_id, $row['name'], $row['pickup_location'], $row['dropoff_location']);
    $stmt2->execute();

    header("Location: transation.php?denied=1");
    exit();
  }
}
$conn->close();
?>
