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
<header class="header">
  <div class="header-left">
    <img src="<?php echo $avatar_url; ?>" alt="Driver Avatar" class="avatar">
    <div class="welcome-text">Welcome, <?php echo htmlspecialchars($driver_name); ?>!</div>
  </div>
  <button onclick="return showLogoutModal();" class="logout-btn">🚪 Logout</button>
</header>

<?php if (isset($_GET['success'])): ?>
  <div class="success-alert">
    ✅ Booking accepted successfully!
  </div>
<?php endif; ?>

<div class="container">
  <!-- Latest News -->
  <div class="section">
    <h2>Latest News</h2>
    <div class="news-item">📢 Marmyles Booking expands coverage to new cities!</div>
    <div class="news-item">🛠 System maintenance scheduled for Sunday 12AM.</div>
    <div class="news-item">💰 New bonus program for top-rated drivers.</div>
  </div>

  <!-- Booking Requests Section -->
  <div class="section">
    <h2>Booking Requests</h2>
    <?php
    $booking_sql = "SELECT booking_id, name, pickup_location, dropoff_location 
                    FROM bookings 
                    WHERE status = 'Pending' AND driver_id = '$driver_id'";
    $booking_result = $conn->query($booking_sql);

    if ($booking_result && $booking_result->num_rows > 0) {
      while ($row = $booking_result->fetch_assoc()) {
        echo "<div class='booking-item'>";
        echo "<strong>Customer:</strong> " . htmlspecialchars($row['name']) . "<br/>";
        echo "<strong>From:</strong> " . htmlspecialchars($row['pickup_location']) . "<br/>";
        echo "<strong>To:</strong> " . htmlspecialchars($row['dropoff_location']) . "<br/>";
        echo "<form method='POST' action='accept_booking.php' class='booking-form'>";
        echo "<input type='hidden' name='booking_id' value='" . $row['booking_id'] . "'/>";
        echo "<button class='accept-btn' type='submit'>Accept Request</button>";
        echo "</form>";
        echo "<form method='POST' action='deny_booking.php' class='booking-form'>";
        echo "<input type='hidden' name='booking_id' value='" . $row['booking_id'] . "'/>";
        echo "<button class='deny-btn' type='submit' onclick='return confirm(\"Are you sure you want to deny this booking?\");'>Deny Request</button>";
        echo "</form>";
        echo "</div>";
      }
    } else {
      echo "<p>No pending bookings assigned to you.</p>";
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
<footer class="footer">
  <a href="driver_feedback.php" class="nav">
    <span>📅</span><p>Feedback to Customer</p>
  </a>
  <a href="transaction.php" class="nav">
    <span>📜</span><p>History</p>
  </a>
  <a href="driver_accept_payment.php" class="nav">
    <span>💵</span><p>Payment</p>
  </a>
</footer>

<!-- Logout Modal -->
<div id="logoutModal" class="modal-overlay">
  <div class="modal">
    <h3>Confirm Logout</h3>
    <p>Are you sure you want to logout?</p>
    <button onclick="logout()" class="modal-btn logout-confirm">Yes</button>
    <button onclick="hideLogoutModal()" class="modal-btn cancel-btn">Cancel</button>
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
