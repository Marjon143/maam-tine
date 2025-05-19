<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "ecarga";

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all transactions with driver name
$sql = "SELECT t.*, driver_name AS driver_name 
        FROM transactions t
        LEFT JOIN drivers d ON t.driver_id = d.driver_id
        ORDER BY t.action_time DESC";

$result = $conn->query($sql);

// Separate transactions
$ongoing = [];
$done = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['transaction_status'] === 'Ongoing') {
            $ongoing[] = $row;
        } else {
            $done[] = $row;
        }
    }
}
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
        <!-- Go Back Button -->
        <div class="mb-4">
            <button onclick="history.back()" class="btn btn-secondary">
                ← Go Back
            </button>
        </div>

        <h2 class="text-center mb-4">Transaction Records</h2>
        <div class="row">
            <!-- Ongoing Column -->
            <div class="col-md-6">
                <h4 class="text-primary mb-3">Ongoing Transactions</h4>
                <?php if (count($ongoing) > 0): ?>
                    <?php foreach ($ongoing as $row): ?>
                        <div class="card mb-3 border-primary shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <?= htmlspecialchars($row['name']) ?> -
                                    <span class="badge bg-warning text-dark"><?= htmlspecialchars($row['action']) ?></span>
                                </h5>
                                <p class="card-text mb-1"><strong>Driver:</strong> <?= htmlspecialchars($row['driver_name'] ?? 'N/A') ?></p>
                                <p class="card-text mb-1"><strong>Pickup:</strong> <?= htmlspecialchars($row['pickup_location']) ?></p>
                                <p class="card-text mb-1"><strong>Dropoff:</strong> <?= htmlspecialchars($row['dropoff_location']) ?></p>
                                <p class="card-text mb-1">
                                    <strong>Status:</strong>
                                    <span class="badge bg-primary"><?= htmlspecialchars($row['transaction_status']) ?></span>
                                </p>
                                <p class="card-text"><small class="text-muted">Action Time: <?= htmlspecialchars($row['action_time']) ?></small></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">No ongoing transactions.</div>
                <?php endif; ?>
            </div>

            <!-- Done Column -->
            <div class="col-md-6">
                <h4 class="text-success mb-3">Completed Transactions</h4>
                <?php if (count($done) > 0): ?>
                    <?php foreach ($done as $row): ?>
                        <div class="card mb-3 border-success shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <?= htmlspecialchars($row['name']) ?> -
                                    <span class="badge bg-success"><?= htmlspecialchars($row['action']) ?></span>
                                </h5>
                                <p class="card-text mb-1"><strong>Driver:</strong> <?= htmlspecialchars($row['driver_name'] ?? 'N/A') ?></p>
                                <p class="card-text mb-1"><strong>Pickup:</strong> <?= htmlspecialchars($row['pickup_location']) ?></p>
                                <p class="card-text mb-1"><strong>Dropoff:</strong> <?= htmlspecialchars($row['dropoff_location']) ?></p>
                                <p class="card-text mb-1">
                                    <strong>Status:</strong>
                                    <span class="badge bg-success"><?= htmlspecialchars($row['transaction_status']) ?></span>
                                </p>
                                <p class="card-text"><small class="text-muted">Action Time: <?= htmlspecialchars($row['action_time']) ?></small></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">No completed transactions.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
