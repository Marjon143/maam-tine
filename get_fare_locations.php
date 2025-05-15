<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "ecarga");
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

if (!isset($_GET['vehicle_type'])) {
    echo json_encode(['error' => 'Missing vehicle_type parameter']);
    exit;
}

$vehicle_type = $_GET['vehicle_type'];

// Fetch all distinct pickup and dropoff locations for the vehicle type
$query = "SELECT DISTINCT pickup_location, dropoff_location FROM fares WHERE vehicle_type = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $vehicle_type);
$stmt->execute();
$result = $stmt->get_result();

$pickup_locations = [];
$dropoff_locations = [];

while ($row = $result->fetch_assoc()) {
    if ($row['pickup_location'] && !in_array($row['pickup_location'], $pickup_locations)) {
        $pickup_locations[] = $row['pickup_location'];
    }
    if ($row['dropoff_location'] && !in_array($row['dropoff_location'], $dropoff_locations)) {
        $dropoff_locations[] = $row['dropoff_location'];
    }
}

echo json_encode([
    'pickup_locations' => $pickup_locations,
    'dropoff_locations' => $dropoff_locations
]);

$stmt->close();
$conn->close();
?>
