<?php
// Database connection (adjust as needed)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecarga";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch distinct vehicle types for the filter dropdown
$vehicleTypesResult = $conn->query("SELECT DISTINCT vehicle_type FROM drivers WHERE vehicle_type IS NOT NULL AND vehicle_type != '' ORDER BY vehicle_type ASC");
$vehicleTypes = [];
if ($vehicleTypesResult->num_rows > 0) {
    while ($vt = $vehicleTypesResult->fetch_assoc()) {
        $vehicleTypes[] = $vt['vehicle_type'];
    }
}

// Get filter values from GET, sanitize input
$minExperience = isset($_GET['min_experience']) ? intval($_GET['min_experience']) : 0;
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$vehicleFilter = isset($_GET['vehicle_type']) ? $_GET['vehicle_type'] : '';

// Build the WHERE clauses based on filters
$whereClauses = [];

if ($minExperience > 0) {
    $whereClauses[] = "years_experience >= $minExperience";
}

if ($statusFilter === 'available' || $statusFilter === 'busy') {
    $whereClauses[] = "status = '" . $conn->real_escape_string($statusFilter) . "'";
}

if ($vehicleFilter && in_array($vehicleFilter, $vehicleTypes)) {
    $whereClauses[] = "vehicle_type = '" . $conn->real_escape_string($vehicleFilter) . "'";
}

$whereSql = "";
if (count($whereClauses) > 0) {
    $whereSql = "WHERE " . implode(" AND ", $whereClauses);
}

// Fetch drivers with filters applied
$sql = "SELECT driver_id, driver_name, image_url, address, vehicle_type, years_experience, status FROM drivers $whereSql";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <title>Drivers List</title>

    <style>
        .card-img-top {
            height: 250px;
            object-fit: cover;
        }
        .status-available {
            color: green;
            font-weight: bold;
        }
        .status-busy {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container my-4" style="background-color: #f8f9fa; border: 2px solid #007bff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
        <h3 class="text-center mb-4">Our Drivers</h3>

        <!-- Filter Form -->
        <form method="GET" class="row g-3 align-items-center justify-content-center">
            <div class="col-auto">
                <label for="min_experience" class="form-label">Min Experience (years)</label>
                <input type="number" min="0" class="form-control" id="min_experience" name="min_experience" value="<?php echo htmlspecialchars($minExperience); ?>" />
            </div>

            <div class="col-auto">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="" <?php if ($statusFilter === '') echo 'selected'; ?>>All</option>
                    <option value="available" <?php if ($statusFilter === 'available') echo 'selected'; ?>>Available</option>
                    <option value="busy" <?php if ($statusFilter === 'busy') echo 'selected'; ?>>Busy</option>
                </select>
            </div>

            <div class="col-auto">
                <label for="vehicle_type" class="form-label">Vehicle Type</label>
                <select class="form-select" id="vehicle_type" name="vehicle_type">
                    <option value="" <?php if ($vehicleFilter === '') echo 'selected'; ?>>All</option>
                    <?php foreach ($vehicleTypes as $vt): ?>
                        <option value="<?php echo htmlspecialchars($vt); ?>" <?php if ($vehicleFilter === $vt) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($vt); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-auto align-self-end">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="<?php echo basename($_SERVER['PHP_SELF']); ?>" class="btn btn-secondary ms-2">Reset</a>
            </div>
        </form>
    </div>

    <div class="container my-4">
        <div class="row g-4">
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Prepare status class for coloring
                    $statusClass = $row['status'] === 'available' ? 'status-available' : 'status-busy';

                    echo '
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="card h-100 shadow-sm">
                            <img src="' . htmlspecialchars($row['image_url']) . '" class="card-img-top" alt="' . htmlspecialchars($row['driver_name']) . '">
                            <div class="card-body text-start">
                                <h5 class="card-title">' . htmlspecialchars($row['driver_name']) . '</h5>
                                <p class="card-text mb-1"><strong>Address:</strong> ' . htmlspecialchars($row['address']) . '</p>
                                <p class="card-text mb-1"><strong>Vehicle Type:</strong> ' . htmlspecialchars($row['vehicle_type']) . '</p>
                                <p class="card-text mb-1"><strong>Experience:</strong> ' . intval($row['years_experience']) . ' years</p>
                                <p class="card-text"><strong>Status:</strong> <span class="' . $statusClass . '">' . ucfirst(htmlspecialchars($row['status'])) . '</span></p>
                            </div>
                        </div>
                    </div>
                    ';
                }
            } else {
                echo '<p class="text-center">No drivers found.</p>';
            }
            ?>
        </div>
    </div>

    <div class="container text-center mt-4 mb-4">
    <a href="dashboard.php" class="btn btn-primary">Go Back</a>
</div>

    <?php $conn->close(); ?>
</body>
</html>
