<?php
// Connect to your MySQL database
$host = "localhost";
$username = "root";
$password = "";
$database = "ecarga";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch fares grouped by vehicle_type
$sql = "SELECT * FROM fares ORDER BY vehicle_type, pickup_location, dropoff_location";
$result = $conn->query($sql);

// Organize results into groups
$groupedFares = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $vehicleType = $row['vehicle_type'];
        $groupedFares[$vehicleType][] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Route List - ECarga</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8;
        }
        .route-container {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 30px;
            margin-top: 40px;
            margin-bottom: 40px;
        }
        .vehicle-type-header {
            background-color: #2c3e50;
            color: #fff;
            padding: 12px;
            border-radius: 10px;
            margin-top: 30px;
            font-size: 25px;
            font-weight: bold;
            text-align: center;
        }
        .fare-table {
            width: 100%;
            margin-top: 15px;
        }
        .fare-table th {
            background-color: #3498db;
            color: white;
            text-align: center;
        }
        .fare-table td {
            text-align: center;
            vertical-align: middle;
        }
        .no-data {
            font-style: italic;
            color: #888;
            margin-top: 20px;
        }
        .back-btn-container {
            text-align: center;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="container route-container">
        <h2 class="text-center text-primary mb-4">Available Routes & Fares</h2>

        <?php if (!empty($groupedFares)): ?>
            <?php foreach ($groupedFares as $vehicleType => $fares): ?>
                <div class="vehicle-type-header text-center"><?php echo htmlspecialchars($vehicleType); ?></div>
                <div class="table-responsive">
                    <table class="table table-bordered fare-table">
                        <thead>
                            <tr>
                                <th>Pickup Location</th>
                                <th>Dropoff Location</th>
                                <th>Fare (₱)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($fares as $fare): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($fare['pickup_location']); ?></td>
                                    <td><?php echo htmlspecialchars($fare['dropoff_location']); ?></td>
                                    <td>₱<?php echo number_format($fare['fare_amount'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-data text-center">No routes available.</p>
        <?php endif; ?>

        <?php $conn->close(); ?>

       <div class="text-center mb-4">
    <a href="<?= $_SERVER['HTTP_REFERER'] ?? 'customer_landing.php' ?>" class="btn btn-secondary">← Go Back</a>
</div>
    </div>
</body>
</html>
