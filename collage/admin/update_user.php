<?php
$conn = new mysqli("localhost", "root", "", "test");

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id'])) {
    $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=?, role=?, is_verified=?, status=? WHERE id=?");
    $stmt->bind_param("ssssiis", $data['first_name'], $data['last_name'], $data['email'], $data['role'], $data['is_verified'], $data['status'], $data['id']);
    
    if ($stmt->execute()) {
        echo "User updated successfully.";
    } else {
        echo "Failed to update user.";
    }
    $stmt->close();
}
?>
