<?php
$conn = new mysqli("localhost", "root", "", "test");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role = $_POST["role"];

    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role, verification_code, is_verified, status) VALUES (?, ?, ?, ?, ?, '', 0, 1)");
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $password, $role);

    if ($stmt->execute()) {
        echo "New user/driver added successfully.";
    } else {
        echo "Error adding user: " . $stmt->error;
    }
}
?>
