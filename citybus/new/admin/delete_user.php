<?php
$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = intval($_GET['id']);
$sql = "UPDATE users SET status = 0 WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo "User deactivated (soft deleted)!";
} else {
    echo "Error deleting user: " . $conn->error;
}
$conn->close();
?>
