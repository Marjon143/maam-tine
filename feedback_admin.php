<?php
// db.php - Your database connection
$conn = new mysqli("localhost", "root", "", "ecarga");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get selected filter (if any)
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Fetch customer feedback grouped by customer_name
$customer_feedback_sql = "SELECT customer_name, comments, created_at FROM customer_feedback ORDER BY customer_name, created_at DESC";
$customer_result = ($filter === 'all' || $filter === 'customer') ? $conn->query($customer_feedback_sql) : null;

// Fetch driver feedback grouped by driver_name
$driver_feedback_sql = "SELECT driver_name, name, comments, created_at FROM driver_feedback ORDER BY driver_name, created_at DESC";
$driver_result = ($filter === 'all' || $filter === 'driver') ? $conn->query($driver_feedback_sql) : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .feedback-section { margin-top: 30px; }
        .feedback-card { margin-bottom: 20px; }
        .group-header { background-color: #f8f9fa; padding: 10px; font-weight: bold; border-left: 5px solid #0d6efd; }
    </style>
</head>
<body>
<div class="container">
    <h1 class="my-4 text-center">Customer and Driver Feedback</h1>

    <!-- FILTER FORM -->
    <form method="get" class="mb-4 text-center">
        <label for="filter" class="form-label fw-bold">Filter Feedback:</label>
        <select name="filter" id="filter" class="form-select d-inline-block w-auto mx-2" onchange="this.form.submit()">
            <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All</option>
            <option value="customer" <?= $filter === 'customer' ? 'selected' : '' ?>>  Driver Only</option>
            <option value="driver" <?= $filter === 'driver' ? 'selected' : '' ?>>Customer Only</option>
        </select>
    </form>

    <div class="text-center mb-4">
    <a href="<?= $_SERVER['HTTP_REFERER'] ?? 'dashboard.php' ?>" class="btn btn-secondary">← Go Back</a>
</div>
    <!-- CUSTOMER FEEDBACK -->
    <?php if ($filter === 'all' || $filter === 'customer'): ?>
        <div class="feedback-section">
            <h3 class="mb-3">Driver Feedback</h3>
            <?php
            $last_customer = "";
            if ($customer_result && $customer_result->num_rows > 0) {
                while ($row = $customer_result->fetch_assoc()) {
                    if ($row['customer_name'] !== $last_customer) {
                        if ($last_customer !== "") echo "</div>";
                        echo "<div class='mb-4'>";
                        echo "<div class='group-header'>Customer: " . htmlspecialchars($row['customer_name']) . "</div>";
                        $last_customer = $row['customer_name'];
                    }
                    echo "<div class='card feedback-card'>";
                    echo "<div class='card-body'>";
                    echo "<p class='card-text'>" . nl2br(htmlspecialchars($row['comments'])) . "</p>";
                    echo "<small class='text-muted'>Submitted on " . $row['created_at'] . "</small>";
                    echo "</div></div>";
                }
                echo "</div>";
            } else {
                echo "<p>No customer feedback found.</p>";
            }
            ?>
        </div>
    <?php endif; ?>

    <!-- DRIVER FEEDBACK -->
    <?php if ($filter === 'all' || $filter === 'driver'): ?>
        <div class="feedback-section">
            <h3 class="mb-3">Customer Feedback</h3>
            <?php
            $last_driver = "";
            if ($driver_result && $driver_result->num_rows > 0) {
                while ($row = $driver_result->fetch_assoc()) {
                    if ($row['driver_name'] !== $last_driver) {
                        if ($last_driver !== "") echo "</div>";
                        echo "<div class='mb-4'>";
                        echo "<div class='group-header'>Driver: " . htmlspecialchars($row['driver_name']) . "</div>";
                        $last_driver = $row['driver_name'];
                    }
                    echo "<div class='card feedback-card'>";
                    echo "<div class='card-body'>";
                    echo "<p><strong>From:</strong> " . htmlspecialchars($row['name']) . "</p>";
                    echo "<p class='card-text'>" . nl2br(htmlspecialchars($row['comments'])) . "</p>";
                    echo "<small class='text-muted'>Submitted on " . $row['created_at'] . "</small>";
                    echo "</div></div>";
                }
                echo "</div>";
            } else {
                echo "<p>No driver feedback found.</p>";
            }
            ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
