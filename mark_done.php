<?php
session_start();
if (!isset($_SESSION['driver_id'])) {
  die("Access denied");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transaction_id'])) {
  $transaction_id = intval($_POST['transaction_id']);
  $driver_id = $_SESSION['driver_id'];

  $conn = new mysqli("localhost", "root", "", "ecarga");
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // Update the transaction status to 'Done'
  $sql = "UPDATE transactions SET transaction_status = 'Done' WHERE transaction_id = ? AND driver_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii", $transaction_id, $driver_id);

  if ($stmt->execute()) {
    header("Location: transaction.php?updated=1");
    exit();
  } else {
    echo "Failed to update transaction.";
  }

  $stmt->close();
  $conn->close();
} else {
  echo "Invalid request.";
}
?>
