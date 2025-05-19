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
$sql = "SELECT driver_name, image_url FROM drivers WHERE driver_id = '$driver_id'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
  $driver = $result->fetch_assoc();
  $driver_name = $driver['driver_name'];
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
  <link rel="stylesheet" href="assets/driver_side_landing.css">
  <title>Driver Dashboard</title>
</head>
<body>

<!-- Header with Logout -->
<header style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background-color: #f2f2f2; border-bottom: 1px solid #ddd;">
  <div style="display: flex; align-items: center; gap: 12px;">
    <img src="<?php echo $avatar_url; ?>" alt="Driver Avatar" class="avatar" style="width: 50px; height: 50px; border-radius: 50%;">
    <div class="welcome-text">Welcome, <?php echo htmlspecialchars($driver_name); ?>!</div>
  </div>
  <button onclick="return showLogoutModal();" style="padding: 8px 16px; background-color: #d9534f; color: white; border: none; border-radius: 5px; cursor: pointer;">
    🚪 Logout
  </button>
</header>

<?php if (isset($_GET['success'])): ?>
  <div style="background: #dff0d8; color: #3c763d; padding: 10px 20px;">
    ✅ Booking accepted successfully!
  </div>
<?php endif; ?>

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
    $booking_sql = "SELECT booking_id, name, pickup_location, dropoff_location FROM bookings WHERE status = 'Pending'";
    $booking_result = $conn->query($booking_sql);

    if ($booking_result && $booking_result->num_rows > 0) {
      while ($row = $booking_result->fetch_assoc()) {
        echo "<div class='booking-item'>";
        echo "<strong>Customer:</strong> " . htmlspecialchars($row['name']) . "<br/>";
        echo "<strong>From:</strong> " . htmlspecialchars($row['pickup_location']) . "<br/>";
        echo "<strong>To:</strong> " . htmlspecialchars($row['dropoff_location']) . "<br/>";

        // Accept Form
        echo "<form method='POST' action='accept_booking.php' style='display:inline-block; margin-right: 10px;'>";
        echo "<input type='hidden' name='booking_id' value='" . $row['booking_id'] . "'/>";
        echo "<button class='accept-btn' type='submit'>Accept Request</button>";
        echo "</form>";

        // Deny Form
        echo "<form method='POST' action='deny_booking.php' style='display:inline-block;'>";
        echo "<input type='hidden' name='booking_id' value='" . $row['booking_id'] . "'/>";
        echo "<button class='deny-btn' type='submit' onclick='return confirm(\"Are you sure you want to deny this booking?\");'>Deny Request</button>";
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

<!-- Footer Nav -->
<footer style="display: flex; justify-content: center; gap: 12px; padding: 15px; border-top: 1px solid #ccc; background: #f8f8f8;">
  <a href="driver_feedback.php" class="nav" style="display: flex; align-items: center; gap: 12px; text-decoration: none; padding: 10px; border: 1px solid #ccc; border-radius: 6px; color: inherit;">
    <span>📅</span><p style="margin: 0;">Feedback to Customer</p>
  </a>
  
  <a href="transaction.php" class="nav" style="display: flex; align-items: center; gap: 12px; text-decoration: none; padding: 10px; border: 1px solid #ccc; border-radius: 6px; color: inherit;">
    <span>📜</span><p style="margin: 0;">History</p>
  </a>

  <a href="driver_accept_payment.php" class="nav" style="display: flex; align-items: center; gap: 12px; text-decoration: none; padding: 10px; border: 1px solid #ccc; border-radius: 6px; color: inherit;">
    <span>💵</span><p style="margin: 0;">Payment</p>
  </a>
</footer>

<!-- Logout Modal -->
<div id="logoutModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
  <div style="background: white; width: 90%; max-width: 400px; margin: 10% auto; padding: 20px; border-radius: 8px; text-align: center;">
    <h3>Confirm Logout</h3>
    <p>Are you sure you want to logout?</p>
    <button onclick="logout()" style="padding: 10px 20px; margin-right: 10px; background-color: #d9534f; color: white; border: none; border-radius: 4px;">Yes</button>
    <button onclick="hideLogoutModal()" style="padding: 10px 20px; background-color: #5bc0de; color: white; border: none; border-radius: 4px;">Cancel</button>
  </div>
</div>

<script>
  function showLogoutModal() {
    document.getElementById('logoutModal').style.display = 'block';
    return false;
  }

  function hideLogoutModal() {
    document.getElementById('logoutModal').style.display = 'none';
  }

  function logout() {
    window.location.href = 'driver_login.php';
  }

  window.onclick = function(event) {
    const modal = document.getElementById('logoutModal');
    if (event.target === modal) {
      modal.style.display = "none";
    }
  };
</script>

</body>
</html>
