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

// Fetch drivers from the database (using the 'address' column instead of 'description')
$sql = "SELECT driver_id, name, image_url, address FROM drivers";
$result = $conn->query($sql);

// Initialize the carousel items counter
$carouselItemIndex = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <!-- Add Bootstrap 5 CDN links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <title>Driver Carousel</title>
    <style>
        /* Adjust carousel image size */
        .carousel-inner img {
            max-height:500px; /* Set the max height */
            object-fit: cover; /* Maintain the aspect ratio while resizing */
        }
        /* Optionally, adjust the carousel size */
        #carouselExampleCaptions {
            max-width: 80%; /* Resize the whole carousel */
            margin: 0 auto; /* Center the carousel */
        }
    </style>
</head>
<body>
<div class="container text-center my-4" style="background-color: #f8f9fa; border: 2px solid #007bff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px;">
    <h3>Our Drivers</h3>
</div>

    
    <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <?php
            // Generate carousel indicators dynamically based on the number of drivers
            $result->data_seek(0); // Reset pointer to the start of the result set
            $index = 0;
            while ($row = $result->fetch_assoc()) {
                $activeClass = $index === 0 ? 'class="active"' : '';
                echo "<button type='button' data-bs-target='#carouselExampleCaptions' data-bs-slide-to='$index' $activeClass aria-label='Slide $index'></button>";
                $index++;
            }
            ?>
        </div>

        <div class="carousel-inner">
            <?php
            // Generate carousel items dynamically based on the database results
            $result->data_seek(0); // Reset pointer to the start of the result set
            $carouselItemIndex = 0;
            while ($row = $result->fetch_assoc()) {
                $activeClass = $carouselItemIndex === 0 ? 'active' : '';
                echo "<div class='carousel-item $activeClass'>
                        <img src='" . $row['image_url'] . "' class='d-block w-100' alt='" . $row['name'] . "'>
                        <div class='carousel-caption d-none d-md-block'>
                            <h5>" . $row['name'] . "</h5>
                            <p>" . $row['address'] . "</p> <!-- Updated to use 'address' instead of 'description' -->
                        </div>
                      </div>";
                $carouselItemIndex++;
            }
            ?>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- Go Back Button placed below the carousel -->
    <div class="container text-center mt-4">
        <a href="javascript:history.back()" class="btn btn-primary">Go Back</a>
    </div>

    <?php
    // Close the database connection
    $conn->close();
    ?>
</body>  
</html>
