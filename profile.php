<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "ecarga");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Example user ID (replace with session variable in real implementation)
$user_id = 1;

// Fetch user details
$sql = "SELECT name, email, avatar_url, address FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $avatar_url, $address);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Profile - ECARGA</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f2f2f2;
      margin: 0;
      padding: 20px;
    }
    .profile-container {
      max-width: 500px;
      margin: auto;
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      text-align: center;
    }
    .profile-avatar {
      width: 180px;
      height: 180px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 20px;
    }
    .profile-info h2 {
      margin: 10px 0 5px;
      font-size: 24px;
    }
    .profile-info p {
      margin: 4px 0;
      color: #555;
    }
    .back-button {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #007BFF;
      color: white;
      border: none;
      border-radius: 6px;
      text-decoration: none;
      font-size: 16px;
      cursor: pointer;
    }
    .back-button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>

  <div class="profile-container">
    <img src="<?php echo htmlspecialchars($avatar_url); ?>" alt="Avatar" class="profile-avatar">
    <div class="profile-info">
      <h2><?php echo htmlspecialchars($name); ?></h2>
      <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
      <p><strong>Address:</strong> <?php echo htmlspecialchars($address); ?></p>
    </div>
    <button class="back-button" onclick="window.history.back();">Go Back</button>
  </div>

</body>
</html>
