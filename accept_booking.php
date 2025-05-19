<?php
session_start();

// Ensure driver is logged in
if (!isset($_SESSION['driver_id'])) {
    die("Unauthorized access");
}

$driver_id = $_SESSION['driver_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);

    // Connect to DB
    $conn = new mysqli("localhost", "root", "", "ecarga");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get booking info (including user_id)
    $stmt = $conn->prepare("SELECT user_id, name, pickup_location, dropoff_location FROM bookings WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($booking = $result->fetch_assoc()) {
        $user_id = $booking['user_id'];
        $name = $booking['name'];
        $pickup_location = $booking['pickup_location'];
        $dropoff_location = $booking['dropoff_location'];

        // Insert into transactions
        $insert = $conn->prepare("INSERT INTO transactions (booking_id, driver_id, user_id, name, pickup_location, dropoff_location, action, transaction_status) 
                                  VALUES (?, ?, ?, ?, ?, ?, 'Accepted', 'Ongoing')");
        $insert->bind_param("iiisss", $booking_id, $driver_id, $user_id, $name, $pickup_location, $dropoff_location);
        $insert->execute();
        $insert->close();

        // ✅ Update booking status to 'Confirmed'
        $update = $conn->prepare("UPDATE bookings SET status = 'Confirmed' WHERE booking_id = ?");
        $update->bind_param("i", $booking_id);
        $update->execute();
        $update->close();

        // Redirect with success flag
        header("Location: driver_side_landing.php?success=1");
        exit();
    } else {
        echo "Booking not found.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
