<?php
// Database connection
$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "ecarga";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$driver = null;

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
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7fc; margin: 0; padding: 0; }
        .container { max-width: 500px; margin: 50px auto; background-color: #fff; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 20px; border-radius: 8px; }
        h2 { text-align: center; margin-bottom: 20px; color: #333; }
        .driver-info { margin-bottom: 15px; }
        .driver-info p { color: #333; margin: 10px 0; }
        .driver-info strong { color: #555; }
        .image-preview { max-width: 100%; height: auto; border-radius: 4px; margin-top: 10px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Driver Details</h2>
    <?php if ($driver): ?>
    <div class="driver-info">
        <p><strong>Name:</strong> <?php echo htmlspecialchars($driver['name']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($driver['address']); ?></p>
        <p><strong>Vehicle Type:</strong> <?php echo htmlspecialchars($driver['vehicle_type']); ?></p>
        <p><strong>Plate Number:</strong> <?php echo htmlspecialchars($driver['plate_number']); ?></p>
        <p><strong>Years of Experience:</strong> <?php echo htmlspecialchars($driver['years_experience']); ?></p>
        <p><strong>Image:</strong><br><img src="<?php echo htmlspecialchars($driver['image_url']); ?>" class="image-preview" alt="Driver Image"></p>
    </div>
    <?php else: ?>
        <p style="color: red; text-align: center;">Driver not found.</p>
    <?php endif; ?>
</div>

</body>
</html>
<?php
$conn->close();
?>
