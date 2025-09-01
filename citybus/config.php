<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "BusTracking"; // Ensure this is your correct database

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
