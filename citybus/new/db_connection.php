<?php
// Database Connection File

$servername = "localhost"; // Change if using a remote server
$username = "root"; // Default for XAMPP (Change if different)
$password = ""; // Default for XAMPP (Change if you set a password)
$database = "itm_BusTracking"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Uncomment for debugging
// echo "✅ Database Connected Successfully!";
?>
