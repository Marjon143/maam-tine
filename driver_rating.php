<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecarga";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all drivers with their average ratings and rating counts
$sql = "
    SELECT d.driver_id, driver_name, d.address, d.image_url, 
           IFNULL(AVG(r.rating), 0) AS avg_rating,
           COUNT(r.rating) AS rating_count
    FROM drivers d
    LEFT JOIN ratings r ON d.driver_id = r.driver_id
    GROUP BY d.driver_id
";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Driver Rating Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: auto;
        }
        .driver-card {
            border: 1px solid #ddd; 
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .driver-info {
            text-align: center;
            margin-bottom: 10px;
        }
        .driver-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .driver-name {
            font-size: 20px;
            font-weight: bold;
        }
        .driver-address {
            font-size: 16px;
            color: #555;
        }
        .star-rating {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }
        .star {
            font-size: 24px;
            color: #ffcc00;
            margin-right: 2px;
        }
        .rating-info {
            margin-top: 8px;
            font-size: 14px;
            color: #333;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Drivers Rate</h1>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rating = round($row['avg_rating']); // Round average rating

            echo "
                <div class='driver-card'>
                    <img src='" . htmlspecialchars($row['image_url']) . "' alt='Driver Image' class='driver-image'>
                    <div class='driver-info'>
                        <h2 class='driver-name'>" . htmlspecialchars($row['driver_name']) . "</h2>
                        <p class='driver-address'>" . htmlspecialchars($row['address']) . "</p>
                    </div>
                    <div class='star-rating'>
            ";

            for ($i = 1; $i <= 5; $i++) {
                echo $i <= $rating 
                    ? "<span class='star'>&#9733;</span>"  // Filled star
                    : "<span class='star'>&#9734;</span>"; // Empty star
            }

            // Show how many people rated this driver
            echo "
                    </div>
                    <div class='rating-info'>
                        Rated by " . intval($row['rating_count']) . " " . (intval($row['rating_count']) === 1 ? "person" : "people") . "
                    </div>
                </div>
            ";
        }
    } else {
        echo "<p>No drivers found.</p>";
    }

    $conn->close();
    ?>
    <div style="text-align:center; margin-top: 30px;">
        <button onclick="history.back()" style="padding: 10px 20px; font-size: 16px; border: none; background-color: #007BFF; color: white; border-radius: 5px; cursor: pointer;">
            ⬅ Go Back
        </button>
    </div>
</div>

</body>
</html>
