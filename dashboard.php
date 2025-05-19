    <?php
    // Include this at the very top of your file before any HTML
    $conn = new mysqli("localhost", "root", "", "ecarga");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    // Fetch total users
$userCountResult = $conn->query("SELECT COUNT(*) AS total_users FROM users");
$userCount = $userCountResult->fetch_assoc()['total_users'] ?? 0;

// Fetch total drivers
$driverCountResult = $conn->query("SELECT COUNT(*) AS total_drivers FROM drivers");
$driverCount = $driverCountResult->fetch_assoc()['total_drivers'] ?? 0;

// Fetch total transactions
$transactionCountResult = $conn->query("SELECT COUNT(*) AS total_transactions FROM transactions");
$transactionCount = $transactionCountResult->fetch_assoc()['total_transactions'] ?? 0;

// Fetch total feedback from both customer_feedback and driver_feedback (assuming these tables exist)
$customerFeedbackCountResult = $conn->query("SELECT COUNT(*) AS total_customer_feedback FROM customer_feedback");
$customerFeedbackCount = $customerFeedbackCountResult->fetch_assoc()['total_customer_feedback'] ?? 0;

$driverFeedbackCountResult = $conn->query("SELECT COUNT(*) AS total_driver_feedback FROM driver_feedback");
$driverFeedbackCount = $driverFeedbackCountResult->fetch_assoc()['total_driver_feedback'] ?? 0;

$totalFeedback = $customerFeedbackCount + $driverFeedbackCount;
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <title>Dashboard</title>
    </head>
    <body>

    <style>
    .table table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    .table th, .table td {
        padding: 10px;
        text-align: left;
        border: 1px solid #ddd;
    }
    .table th {
        background-color: rgb(64, 163, 209);
        font-weight: bold;
    }
    .table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .table tr:hover {
        background-color: #f1f1f1;
    }
    </style>

    <section class="header">
    <div class="logo">
        <i class="ri-menu-line icon icon-0 menu"></i>
        <h2>E<span>CARGA</span></h2>
    </div>
    <div class="search--notification--profile">
        <div class="search">
        <input type="text" placeholder="Search Schedule..">
        <button><i class="ri-search-2-line"></i></button>
        </div>
        <div class="notification--profile">
        <div class="picon lock"><i class="ri-lock-line"></i></div>
        <div class="picon bell"><i class="ri-notification-2-line"></i></div>
        <div class="picon chat"><i class="ri-wechat-2-line"></i></div>
        <div class="picon profile">
            <img src="assets/images/profile.jpg" alt="">
        </div>
        </div>
    </div>
    </section>

    <section class="main">
    <div class="sidebar">
        <ul class="sidebar--items">
        <li><a href="#" id="active--link"><span class="icon icon-1"><i class="ri-layout-grid-line"></i></span><span class="sidebar--item">Dashboard</span></a></li>
        <li><a href="crud.php"><span class="icon icon-2"><i class="ri-calendar-2-line"></i></span><span class="sidebar--item">Create</span></a></li>
        <li><a href="transaction_admin.php"><span class="icon icon-3"><i class="ri-user-2-line"></i></span><span class="sidebar--item">Transactions</span></a></li>
        <li><a href="availability.php"><span class="icon icon-4"><i class="ri-user-line"></i></span><span class="sidebar--item">Availability</span></a></li>
        <li><a href="route.php"><span class="icon icon-5"><i class="ri-line-chart-line"></i></span><span class="sidebar--item">Routes</span></a></li>
        <li><a href="#"><span class="icon icon-6"><i class="ri-customer-service-line"></i></span><span class="sidebar--item">Reporting</span></a></li>
        </ul>
        <ul class="sidebar--bottom-items">
        <li><a href="#"><span class="icon icon-7"><i class="ri-settings-3-line"></i></span><span class="sidebar--item">Settings</span></a></li>
        <li><a href="#"><span class="icon icon-8"><i class="ri-logout-box-r-line"></i></span><span class="sidebar--item">Logout</span></a></li>
        </ul>
    </div>

    <div class="main--content">
        <div class="overview">
        <div class="title">
            <h2 class="section--title">Overview</h2>
            <select name="date" id="date" class="dropdown">
            <option value="today">Today</option>
            <option value="lastweek">Last Week</option>
            <option value="lastmonth">Last Month</option>
            <option value="lastyear">Last Year</option>
            <option value="alltime">All Time</option>
            </select>
        </div>
        <div class="cards">
           <div class="card card-1">
  <div class="card--data">
    <div class="card--content">
      <h5 class="card--title">Total Users</h5>
      <h1><?php echo $userCount; ?></h1>
    </div>
    <i class="ri-user-2-line card--icon--lg"></i>
  </div>
</div>
<div class="card card-2">
  <div class="card--data">
    <div class="card--content">
      <h5 class="card--title">Total Drivers</h5>
      <h1><?php echo $driverCount; ?></h1>
    </div>
    <i class="ri-user-line card--icon--lg"></i>
  </div>
</div>
<div class="card card-3">
  <div class="card--data">
    <div class="card--content">
      <h5 class="card--title">Total Transactions</h5>
      <h1><?php echo $transactionCount; ?></h1>
    </div>
    <i class="ri-calendar-2-line card--icon--lg"></i>
  </div>
</div>
<div class="card card-4s">
  <div class="card--data">
    <div class="card--content">
      <h5 class="card--title">Number of Feedback</h5>
      <h1><?php echo $totalFeedback; ?></h1>
    </div>
    <i class="ri-feedback-line card--icon--lg"></i>
  </div>
</div>

        </div>
        </div>

        <div class="doctors">
        <div class="title">
            <h2 class="section--title">Riders</h2>
            <div class="doctors--right--btns">
            <select name="filter" id="driverFilter" class="dropdown doctor--filter">
                <option value="all">Filter</option>
                <option value="available">Available</option>
                <option value="busy">Busy</option>
            </select>
            </div>
        </div>
        <div class="doctors--cards" id="driverList">
            <?php
            $sql = "SELECT driver_name, image_url, status FROM drivers";
            $result = $conn->query($sql);
            if ($result->num_rows > 0):
                while($row = $result->fetch_assoc()):
                    $statusClass = $row["status"] === "available" ? "free" : "scheduled";
            ?>
            <a href="#" class="doctor--card" data-status="<?php echo $row['status']; ?>">
            <div class="img--box--cover">
                <div class="img--box">
                <img src="<?php echo htmlspecialchars($row["image_url"]); ?>" alt="">
                </div>
            </div>
            <p class="<?php echo $statusClass; ?>"><?php echo ucfirst($row["status"]); ?> - <?php echo htmlspecialchars($row["driver_name"]); ?></p>
            </a>
            <?php
                endwhile;
            else:
            ?>
            <p>No drivers found.</p>
            <?php endif; ?>
        </div>
        </div>

        <div class="recent--patients">
        <div class="title">
            <h2 class="section--title">Recent Activities</h2>
        </div>
        <div class="table">
            <table>
            <thead>
                <tr>
                <th>Name</th>
                <th>Date</th>
                <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT name, booking_date, status FROM bookings ORDER BY booking_date DESC LIMIT 6";
                $result = $conn->query($sql);
                if ($result->num_rows > 0):
                    while($row = $result->fetch_assoc()):
                ?>
                <tr>
                <td><?php echo htmlspecialchars($row["name"]); ?></td>
                <td><?php echo htmlspecialchars($row["booking_date"]); ?></td>
                <td><?php echo htmlspecialchars($row["status"]); ?></td>
                </tr>
                <?php
                    endwhile;
                else:
                ?>
                <tr>
                <td colspan="3">No recent bookings found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
            </table>
        </div>
        </div>
    </div>
    </section>

    <script>
    document.getElementById('driverFilter').addEventListener('change', function () {
        var selected = this.value;
        var cards = document.querySelectorAll('.doctor--card');

        cards.forEach(function (card) {
            if (selected === 'all') {
                card.style.display = 'block';
            } else {
                card.style.display = card.getAttribute('data-status') === selected ? 'block' : 'none';
            }
        });
    });
    </script>

    <script src="assets/dashboard.js"></script>
    </body>
    </html>
