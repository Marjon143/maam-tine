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

// Fetch fare details for editing
if (isset($_GET['fare_id'])) {
    $fare_id = intval($_GET['fare_id']);
    $stmt = $conn->prepare("SELECT * FROM fares WHERE fare_id = ?");
    $stmt->bind_param("i", $fare_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $fare = $result->fetch_assoc();
    $stmt->close();
    
    if (!$fare) {
        echo "Fare not found.";
        exit;
    }
} else {
    echo "Fare ID is missing.";
    exit;
}

// Handle form submission for editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fare_action']) && $_POST['fare_action'] === 'edit') {
    $fare_id = intval($_POST['fare_id']);
    $vehicle_type = $_POST['fareVehicleType'];
    $pickup_location = $_POST['farepickuplocation'];
    $dropoff_location = $_POST['faredropofflocation'];
    $fare_amount = floatval($_POST['fareAmount']);

    $stmt = $conn->prepare("UPDATE fares SET vehicle_type = ?, pickup_location = ?, dropoff_location = ?, fare_amount = ? WHERE fare_id = ?");
    $stmt->bind_param("sssdi", $vehicle_type, $pickup_location, $dropoff_location, $fare_amount, $fare_id);

    if ($stmt->execute()) {
        header("Location: crud.php");
        exit;
    } else {
        echo "Error updating fare: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Fare</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container {
            margin-bottom: 20px;
        }
        .form-container input,
        .form-container select,
        .form-container button {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            width: 100%;
            margin-bottom: 15px;
        }
        .form-container button {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .form-container button:hover {
            background-color: #218838;
        }
        .back-btn {
            display: inline-block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .back-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Fare</h2>

    <form action="edit_fare.php?fare_id=<?= htmlspecialchars($fare['fare_id']) ?>" method="POST" class="form-container">
        <select name="fareVehicleType" required>
            <option value="" disabled>Select Vehicle Type</option>
            <option value="Jeep" <?= $fare['vehicle_type'] === 'Jeep' ? 'selected' : '' ?>>Jeep</option>
            <option value="Bao-Bao" <?= $fare['vehicle_type'] === 'Bao-Bao' ? 'selected' : '' ?>>Bao-Bao</option>
            <option value="Motorcycle" <?= $fare['vehicle_type'] === 'Motorcycle' ? 'selected' : '' ?>>Motorcycle</option>
        </select>

        <input type="text" name="farepickuplocation" placeholder="Pick-up Location" value="<?= htmlspecialchars($fare['pickup_location']) ?>" required>
        <input type="text" name="faredropofflocation" placeholder="Drop-off Location" value="<?= htmlspecialchars($fare['dropoff_location']) ?>" required>

        <input type="number" step="0.01" name="fareAmount" placeholder="Fare Amount" value="<?= htmlspecialchars($fare['fare_amount']) ?>" required>

        <input type="hidden" name="fare_id" value="<?= htmlspecialchars($fare['fare_id']) ?>">
        <input type="hidden" name="fare_action" value="edit">

        <button type="submit">Update Fare</button>
    </form>

    <a href="crud.php" class="back-btn">Back to Routes List</a>
</div>

</body>
</html>

<?php $conn->close(); ?>
