<?php
session_start();

// Temporary fallback session
if (!isset($_SESSION['driver_id'])) {
  $_SESSION['driver_id'] = 1; // Replace with real login logic later
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecarga";

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get driver info
$driver_id = $_SESSION['driver_id'];
$sql = "SELECT name, image_url FROM drivers WHERE driver_id = '$driver_id'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
  $driver = $result->fetch_assoc();
  $driver_name = $driver['name'];
  $avatar_url = !empty($driver['image_url']) ? $driver['image_url'] : 'uploads/default-avatar.png';
} else {
  $driver_name = "Driver";
  $avatar_url = 'uploads/default-avatar.png';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Driver Dashboard</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    header {
      background: #1e88e5;
      color: white;
      padding: 15px 20px;
    }
    .header-content {
      display: flex;
      align-items: center;
    }
    .avatar {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid white;
      box-shadow: 0 0 5px rgba(0,0,0,0.2);
    }
    .welcome-text {
      margin-left: 15px;
      font-size: 1.5em;
    }
    .container {
      flex: 1;
      display: flex;
      flex-direction: column;
      padding: 20px;
      gap: 20px;
      overflow-y: auto;
    }
    .section {
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    h2 {
      margin-top: 0;
      color: #333;
    }
    .booking-item, .news-item {
      border-bottom: 1px solid #ddd;
      padding: 10px 0;
    }
    .gas-price {
      font-size: 1.5em;
      color: #388e3c;
      margin-top: 10px;
    }
    footer {
      background: #eee;
      padding: 20px;
      text-align: center;
    }
    .footer-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-around;
      gap: 20px;
      max-width: 1200px;
      margin: auto;
    }
    .footer-section {
      flex: 1 1 300px;
      text-align: left;
    }
    textarea {
      width: 100%;
      padding: 10px;
      border-radius: 4px;
      border: 1px solid #ccc;
      resize: vertical;
    }
    button.submit-feedback {
      margin-top: 10px;
      padding: 8px 15px;
      background-color: #1e88e5;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .payment-info {
      font-size: 1.1em;
      color: #333;
    }
    button.accept-btn {
      margin-top: 8px;
      padding: 6px 12px;
      background: #4caf50;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
  </style>
</head>
<body>

<header>
  <div class="header-content">
    <img src="<?php echo $avatar_url; ?>" alt="Driver Avatar" class="avatar">
    <div class="welcome-text">Welcome, <?php echo htmlspecialchars($driver_name); ?>!</div>
  </div>
</header>

<div class="container">
  <!-- Latest News -->
  <div class="section">
    <h2>Latest News</h2>
    <div class="news-item">📢 ECarga expands coverage to new cities!</div>
    <div class="news-item">🛠 System maintenance scheduled for Sunday 12AM.</div>
    <div class="news-item">💰 New bonus program for top-rated drivers.</div>
  </div>

  <!-- Booking Requests Section -->
  <div class="section">
    <h2>Booking Requests</h2>
    <?php
    $booking_sql = "SELECT booking_id,name, pickup_location, dropoff_location FROM bookings WHERE status = 'pending'";
    $booking_result = $conn->query($booking_sql);

    if ($booking_result && $booking_result->num_rows > 0) {
      while ($row = $booking_result->fetch_assoc()) {
        echo "<div class='booking-item'>";
        echo "<strong>Customer:</strong> " . htmlspecialchars($row['customer_name']) . "<br/>";
        echo "<strong>From:</strong> " . htmlspecialchars($row['pickup_location']) . "<br/>";
        echo "<strong>To:</strong> " . htmlspecialchars($row['dropoff_location']) . "<br/>";
        echo "<form method='POST' action='accept_booking.php'>";
        echo "<input type='hidden' name='booking_id' value='" . $row['booking_id'] . "'/>";
        echo "<button class='accept-btn' type='submit'>Accept Request</button>";
        echo "</form>";
        echo "</div>";
      }
    } else {
      echo "<p>No pending bookings.</p>";
    }
    ?>
  </div>

  <!-- Gasoline Prices -->
  <div class="section">
    <h2>Gasoline Prices</h2>
    <p>⛽ Unleaded: <span class="gas-price">₱65.25 / L</span></p>
    <p>⛽ Diesel: <span class="gas-price">₱58.30 / L</span></p>
    <p>⛽ Premium: <span class="gas-price">₱70.10 / L</span></p>
  </div>
</div>

<!-- Footer -->
<footer>
  <div class="footer-container">
    <!-- Feedback -->
    <div class="footer-section">
      <h3>Feedback</h3>
      <form method="post" action="submit_feedback.php">
        <textarea name="feedback" rows="4" placeholder="Leave your feedback here..."></textarea>
        <br/>
        <button type="submit" class="submit-feedback">Submit</button>
      </form>
    </div>

    <!-- Payment Info -->
    <div class="footer-section">
      <h3>Payment Information</h3>
      <p class="payment-info">💸 Current Balance: ₱4,250.00</p>
      <p class="payment-info">🧾 Last Payout: ₱2,000.00 on May 10, 2025</p>
    </div>
  </div>
  <div class="copyright">
    &copy; 2025 ECarga. All rights reserved.
  </div>
</footer>

</body>
</html>
