<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecarga";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle add action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = $_POST['driverName'];
    $address = $_POST['address'];
    $vehicle_type = $_POST['vehicleType'];
    $plate_number = $_POST['plateNumber'];
    $years_experience = $_POST['yearsExperience'];
    $image_url = $_POST['driverImageURL'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO drivers (name, address, vehicle_type, plate_number, years_experience, image_url, email, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $name, $address, $vehicle_type, $plate_number, $years_experience, $image_url, $email, $hashed_password);

    if ($stmt->execute()) {
        header("Location: crud.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Handle delete confirmation
if (isset($_GET['action']) && $_GET['action'] === 'confirm_delete' && isset($_GET['driver_id'])) {
    $driver_id = intval($_GET['driver_id']);
    $sql = "DELETE FROM drivers WHERE driver_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $driver_id);
        if ($stmt->execute()) {
            echo "<p>Driver deleted successfully at " . date('Y-m-d H:i:s') . ".</p>";
        } else {
            echo "Error deleting driver: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch all drivers
$sql = "SELECT * FROM drivers";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver CRUD Operations</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .form-container form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .form-container input,
        .form-container select,
        .form-container button {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-container button {
            grid-column: span 2;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-container button:hover {
            background-color: #218838;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: left;
        }

        thead {
            background-color: #343a40;
            color: white;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .image-preview {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-buttons a,
        .action-buttons button {
            padding: 6px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            color: white;
        }

        .view-btn {
            background-color: #17a2b8;
        }

        .update-btn {
            background-color: #ffc107;
            color: black;
        }

        .delete-btn {
            background-color: #dc3545;
        }

        #deleteModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .modal-content button {
            margin: 10px;
            padding: 10px 20px;
        }
    </style>
    <script>
        function confirmDelete(driverId) {
            document.getElementById('deleteModal').style.display = 'flex';
            document.getElementById('deleteButton').onclick = function () {
                window.location.href = "?action=confirm_delete&driver_id=" + driverId;
            };
        }
    </script>
</head>
<body>

<div class="container">
    <h2>Driver CRUD Operations</h2>

    <div class="form-container">
        <form action="crud.php" method="POST">
            <input type="text" name="driverName" placeholder="Driver Name" required>
            <input type="text" name="address" placeholder="Address" required>
            <select name="vehicleType" required>
                <option value="" disabled selected>Select Vehicle Type</option>
                <option value="Jeep">Jeep</option>
                <option value="Bao-Bao">Bao-Bao</option>
                <option value="Motorcycle">Motorcycle</option>
            </select>
            <input type="text" name="plateNumber" placeholder="Plate Number" required>
            <input type="number" name="yearsExperience" placeholder="Years of Experience" required>
            <input type="url" name="driverImageURL" placeholder="Image URL" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="hidden" name="action" value="add">
            <button type="submit">Add Driver</button>
        </form>
    </div>

    <h1>Drivers List</h1>
    <table>
        <thead>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Address</th>
            <th>Vehicle Type</th>
            <th>Plate Number</th>
            <th>Years of Experience</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><img src="<?= htmlspecialchars($row['image_url']) ?>" class="image-preview" alt="Driver Image"></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['address']) ?></td>
                    <td><?= htmlspecialchars($row['vehicle_type']) ?></td>
                    <td><?= htmlspecialchars($row['plate_number']) ?></td>
                    <td><?= htmlspecialchars($row['years_experience']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td class="action-buttons">
                        <a href="view.php?id=<?= $row['driver_id'] ?>" class="view-btn">View</a>
                        <a href="edit.php?id=<?= $row['driver_id'] ?>" class="update-btn">Edit</a>
                        <button onclick="confirmDelete(<?= $row['driver_id'] ?>)" class="delete-btn">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8">No drivers found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="deleteModal">
    <div class="modal-content">
        <p>Are you sure you want to delete this driver?</p>
        <button id="deleteButton">Yes, Delete</button>
        <button onclick="document.getElementById('deleteModal').style.display='none'">Cancel</button>
    </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
