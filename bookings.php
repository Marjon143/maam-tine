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
  $pickup_location = $_POST["pickup_location"];
  $dropoff_location = $_POST["dropoff_location"];

  $stmt = $conn->prepare("INSERT INTO bookings (user_id, name, address, cellphone, vehicle_type, driver_id, booking_date, booking_time, pickup_location, dropoff_location, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
  $stmt->bind_param("isssssssss", $user_id, $name, $address, $cellphone, $vehicle_type, $driver_id, $booking_date, $booking_time, $pickup_location, $dropoff_location);

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
  <link rel="stylesheet" href="assets/booking1.css">
  <title>Book a Vehicle - ECARGA</title>
</head>
<body>
  <header>
    <div class="header-left">
      <img src="https://cdn-icons-png.flaticon.com/512/684/684908.png" class="flag" alt="App Logo" width="40" />
      <h1 style="margin-left: 50px; margin-top: -40px">e<span>CARGA</span> <span class="beta">TM</span></h1>
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

        <label for="pickup_location">Pickup Location:</label>
<select name="pickup_location" id="pickup_location" required>
  <option value="">-- Select Pickup Location --</option>
</select>

<label for="dropoff_location">Dropoff Location:</label>
<select name="dropoff_location" id="dropoff_location" required>
  <option value="">-- Select Dropoff Location --</option>
</select>

<label for="amount">Amount:</label>
<input type="text" name="amount" id="amount" readonly>



        <button type="submit" class="book-btn">Book Now</button>
        <button type="button" class="book-btn" onclick="location.href='payment.php';">Go Back</button>
      </form>
    </section>
  </main>

  <script>
    document.getElementById('vehicle_type').addEventListener('change', function () {
      const vehicleType = this.value;
      const driverSelect = document.getElementById('driver_id');
      const pickupInput = document.getElementById('pickup_location');
      const dropoffInput = document.getElementById('dropoff_location');

      // Clear pickup and dropoff while loading
      pickupInput.value = 'Loading...';
      dropoffInput.value = 'Loading...';

      // Fetch drivers for the selected vehicle type
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
        .catch(() => {
          driverSelect.innerHTML = '<option value="">No drivers found</option>';
        });

      // Fetch pickup and dropoff locations for the selected vehicle type
      // Fetch pickup and dropoff locations for the selected vehicle type
fetch('get_fare_locations.php?vehicle_type=' + encodeURIComponent(vehicleType))
  .then(response => response.json())
  .then(data => {
    const pickupSelect = document.getElementById('pickup_location');
    const dropoffSelect = document.getElementById('dropoff_location');

    // Clear previous options except the placeholder
    pickupSelect.innerHTML = '<option value="">-- Select Pickup Location --</option>';
    dropoffSelect.innerHTML = '<option value="">-- Select Dropoff Location --</option>';

    if (!data.error) {
      // Populate pickup locations
      data.pickup_locations.forEach(loc => {
        const option = document.createElement('option');
        option.value = loc;
        option.textContent = loc;
        pickupSelect.appendChild(option);
      });

      // Populate dropoff locations
      data.dropoff_locations.forEach(loc => {
        const option = document.createElement('option');
        option.value = loc;
        option.textContent = loc;
        dropoffSelect.appendChild(option);
      });
    } else {
      console.warn(data.error);
    }
  })
  .catch(() => {
    const pickupSelect = document.getElementById('pickup_location');
    const dropoffSelect = document.getElementById('dropoff_location');
    pickupSelect.innerHTML = '<option value="">-- Select Pickup Location --</option>';
    dropoffSelect.innerHTML = '<option value="">-- Select Dropoff Location --</option>';
  });

  // Function to fetch and update amount
function updateAmount() {
  const vehicleType = document.getElementById('vehicle_type').value;
  const pickup = document.getElementById('pickup_location').value;
  const dropoff = document.getElementById('dropoff_location').value;
  const amountInput = document.getElementById('amount');

  if (vehicleType && pickup && dropoff) {
    // Example fetch call to backend to get fare amount
    fetch(`get_fare_amount.php?vehicle_type=${encodeURIComponent(vehicleType)}&pickup=${encodeURIComponent(pickup)}&dropoff=${encodeURIComponent(dropoff)}`)
      .then(response => response.json())
      .then(data => {
        if (data.amount) {
          amountInput.value = data.amount;
        } else {
          amountInput.value = "N/A";
        }
      })
      .catch(() => {
        amountInput.value = "Error";
      });
  } else {
    amountInput.value = "";
  }
}

// Call updateAmount when pickup or dropoff changes
document.getElementById('pickup_location').addEventListener('change', updateAmount);
document.getElementById('dropoff_location').addEventListener('change', updateAmount);
document.getElementById('vehicle_type').addEventListener('change', () => {
  // Clear amount on vehicle type change
  document.getElementById('amount').value = "";
  updateAmount();
});


    });
  </script>
</body>
</html>
