<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "ecarga");
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

if (!isset($_GET['vehicle_type'])) {
    echo json_encode(['error' => 'Vehicle type not specified']);
    exit;
}

$vehicle_type = $_GET['vehicle_type'];

$stmt = $conn->prepare("SELECT driver_id, driver_name FROM drivers WHERE vehicle_type = ? AND status = 'available'");
$stmt->bind_param("s", $vehicle_type);
$stmt->execute();
$result = $stmt->get_result();

$drivers = [];
while ($row = $result->fetch_assoc()) {
    $drivers[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($drivers);
