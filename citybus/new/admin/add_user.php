<?php
$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize inputs
$first_name = $conn->real_escape_string($_POST['first_name']);
$last_name = $conn->real_escape_string($_POST['last_name']);
$email = $conn->real_escape_string($_POST['email']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password hashing
$role = $conn->real_escape_string($_POST['role']);
$is_verified = 0; // default
$status = 1; // active by default

// Check if email already exists
$check = $conn->query("SELECT id FROM users WHERE email='$email'");
if ($check->num_rows > 0) {
    echo "Email already exists!";
    exit;
}

$sql = "INSERT INTO users (first_name, last_name, email, password, role, is_verified, status)
        VALUES ('$first_name', '$last_name', '$email', '$password', '$role', $is_verified, $status)";

if ($conn->query($sql) === TRUE) {
    echo "User added successfully!";
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>
