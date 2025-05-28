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
  <link rel="stylesheet" href="assets/transaction.css">
  
</head>
<body>



<h2>Transaction History</h2>

<?php if (isset($_GET['updated'])): ?>
  <div class="alert-success">✅ Transaction marked as done.</div>
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
        <td><?= htmlspecialchars($row['action']); ?></td>
        <td>
          <?php if ($row['transaction_status'] === 'Done'): ?>
            <span class="status-done">✅ Done</span>
          <?php elseif ($row['transaction_status'] === 'Ongoing'): ?>
            <span class="status-ongoing">Ongoing</span>
          <?php else: ?>
            <?= htmlspecialchars($row['transaction_status']); ?>
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($row['action_time']); ?></td>
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
    <tr><td colspan="7" style="text-align: center;">No transactions found.</td></tr>
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
<a href="driver_side_landing.php" class="btn">← Go Back</a>

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
