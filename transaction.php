<?php
session_start();
if (!isset($_SESSION['driver_id'])) {
  die("Access denied");
}
$driver_id = $_SESSION['driver_id'];

$conn = new mysqli("localhost", "root", "", "ecarga");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM transactions WHERE driver_id = ? ORDER BY action_time DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Driver Transactions</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
    th { background-color: #f4f4f4; }
    .done-btn { background: #4CAF50; color: white; border: none; padding: 5px 10px; cursor: pointer; }
    .done-btn:hover { background: #45a049; }
    .modal {
      display: none; position: fixed; z-index: 999;
      left: 0; top: 0; width: 100%; height: 100%;
      background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center;
    }
    .modal-content {
      background: white; padding: 20px; border-radius: 5px; width: 300px;
      text-align: center;
    }
    .modal-buttons { margin-top: 20px; display: flex; justify-content: space-around; }
    .modal-buttons button { padding: 5px 15px; }
  </style>
</head>
<body>

<h2>Transaction History</h2>

<?php if (isset($_GET['updated'])): ?>
  <div style="background: #dff0d8; padding: 10px; color: #3c763d;">✅ Transaction marked as done.</div>
<?php endif; ?>

<table>
  <tr>
    <th>Customer</th>
    <th>Pickup</th>
    <th>Dropoff</th>
    <th>Action</th>
    <th>Status</th>
    <th>Time</th>
    <th>Update</th>
  </tr>
  <?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['name']); ?></td>
        <td><?= htmlspecialchars($row['pickup_location']); ?></td>
        <td><?= htmlspecialchars($row['dropoff_location']); ?></td>
        <td><?= $row['action']; ?></td>
        <td><?= $row['transaction_status']; ?></td>
        <td><?= $row['action_time']; ?></td>
        <td>
          <?php if ($row['transaction_status'] === 'Done'): ?>
            ✅ Done
          <?php elseif ($row['action'] === 'Accepted' && $row['transaction_status'] === 'Ongoing'): ?>
            <button class="done-btn" onclick="openModal(<?= $row['transaction_id']; ?>)">Mark as Done</button>
          <?php else: ?>
            —
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  <?php else: ?>
    <tr><td colspan="7">No transactions found.</td></tr>
  <?php endif; ?>
</table>

<!-- Confirmation Modal -->
<div class="modal" id="confirmationModal">
  <div class="modal-content">
    <p>Are you sure you want to mark this transaction as done?</p>
    <form method="POST" action="mark_done.php" id="confirmForm">
      <input type="hidden" name="transaction_id" id="modalTransactionId">
      <div class="modal-buttons">
        <button type="button" onclick="closeModal()">Cancel</button>
        <button type="submit" class="done-btn">Yes, Mark Done</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openModal(transactionId) {
    document.getElementById('modalTransactionId').value = transactionId;
    document.getElementById('confirmationModal').style.display = 'flex';
  }

  function closeModal() {
    document.getElementById('confirmationModal').style.display = 'none';
  }
</script>

</body>
</html>
