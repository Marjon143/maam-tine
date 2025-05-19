<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecarga";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$driver = null;
$driver_id = null;

// Fetch driver details for editing
if (isset($_GET['id'])) {
    $driver_id = $_GET['id'];
    $sql = "SELECT * FROM drivers WHERE driver_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $driver_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $driver = $result->fetch_assoc();
        $stmt->close();
    } else {
        echo "Error: " . $conn->error . "<br>";
    }
}

// Handle update action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $driver_id = $_POST['id'];
    $name = $_POST['driverName'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $vehicleType = $_POST['vehicleType'];
    $plateNumber = $_POST['plateNumber'];
    $yearsExperience = $_POST['yearsExperience'];
    $imageURL = $_POST['driverImageURL'];

    $sql = "UPDATE drivers SET name = ?, address = ?, email = ?, vehicle_type = ?, plate_number = ?, years_experience = ?, image_url = ? WHERE driver_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssssssi", $name, $address, $email, $vehicleType, $plateNumber, $yearsExperience, $imageURL, $driver_id);
        if ($stmt->execute()) {
            echo "<p style='color: green; text-align: center;'>Driver details updated successfully.</p>";
        } else {
            echo "<p style='color: red; text-align: center;'>Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color: red;'>Error preparing statement: " . $conn->error . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Driver Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7fc; margin: 0; padding: 0; }
        .container { max-width: 500px; margin: 50px auto; background-color: #fff; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 20px; border-radius: 8px; }
        h2 { text-align: center; margin-bottom: 20px; color: #333; }
        input, select { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; }
        button { width: 100%; padding: 10px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; }
        button:hover { background-color: #45a049; }
        .form-group { margin-bottom: 15px; }
        .form-group label { font-weight: bold; display: block; margin-bottom: 5px; color: #555; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Driver Details</h2>
    <?php if ($driver): ?>
    <form action="edit.php?id=<?php echo htmlspecialchars($driver['driver_id']); ?>" method="POST">
        <div class="form-group">
            <label for="driverName">Driver Name</label>
            <input type="text" name="driverName" value="<?php echo htmlspecialchars($driver['driver_name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($driver['address']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($driver['email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="vehicleType">Vehicle Type</label>   
            <select name="vehicleType" required>
                <option value="Bao-Bao" <?php echo $driver['vehicle_type'] == 'Bao-Bao' ? 'selected' : ''; ?>>Bao-Bao</option>
                <option value="Jeep" <?php echo $driver['vehicle_type'] == 'Jeep' ? 'selected' : ''; ?>>Jeep</option>
                <option value="Motorcycle" <?php echo $driver['vehicle_type'] == 'Motorcycle' ? 'selected' : ''; ?>>Motorcycle</option>
            </select>
        </div>

        <div class="form-group">
            <label for="plateNumber">Plate Number</label>
            <input type="text" name="plateNumber" value="<?php echo htmlspecialchars($driver['plate_number']); ?>" required>
        </div>

        <div class="form-group">
            <label for="yearsExperience">Years of Experience</label>
            <input type="number" name="yearsExperience" value="<?php echo htmlspecialchars($driver['years_experience']); ?>" required>
        </div>

        <div class="form-group">
            <label for="driverImageURL">Driver Image URL</label>
            <input type="url" name="driverImageURL" value="<?php echo htmlspecialchars($driver['image_url']); ?>" required>
        </div>

        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($driver['driver_id']); ?>">
        <button type="submit">Update Driver</button>
    </form>
    <?php else: ?>
        <p style="color: red; text-align: center;">Driver not found.</p>
    <?php endif; ?>
</div>

</body>
</html>
<?php
$conn->close();
?>
