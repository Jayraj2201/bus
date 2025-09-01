<?php
$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);

$id = intval($data['id']);
$first_name = $conn->real_escape_string($data['first_name']);
$last_name = $conn->real_escape_string($data['last_name']);
$email = $conn->real_escape_string($data['email']);
$role = $conn->real_escape_string($data['role']);
$is_verified = intval($data['is_verified']);
$status = intval($data['status']);

// Optional: Prevent changing email to one that already exists (for another user)
$check = $conn->query("SELECT id FROM users WHERE email='$email' AND id!=$id");
if ($check->num_rows > 0) {
    echo "Email already exists!";
    exit;
}

$sql = "UPDATE users SET
            first_name = '$first_name',
            last_name = '$last_name',
            email = '$email',
            role = '$role',
            is_verified = $is_verified,
            status = $status
        WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo "User updated successfully!";
} else {
    echo "Error updating user: " . $conn->error;
}
$conn->close();
?>
