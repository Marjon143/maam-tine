<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Our Services | ECARGA</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f5f7fa;
      color: #333;
    }
    .header {
      background-color: #4CAF50;
      padding: 1.5rem;
      color: white;
      text-align: center;
    }
    .back-button {
      display: inline-block;
      margin: 1rem 1rem 0 1rem;
      padding: 0.5rem 1rem;
      background-color: #4CAF50;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      transition: background-color 0.3s ease;
    }
    .back-button:hover {
      background-color: #45a049;
    }
    .container {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1rem;
    }
    .section-title {
      text-align: center;
      font-size: 2rem;
      margin-bottom: 2rem;
      color: #333;
    }
    .services {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 2rem;
    }
    .service-card {
      background: white;
      padding: 1.5rem;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s ease;
    }
    .service-card:hover {
      transform: translateY(-5px);
    }
    .service-icon {
      font-size: 2.5rem;
      margin-bottom: 1rem;
      color: #4CAF50;
    }
    .service-title {
      font-size: 1.25rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }
    .service-description {
      font-size: 1rem;
      color: #555;
    }
    .footer {
      margin-top: 3rem;
      text-align: center;
      font-size: 0.9rem;
      color: #888;
      padding: 1rem;
    }
  </style>
</head>
<body>

  <div class="header">
    <h1>ECARGA Services</h1>
    <p>Safe, fast, and convenient motorcycle bookings</p>
  </div>

  <!-- Go Back Button -->
  <a href="index.php" class="back-button">← Go Back</a>

  <div class="container">
    <h2 class="section-title">What We Offer</h2>
    <div class="services">
      <div class="service-card">
        <div class="service-icon">🛵</div>
        <div class="service-title">Motorcycle Ride Booking</div>
        <div class="service-description">
          Quickly book nearby motorcycles for safe and hassle-free travel.
        </div>
      </div>

      <div class="service-card">
        <div class="service-icon">🕒</div>
        <div class="service-title">Real-time Availability</div>
        <div class="service-description">
          See available drivers instantly and book a ride in seconds.
        </div>
      </div>

      <div class="service-card">
        <div class="service-icon">🔐</div>
        <div class="service-title">Secure Transactions</div>
        <div class="service-description">
          Your payments and booking data are encrypted and protected.
        </div>
      </div>

      <div class="service-card">
        <div class="service-icon">📍</div>
        <div class="service-title">Live Location Tracking</div>
        <div class="service-description">
          Track your driver in real-time from pickup to drop-off.
        </div>
      </div>

      <div class="service-card">
        <div class="service-icon">⭐</div>
        <div class="service-title">Driver Ratings</div>
        <div class="service-description">
          View driver ratings and feedback before booking your ride.
        </div>
      </div>

      <div class="service-card">
        <div class="service-icon">📩</div>
        <div class="service-title">Email Notifications</div>
        <div class="service-description">
          Get instant booking confirmations and trip summaries in your inbox.
        </div>
      </div>
    </div>
  </div>

  <div class="footer">
    &copy; 2025 ECARGA. All Rights Reserved.
  </div>

</body>
</html>
