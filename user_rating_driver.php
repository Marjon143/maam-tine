<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecarga";

// Simulated logged-in user ID (replace with session-based user ID in a real system)
$user_id = 1;

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['driver_id'], $_POST['rating'])) {
    $driver_id = intval($_POST['driver_id']);
    $rating = intval($_POST['rating']);

    // Check if user has already rated the driver
    $check = $conn->prepare("SELECT * FROM ratings WHERE driver_id = ? AND user_id = ?");
    $check->bind_param("ii", $driver_id, $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0 && $rating >= 1 && $rating <= 5) {
        // Insert new rating
        $stmt = $conn->prepare("INSERT INTO ratings (driver_id, user_id, rating) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $driver_id, $user_id, $rating);
        $stmt->execute();
        $stmt->close();
        $message = "Thank you for rating!";
    } else {
        $message = "You have already rated this driver or the rating is invalid.";
    }
    $check->close();
}

// Fetch all available drivers
$drivers = $conn->query("SELECT driver_id, name FROM drivers WHERE status = 'available'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rate a Driver</title>
    <style>
        .rating-form {
            max-width: 400px;
            margin: 40px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
        h2{
            text-align: center;
        }

        .stars {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
        }

        .stars input {
            display: none;
        }

        .stars label {
            font-size: 30px;
            color: #ccc;
            cursor: pointer;
        }

        .stars input:checked ~ label,
        .stars label:hover,
        .stars label:hover ~ label {
            color: #ffcc00;
        }

        select, button {
            padding: 8px;
            margin-top: 10px;
            width: 100%;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            text-align: center;
        }

        .modal button {
            padding: 10px 20px;
            margin: 5px;
            cursor: pointer;
        }

        /* Go Back Button */
        .go-back-btn {
            padding: 10px 20px;
            background-color: #f1f1f1;
            border: none;
            cursor: pointer;
            margin-top: 20px;
            display: block;
            text-align: center;
            width: 100%;
        }

    </style>
</head>
<body>

<div class="rating-form">
    <h2>Rate a Driver</h2>
    <?php if (isset($message)) echo "<p>$message</p>"; ?>

    <form method="POST" id="ratingForm">
        <label for="driver_id">Select Driver:</label><br>
        <select name="driver_id" id="driver_id" required>
            <option value="">-- Select Driver --</option>
            <?php
            if ($drivers->num_rows > 0) {
                while ($row = $drivers->fetch_assoc()) {
                    echo "<option value='{$row['driver_id']}'>{$row['name']}</option>";
                }
            } else {
                echo "<option disabled>No available drivers</option>";
            }
            ?>
        </select><br><br>

        <label>Rate:</label>
        <div class="stars">
            <input type="radio" name="rating" id="star5" value="5"><label for="star5">&#9733;</label>
            <input type="radio" name="rating" id="star4" value="4"><label for="star4">&#9733;</label>
            <input type="radio" name="rating" id="star3" value="3"><label for="star3">&#9733;</label>
            <input type="radio" name="rating" id="star2" value="2"><label for="star2">&#9733;</label>
            <input type="radio" name="rating" id="star1" value="1"><label for="star1">&#9733;</label>
        </div><br>

        <button type="button" onclick="showModal()">Submit Rating</button>
    </form>
</div>

<!-- Modal Confirmation -->
<div id="confirmationModal" class="modal">
    <div class="modal-content">
        <h3>Confirm Your Rating</h3>
        <p>Are you sure you want to rate this driver?</p>
        <button onclick="submitForm()">Yes</button>
        <button onclick="closeModal()">No</button>
    </div>
</div>

<!-- Go Back Button -->
<button class="go-back-btn" onclick="window.history.back()">Go Back</button>

<script>
    function showModal() {
        // Check if driver is selected and a rating is provided
        var driverId = document.getElementById("driver_id").value;
        var rating = document.querySelector('input[name="rating"]:checked');

        if (driverId && rating) {
            document.getElementById("confirmationModal").style.display = "block";
        } else {
            alert("Please select a driver and a rating.");
        }
    }

    function closeModal() {
        document.getElementById("confirmationModal").style.display = "none";
    }

    function submitForm() {
        // Submit the form
        document.getElementById("ratingForm").submit();
    }
</script>

</body>
</html>

<?php $conn->close(); ?>
