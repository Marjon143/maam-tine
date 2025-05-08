<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "ecarga");
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed."]));
}

$vehicle_type = $_GET['vehicle_type'] ?? '';

if ($vehicle_type === '') {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT driver_id, name FROM drivers WHERE vehicle_type = ?");
$stmt->bind_param("s", $vehicle_type);
$stmt->execute();
$result = $stmt->get_result();

$drivers = [];
while ($row = $result->fetch_assoc()) {
    $drivers[] = $row;
}

echo json_encode($drivers);

$stmt->close();
$conn->close();
?>
