<?php
$host = "localhost";
$user = "root";
$pass = ""; // change if you have a password
$dbname = "ecarga";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
