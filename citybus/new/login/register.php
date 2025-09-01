<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/send_email.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST["firstName"]);
    $last_name = trim($_POST["lastName"]);
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $role = $_POST["ntype"];


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("❌ Invalid email format.");
    }

    if ($password !== $confirm_password) {
        die("❌ Passwords do not match.");
    }

    if (strlen($password) < 6) {
        die("❌ Password must be at least 6 characters.");
    }

    // Check if email already exists
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    if (!$check_stmt) {
        die("❌ Prepare failed: " . $conn->error);
    }
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo '<button onclick="window.location.href=\'./../index.html\'" type="button">Go to website</button>';
        die("❌ Email already registered. Please log in.");
    }

    $verification_code = rand(100000, 999999);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 🔽 Updated insert with all fields
    $insert_sql = "INSERT INTO users (first_name, last_name, email, password, verification_code, is_verified,role) 
                   VALUES (?, ?, ?, ?, ?, 0, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    if (!$insert_stmt) {
        die("❌ Insert prepare failed: " . $conn->error);
    }

    $insert_stmt->bind_param("ssssis", $first_name, $last_name, $email, $hashed_password, $verification_code, $role);

    if ($insert_stmt->execute()) {
        if (sendVerificationEmail($email, $verification_code)) {
            echo "✅ Registration successful! Check your email for the verification code.<br><br>";
            echo '<a href="verify.php"><button type="button">Go to verify</button></a>';
        } else {
            echo "❌ Registered but failed to send verification email.";
        }
    } else {
        die("❌ Registration failed: " . $insert_stmt->error);
    }

    $check_stmt->close();
    $insert_stmt->close();
    $conn->close();
}



?>

