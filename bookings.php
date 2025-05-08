<?php
$conn = new mysqli("localhost", "root", "", "ecarga");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Simulated logged-in user ID (replace this with session value in production)
$user_id = 1; 
$message = "";

// Fetch user details
$name = "";
$address = "";

$user_stmt = $conn->prepare("SELECT name, address FROM users WHERE user_id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
if ($user_row = $user_result->fetch_assoc()) {
  $name = $user_row['name'];
  $address = $user_row['address'];
}
$user_stmt->close();

// Handle booking form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = $_POST["name"];
  $address = $_POST["address"];
  $cellphone = $_POST["cellphone"];
  $vehicle_type = $_POST["vehicle_type"];
  $driver_id = $_POST["driver_id"];
  $booking_date = $_POST["booking_date"];
  $booking_time = $_POST["booking_time"];

  $stmt = $conn->prepare("INSERT INTO bookings (user_id, name, address, cellphone, vehicle_type, driver_id, booking_date, booking_time, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
  $stmt->bind_param("isssssss", $user_id, $name, $address, $cellphone, $vehicle_type, $driver_id, $booking_date, $booking_time);

  if ($stmt->execute()) {
    $message = "Booking successful!";
  } else {
    $message = "Booking failed: " . $stmt->error;
  }

  $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Book a Vehicle - ECARGA</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      margin: 0;
      padding: 0;
      padding-bottom: 70px;
    }

    header {
      background-color: #222;
      color: white;
      padding: 10px 20px;
      display: flex;
      align-items: center;
    }

    .header-left h1 span {
      color: #00bcd4;
    }

    .booking-form {
      background: white;
      max-width: 500px;
      margin: 30px auto;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .booking-form h2 {
      text-align: center;
      color: #333;
    }

    form label {
      display: block;
      margin-top: 15px;
      color: #333;
    }

    form input, form select {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 4px;
      border: 1px solid #ccc;
    }

    .book-btn {
      width: 100%;
      margin-top: 20px;
      padding: 12px;
      background-color: #00bcd4;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
    }

    .book-btn:hover {
      background-color: #0097a7;
    }

    footer {
      position: fixed;
      bottom: 0;
      width: 100%;
      background: #222;
      display: flex;
      justify-content: space-around;
      padding: 10px 0;
      color: white;
      z-index: 100;
    }

    .nav a {
      color: white;
      text-decoration: none;
      font-size: 14px;
      text-align: center;
    }

    .nav span {
      display: block;
      font-size: 20px;
    }
  </style>
</head>
<body>
  <header>
    <div class="header-left">
      <img src="https://cdn-icons-png.flaticon.com/512/684/684908.png" class="flag" alt="App Logo" width="40" />
      <h1 style="margin-left: 10px;">E<span>CARGA</span> <span class="beta">TM</span></h1>
    </div>
  </header>

  <main>
    <section class="booking-form">
      <h2>Book a Vehicle</h2>
      <?php if ($message): ?>
        <p style="color: green;"><?php echo htmlspecialchars($message); ?></p>
      <?php endif; ?>
      <form method="POST" action="">
      <label for="name">Full Name:</label>
<input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($name); ?>">

<label for="address">Address:</label>
<input type="text" name="address" id="address" required value="<?php echo htmlspecialchars($address); ?>">


        <label for="cellphone">Cellphone Number:</label>
        <input type="tel" name="cellphone" id="cellphone" pattern="[0-9]{11}" placeholder="e.g., 09123456789" required>

        <label for="vehicle_type">Vehicle Type:</label>
        <select name="vehicle_type" id="vehicle_type" required>
          <option value="">-- Select Vehicle --</option>
          <option value="Motorcycle">Motorcycle</option>
          <option value="Bao-Bao">Bao-Bao</option>
          <option value="Jeep">Jeep</option>
        </select> 

        <label for="driver_id">Driver:</label>
        <select name="driver_id" id="driver_id" required>
          <option value="">-- Select Driver --</option>
        </select>

        <label for="booking_date">Booking Date:</label>
        <input type="date" name="booking_date" id="booking_date" required min="<?php echo date('Y-m-d'); ?>">

        <label for="booking_time">Booking Time:</label>
        <input type="time" name="booking_time" id="booking_time" required>

        <button type="submit" class="book-btn">Book Now</button>
        <button type="button" class="book-btn" onclick="location.href='customer_landing.php';">Go Back</button>
      </form>
    </section>
  </main>

  <footer>
    <div class="nav"><a href="home.php"><span>🏠</span><p>Home</p></a></div>
    <div class="nav"><a href="bookings.php"><span>📅</span><p>Bookings</p></a></div>
    <div class="nav"><a href="history.php"><span>📜</span><p>History</p></a></div>
    <div class="nav"><a href="profile.php"><span>👤</span><p>Profile</p></a></div>
    <div class="nav"><a href="rate.php"><span>🌟</span><p>Rate</p></a></div>
  </footer>

  <script>
    document.getElementById('vehicle_type').addEventListener('change', function () {
      const vehicleType = this.value;
      const driverSelect = document.getElementById('driver_id');

      driverSelect.innerHTML = '<option value="">Loading...</option>';

      fetch('get_drivers.php?vehicle_type=' + encodeURIComponent(vehicleType))
        .then(response => response.json())
        .then(data => {
          driverSelect.innerHTML = '<option value="">-- Select Driver --</option>';
          data.forEach(driver => {
            const option = document.createElement('option');
            option.value = driver.driver_id;
            option.textContent = driver.name;
            driverSelect.appendChild(option);
          });
        })
        .catch(error => {
          console.error('Error fetching drivers:', error);
          driverSelect.innerHTML = '<option value="">No drivers found</option>';
        });
    });
  </script>
</body>
</html>
