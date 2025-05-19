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
<style>
  /* Reset and base */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
  background-color: #f9fafb;
  color: #333;
  line-height: 1.6;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* Header */
header {
  background-color: #005f73;
  color: white;
  padding: 15px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 6px rgb(0 0 0 / 0.1);
}

.avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  border: 2px solid #94d2bd;
  object-fit: cover;
  box-shadow: 0 0 5px rgba(0,0,0,0.2);
}

.welcome-text {
  font-weight: 600;
  font-size: 1.2rem;
  margin-left: 12px;
}

header button {
  background-color: #ee6c4d;
  border: none;
  padding: 10px 20px;
  font-size: 1rem;
  border-radius: 8px;
  color: white;
  cursor: pointer;
  transition: background-color 0.3s ease;
  box-shadow: 0 2px 6px rgb(0 0 0 / 0.2);
}

header button:hover {
  background-color: #d94f33;
}

/* Success message */
div[style*="background: #dff0d8"] {
  margin: 20px auto;
  max-width: 600px;
  background: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
  border-radius: 8px;
  padding: 12px 20px;
  font-weight: 600;
  text-align: center;
  box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);
}

/* Container */
.container {
  flex-grow: 1;
  padding: 30px 20px;
  max-width: 900px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr;
  gap: 30px;
}

/* Sections */
.section {
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 4px 8px rgb(0 0 0 / 0.05);
  padding: 25px 30px;
  transition: box-shadow 0.3s ease;
}

.section:hover {
  box-shadow: 0 6px 12px rgb(0 0 0 / 0.1);
}

.section h2 {
  margin-bottom: 18px;
  font-size: 1.5rem;
  color: #0a9396;
  border-bottom: 2px solid #94d2bd;
  padding-bottom: 8px;
}

/* News items */
.news-item {
  background: #e0fbfc;
  padding: 12px 15px;
  border-radius: 6px;
  margin-bottom: 10px;
  font-size: 1rem;
  box-shadow: inset 1px 1px 5px rgb(0 0 0 / 0.05);
}

/* Booking Items */
.booking-item {
  background-color: #caf0f8;
  padding: 15px 20px;
  border-radius: 10px;
  margin-bottom: 16px;
  box-shadow: 0 1px 4px rgb(0 0 0 / 0.1);
  font-size: 1rem;
  line-height: 1.4;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 15px;
}

.booking-item strong {
  color: #005f73;
  margin-right: 5px;
  min-width: 85px;
  display: inline-block;
}

.booking-item form {
  margin: 0;
}

/* Buttons */
.accept-btn,
.deny-btn {
  padding: 8px 14px;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s ease;
  box-shadow: 0 2px 5px rgb(0 0 0 / 0.1);
  font-size: 0.9rem;
}

.accept-btn {
  background-color: #52b788;
  color: white;
  margin-right: 8px;
}

.accept-btn:hover {
  background-color: #40916c;
}

.deny-btn {
  background-color: #e63946;
  color: white;
}

.deny-btn:hover {
  background-color: #a61c29;
}

/* Gasoline prices */
.gas-price {
  font-weight: 700;
  color: #ee6c4d;
  font-size: 1.1rem;
}

/* Footer Nav */
footer {
  background-color: #94d2bd;
  padding: 15px 30px;
  display: flex;
  justify-content: center;
  gap: 20px;
  box-shadow: 0 -3px 6px rgb(0 0 0 / 0.1);
  border-top-left-radius: 12px;
  border-top-right-radius: 12px;
  flex-wrap: wrap;
}

footer .nav {
  background: white;
  color: #005f73;
  border-radius: 10px;
  padding: 12px 18px;
  font-weight: 600;
  font-size: 1rem;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 10px;
  box-shadow: 0 2px 6px rgb(0 0 0 / 0.15);
  transition: background-color 0.3s ease, color 0.3s ease;
}

footer .nav:hover {
  background-color: #005f73;
  color: white;
  box-shadow: 0 4px 12px rgb(0 0 0 / 0.25);
}

footer .nav span {
  font-size: 1.3rem;
}

/* Logout modal */
#logoutModal {
  display: none;
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.6);
  display: flex;
  align-items: center;
  justify-content: center;
}

#logoutModal > div {
  background: white;
  width: 90%;
  max-width: 400px;
  padding: 30px 25px;
  border-radius: 12px;
  text-align: center;
  box-shadow: 0 8px 24px rgb(0 0 0 / 0.2);
}

#logoutModal h3 {
  margin-bottom: 20px;
  color: #d9534f;
}

#logoutModal p {
  margin-bottom: 25px;
  font-size: 1.1rem;
  color: #444;
}

#logoutModal button {
  padding: 12px 25px;
  border-radius: 6px;
  border: none;
  font-weight: 600;
  font-size: 1rem;
  margin: 0 8px;
  cursor: pointer;
  box-shadow: 0 3px 8px rgb(0 0 0 / 0.1);
  transition: background-color 0.3s ease;
}

#logoutModal button:first-child {
  background-color: #d9534f;
  color: white;
}

#logoutModal button:first-child:hover {
  background-color: #b53b3b;
}

#logoutModal button:last-child {
  background-color: #5bc0de;
  color: white;
}

#logoutModal button:last-child:hover {
  background-color: #3996b6;
}

/* Responsive */
@media (min-width: 768px) {
  .container {
    grid-template-columns: 1fr 1fr;
  }
  .booking-item {
    flex-wrap: nowrap;
  }
}

</style>
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
