<?php
session_start();

$host = "localhost";
$dbname = "ecarga";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $driver_name = $_POST['driver_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = $_POST['address'];
    $vehicle_type = $_POST['vehicle_type'];
    $plate_number = $_POST['plate_number'];
    $years_experience = $_POST['years_experience'];
    $image_url = $_POST['image_url'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO drivers 
                (driver_name, email, password, address, vehicle_type, plate_number, years_experience, image_url, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'available')");
            $stmt->execute([$driver_name, $email, $hashed_password, $address, $vehicle_type, $plate_number, $years_experience, $image_url]);

            $_SESSION['otp_email_driver'] = $email;
            header("Location: send_otp_driver.php?email=" . urlencode($email));
            exit();

        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Driver Register | eCarga</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
  body { font-family: Arial, sans-serif; background: #f9f9f9; }
  .container { max-width: 400px; margin: 50px auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
  input, textarea { width: 100%; padding: 10px; margin: 8px 0; border-radius: 5px; border: 1px solid #ccc; }
  button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
  button:hover { background: #0056b3; }
  .error { color: red; margin-bottom: 10px; }
  a { color: #007bff; text-decoration: none; }
  a:hover { text-decoration: underline; }
</style>
</head>
<body>
<div class="container">
  <h1>Driver Registration</h1>
  <?php if (!empty($error)) { echo "<p class='error'>$error</p>"; } ?>
  <form method="POST" action="">
    <input type="text" name="driver_name" placeholder="Full Name" required />
    <input type="email" name="email" placeholder="Email" required />
    <input type="password" name="password" placeholder="Password" required />
    <input type="password" name="confirm_password" placeholder="Confirm Password" required />
    <textarea name="address" placeholder="Address" required></textarea>
    <input type="text" name="vehicle_type" placeholder="Vehicle Type" required />
    <input type="text" name="plate_number" placeholder="Plate Number" required />
    <input type="number" name="years_experience" placeholder="Years of Experience" min="0" required />
    <input type="text" name="image_url" placeholder="Image URL" required />
    <button type="submit">Register</button>
  </form>
  <p>Already have an account? <a href="driver_login.php">Login here</a>.</p>
</div>
</body>
</html>
