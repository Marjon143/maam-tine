<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "ecarga");

if ($conn->connect_error) {
  echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
  exit;
}

// Get parameters from GET request
$vehicle_type = $_GET['vehicle_type'] ?? '';
$pickup = $_GET['pickup'] ?? '';
$dropoff = $_GET['dropoff'] ?? '';

// Validate input
if (empty($vehicle_type) || empty($pickup) || empty($dropoff)) {
  echo json_encode(["error" => "Missing parameters"]);
  exit;
}

// Prepare and execute query
$stmt = $conn->prepare("SELECT fare_amount FROM fares WHERE vehicle_type = ? AND pickup_location = ? AND dropoff_location = ?");
$stmt->bind_param("sss", $vehicle_type, $pickup, $dropoff);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  echo json_encode(["amount" => $row['fare_amount']]);
} else {
  echo json_encode(["error" => "Fare not found"]);
}

$stmt->close();
$conn->close();
?>
