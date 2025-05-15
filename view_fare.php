<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecarga";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch fare details
if (isset($_GET['fare_id'])) {
    $fare_id = intval($_GET['fare_id']);
    $stmt = $conn->prepare("SELECT * FROM fares WHERE fare_id = ?");
    $stmt->bind_param("i", $fare_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $fare = $result->fetch_assoc();
    $stmt->close();

    if (!$fare) {
        echo "Fare record not found.";
        exit;
    }
} else {
    echo "Fare ID is missing.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Fare</title>
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
        .details {
            margin-bottom: 20px;
        }
        .details p {
            font-size: 16px;
            line-height: 1.5;
        }
        .back-btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .back-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Fare Details</h2>

    <div class="details">
        <p><strong>Vehicle Type:</strong> <?= htmlspecialchars($fare['vehicle_type']) ?></p>
        <p><strong>Pick-up Location:</strong> <?= htmlspecialchars($fare['pickup_location']) ?></p>
        <p><strong>Drop-off Location:</strong> <?= htmlspecialchars($fare['dropoff_location']) ?></p>
        <p><strong>Fare Amount:</strong> ₱<?= number_format((float)$fare['fare_amount'], 2) ?></p>
    </div>

    <a href="crud.php" class="back-btn">Back to Routes List</a>
</div>

</body>
</html>

<?php $conn->close(); ?>
