<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "ecarga"; // replace with your DB

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM transactions ORDER BY action_time DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transaction Page</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <h2 class="text-center mb-4">Transaction Records</h2>

        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['name']) ?> - <?= htmlspecialchars($row['action']) ?></h5>
                        <p class="card-text mb-1"><strong>Pickup:</strong> <?= htmlspecialchars($row['pickup_location']) ?></p>
                        <p class="card-text mb-1"><strong>Dropoff:</strong> <?= htmlspecialchars($row['dropoff_location']) ?></p>
                        <p class="card-text mb-1"><strong>Status:</strong> <?= htmlspecialchars($row['transaction_status']) ?></p>
                        <p class="card-text"><small class="text-muted">Action Time: <?= htmlspecialchars($row['action_time']) ?></small></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info text-center">No transactions found.</div>
        <?php endif; ?>

    </div>
</body>
</html>

<?php
$conn->close();
?>
