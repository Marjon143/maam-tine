<?php
session_start();
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: auth.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "ecarga");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ fix: load only current user’s name and avatar_url
$sql = "SELECT name, avatar_url FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $avatar_url);
$stmt->fetch();
$stmt->close();
?>

  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>ECARGA</title>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <header>
      <div class="header-left">
        <img src="https://cdn-icons-png.flaticon.com/512/684/684908.png" class="flag" alt="App Logo" />
        <h1>E<span>CARGA</span> <span class="beta">TM</span></h1>
      </div>
    </header>

    <main>
      <section class="welcome">
      <img src="<?php echo $avatar_url; ?>" class="profile-pic" alt="Profile Picture">
<h2>Welcome, <?php echo $username; ?></h2>
        <p>Ready to book your next service?</p>
      </section>

      <section class="services">
        <div class="service"><img src="image/Graphicloads-100-Flat-2-Bus.64.png"><span>Vehicle</span></div>
        <a href="driver_info.php"><div class="service"><img src="image/Icons8-Windows-8-Transport-Driver.64.png" alt="Drivers"><span>Drivers</span></div></a>
        <a href="driver_rating.php"><div class="service"><img src="image/Icons8-Windows-8-Very-Basic-Rating.64.png" alt="Ratings Icon"><span>Ratings</span></div></a>

      </section>

      <section class="booking-banner">
        <h3>Book Now</h3>
        <p>Select a service and schedule your appointment.</p>
        <a href="bookings.php" class="book-btn">Start Booking</a>

      </section>

      <style>
  .recent-activity {
    background-color: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin: 20px 0;
  }

  .recent-activity h3 {
    font-size: 1.5rem;
    color: #333;
    margin-bottom: 15px;
    text-align: center;
  }

  .activity-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
  }

  .activity-table th,
  .activity-table td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #ddd;
    font-weight: bold;
    font-size: 18px; /* Adjust this value as needed */
}

  .activity-table th {
    font-weight: bold;
  }

  .activity-table th:nth-child(1) {
    background-color: #4CAF50;
    color: white;
  }

  .activity-table th:nth-child(2) {
    background-color: #2196F3;
    color: white;
  }

  .activity-table th:nth-child(3) {
    background-color: #FF9800;
    color: white;
  }
  .activity-table th:nth-child(4) {
    background-color:rgb(85, 40, 234);
    color: white;
  }
  .activity-table tr:hover {
    background-color: #f1f1f1;
  }

  .activity-table td {
    color: #555;
  }

  .activity-table td:nth-child(3) {
    font-weight: bold;
  }

  .recent-activity p {
    color: #777;
    font-size: 1rem;
    font-style: italic;
  }
</style>

<section class="recent-activity">
  <h3>Recent Activity</h3>
  <?php
    // Re-establish DB connection
    $conn = new mysqli("localhost", "root", "", "ecarga");
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    // Fetch recent bookings: name, booking_date, booking_time, status
    $sql = "SELECT name, booking_date, booking_time, status FROM bookings WHERE user_id = ? ORDER BY booking_date DESC, booking_time DESC LIMIT 5";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0): ?>
      <table class="activity-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['name']); ?></td>
              <td><?php echo htmlspecialchars(date("M d, Y", strtotime($row['booking_date']))); ?></td>
              <td><?php echo htmlspecialchars(date("h:i A", strtotime($row['booking_time']))); ?></td>
              <td><?php echo htmlspecialchars($row['status']); ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No recent activity found.</p>
    <?php endif;

    $stmt->close();
    $conn->close();
  ?>
</section>
<style>
  .fare-route {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
  }

  .fare-route h3 {
    font-size: 1.5rem;
    color: #0b5394;
    margin-bottom: 15px;
    text-align: center;
    
  }

  .fare-table-container {
    overflow-x: auto;
  }

  .fare-table {
    width: 100%;
    border-collapse: collapse;
  }

  .fare-table th,
  .fare-table td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #ddd;
    font-weight: bold;
    font-size: 18px;
  }

  .fare-table th {
    color: white;
  }

  /* Different header background colors */
  .fare-table th:nth-child(1) {
    background-color: #4CAF50; /* Green */
  }

  .fare-table th:nth-child(2) {
    background-color: #2196F3; /* Blue */
  }

  .fare-table th:nth-child(3) {
    background-color: #FF9800; /* Orange */
  }
   .fare-table th:nth-child(4) {
    background-color:rgb(10, 161, 10); /* Orange */
  }

  /* Different column background colors */
  .fare-table td:nth-child(1) {
    background-color: #e8f5e9; /* Light green */
  }

  .fare-table td:nth-child(2) {
    background-color:rgb(231, 235, 238); /* Light blue */
  }

  .fare-table td:nth-child(3) {
    background-color: #fff3e0; /* Light orange */
  }

  .fare-table tr:hover td {
    background-color: #f1f1f1 !important;
  }

  .fare-table td {
    color: #333;
  }
</style>

<section class="fare-route">
  <h3>Fare & Route Information</h3>
  <div class="fare-table-container">
    <?php
      $conn = new mysqli("localhost", "root", "", "ecarga");
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }

      $sql = "SELECT vehicle_type, pickup_location, dropoff_location, fare_amount FROM fares";
      $result = $conn->query($sql);

      if ($result->num_rows > 0): ?>
        <table class="fare-table">
          <thead>
            <tr>
              <th>Vehicle Type</th>
              <th>Pickup Location</th>
              <th>Dropoff Location</th>
              <th>Fare (₱)</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['vehicle_type']); ?></td>
                <td><?php echo htmlspecialchars($row['pickup_location']); ?></td>
                <td><?php echo htmlspecialchars($row['dropoff_location']); ?></td>
                <td><?php echo number_format($row['fare_amount'], 2); ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No fare information available.</p>
      <?php endif;

      $conn->close();
    ?>
  </div>
</section>






    </main>

    <footer>
      <div class="nav"><a href="home.php"><span>🏠</span><p>Home</p></a></div>
      <div class="nav"><a href="bookings.php"><span>📅</span><p>Bookings</p></a></div>
      <div class="nav"><a href="history.php"><span>📜</span><p>History</p></a></div>
      <div class="nav"><a href="profile.php"><span>👤</span><p>Profile</p></a></div>
      <a href="user_rating_driver.php" class="nav" style="display: flex; align-items: center; gap: 6px; text-decoration: none; padding: 10px; border: 1px solid #ccc; border-radius: 6px; color: inherit;"><span>👤</span><p style="margin: 0;">Rate</p></a>

    </footer>

    <script src="script.js"></script>
  </body>
  </html>
