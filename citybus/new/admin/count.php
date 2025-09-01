<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root"; // Default XAMPP username
$pass = ""; // Default XAMPP password (leave empty)
$dbname = "test"; // Your actual database name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("❌ Database Connection Failed: " . $conn->connect_error);
} 
else {
    echo "✅ Database Connected Successfully!<br>";
}

$sql = "SELECT COUNT(*) AS total_users FROM users";
$result = $conn->query($sql);
$total_users = 0;

if ($result && $row = $result->fetch_assoc()) {
    $total_users = $row['total_users'];
    echo "Total users: $total_users<br>";
}
?>