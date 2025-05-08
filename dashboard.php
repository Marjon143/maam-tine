<?php
// Include this at the very top of your file before any HTML
$conn = new mysqli("localhost", "root", "", "ecarga");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
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

<style>.table table {
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
    background-color:rgb(64, 163, 209);
    font-weight: bold;
}

.table tr:nth-child(even) {
    background-color: #f9f9f9;
}

.table tr:hover {
    background-color: #f1f1f1;
}</style>
    <section class="header">
        <div class="logo">
            <i class="ri-menu-line icon icon-0 menu"></i>
            <h2>E<span>CARGA</span></h2>
        </div>
        <div class="search--notification--profile">
            <div class="search">
                <input type="text" placeholder="Search Scdule..">
                <button><i class="ri-search-2-line"></i></button>
            </div>
            <div class="notification--profile">
                <div class="picon lock">
                    <i class="ri-lock-line"></i>
                </div>
                <div class="picon bell">
                    <i class="ri-notification-2-line"></i>
                </div>
                <div class="picon chat">
                    <i class="ri-wechat-2-line"></i>
                </div>
                <div class="picon profile">
                    <img src="assets/images/profile.jpg" alt="">
                </div>
            </div>
        </div>
    </section>
    <section class="main">
        <div class="sidebar">
            <ul class="sidebar--items">
                <li>
                    <a href="#" id="active--link">
                        <span class="icon icon-1"><i class="ri-layout-grid-line"></i></span>
                        <span class="sidebar--item">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="customer.php">
                        <span class="icon icon-2"><i class="ri-calendar-2-line"></i></span>
                        <span class="sidebar--item">Customers</span>
                    </a>
                </li>
                <li>
                    <a href="booking.php">
                        <span class="icon icon-3"><i class="ri-user-2-line"></i></span>
                        <span class="sidebar--item" style="white-space: nowrap;">Bookings</span>
                    </a>
                </li>
                <li>
                    <a href="availability.php">
                        <span class="icon icon-4"><i class="ri-user-line"></i></span>
                        <span class="sidebar--item">Availability</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="icon icon-5"><i class="ri-line-chart-line"></i></span>
                        <span class="sidebar--item">Routes</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="icon icon-6"><i class="ri-customer-service-line"></i></span>
                        <span class="sidebar--item">Reporting</span>
                    </a>
                </li>
            </ul>
            <ul class="sidebar--bottom-items">
                <li>
                    <a href="#">
                        <span class="icon icon-7"><i class="ri-settings-3-line"></i></span>
                        <span class="sidebar--item">Settings</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="icon icon-8"><i class="ri-logout-box-r-line"></i></span>
                        <span class="sidebar--item">Logout</span>
                    </a>
                </li>
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
                                <h1>152</h1>
                            </div>
                            <i class="ri-user-2-line card--icon--lg"></i>
                        </div>
                        <div class="card--stats">
                            <span><i class="ri-bar-chart-fill card--icon stat--icon"></i></span>
                            <span><i class="ri-arrow-up-s-fill card--icon up--arrow"></i></span>
                            <span><i class="ri-arrow-down-s-fill card--icon down--arrow"></i>2</span>
                        </div>
                    </div>
                    <div class="card card-2">
                        <div class="card--data">
                            <div class="card--content">
                                <h5 class="card--title">Total Rides</h5>
                                <h1>1145</h1>
                            </div>
                            <i class="ri-user-line card--icon--lg"></i>
                        </div>
                        <div class="card--stats">
                            <span><i class="ri-bar-chart-fill card--icon stat--icon"></i>82%</span>
                            <span><i class="ri-arrow-up-s-fill card--icon up--arrow"></i>230</span>
                            <span><i class="ri-arrow-down-s-fill card--icon down--arrow"></i>45</span>
                        </div>
                    </div>
                    <div class="card card-3">
                        <div class="card--data">
                            <div class="card--content">
                                <h5 class="card--title">Schedule</h5>
                                <h1>102</h1>
                            </div>
                            <i class="ri-calendar-2-line card--icon--lg"></i>
                        </div>
                        <div class="card--stats">
                            <span><i class="ri-bar-chart-fill card--icon stat--icon"></i>27%</span>
                            <span><i class="ri-arrow-up-s-fill card--icon up--arrow"></i>31</span>
                            <span><i class="ri-arrow-down-s-fill card--icon down--arrow"></i>23</span>
                        </div>
                    </div>
                    <div class="card card-4">
                        <div class="card--data">
                            <div class="card--content">
                                <h5 class="card--title">Beds Available</h5>
                                <h1>15</h1>
                            </div>
                            <i class="ri-hotel-bed-line card--icon--lg"></i>
                        </div>
                        <div class="card--stats">
                            <span><i class="ri-bar-chart-fill card--icon stat--icon"></i>8%</span>
                            <span><i class="ri-arrow-up-s-fill card--icon up--arrow"></i>11</span>
                            <span><i class="ri-arrow-down-s-fill card--icon down--arrow"></i>2</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="doctors">
                <div class="title">
                    <h2 class="section--title">Riders</h2>
                    <div class="doctors--right--btns">
                        <select name="date" id="date" class="dropdown doctor--filter">
                            <option >Filter</option>
                            <option value="free">Free</option>
                            <option value="scheduled">Scheduled</option>
                        </select>
                       
                    </div>
                </div>
                <div class="doctors--cards">
                    <a href="#" class="doctor--card">
                        <div class="img--box--cover">
                            <div class="img--box">
                                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTNt-Vy_I4LN6JZhvwnJpxoetaJP321ospbf_fxhzPABPv1k67JLDsQbammchNbmtep_H1RVS0-rq9_qqdpO8vYgA" alt="">
                            </div>
                        </div>
                        <p class="free">Free</p>
                    </a>
                    <a href="#" class="doctor--card">
                        <div class="img--box--cover">
                            <div class="img--box">
                                <img src="assets/images/doctor2.jpg" alt="">
                            </div>
                        </div>
                        <p class="scheduled">Scheduled</p>
                    </a>
                    <a href="#" class="doctor--card">
                        <div class="img--box--cover">
                            <div class="img--box">
                                <img src="assets/images/doctor3.jpg" alt="">
                            </div>
                        </div>
                        <p class="scheduled">Scheduled</p>
                    </a>
                    <a href="#" class="doctor--card">
                        <div class="img--box--cover">
                            <div class="img--box">
                                <img src="assets/images/doctor4.jpg" alt="">
                            </div>
                        </div>
                        <p class="free">Free</p>
                    </a>
                    <a href="#" class="doctor--card">
                        <div class="img--box--cover">
                            <div class="img--box">
                                <img src="assets/images/doctor5.jpg" alt="">
                            </div>
                        </div>
                        <p class="scheduled">Scheduled</p>
                    </a>
                    <a href="#" class="doctor--card">
                        <div class="img--box--cover">
                            <div class="img--box">
                                <img src="assets/images/doctor6.jpg" alt="">
                            </div>
                        </div>
                        <p class="free">Free</p>
                    </a>
                    <a href="#" class="doctor--card">
                        <div class="img--box--cover">
                            <div class="img--box">
                                <img src="assets/images/doctor7.jpg" alt="">
                            </div>
                        </div>
                        <p class="scheduled">Scheduled</p>
                    </a>
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
// Query to fetch booking data
$sql = "SELECT name, booking_date, status FROM bookings ORDER BY booking_date DESC LIMIT 6";
$result = $conn->query($sql);

if ($result->num_rows > 0):
    // Loop through results and display them
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
    <script src="assets/dashboard.js"></script>
</body>
</html>