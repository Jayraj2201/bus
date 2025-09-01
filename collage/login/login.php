<?php
require 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Get user details from database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check if password is correct
        if (password_verify($password, $user['password'])) {
            if ($user['status'] == 1) { // Status must be 1 (active)
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                echo "✅ Login successful! Welcome, " . $user['email'] . "<br>";

                // Role-based navigation or response
                switch ($user['role']) {
                    case 'admin':
                        echo "🛠️ Logged in as Admin<br> <br>";
                        echo '<button onclick="window.location.href=\'../admin/dashbord.php\'">Admin Dashboard</button>';
                        break;

                    case 'driver':
                        echo "🚌 Logged in as Driver<br> <br>";
                        echo '<button onclick="window.location.href=\'../driver/dashbord.php\'">Driver Panel</button>';
                        break;

                    case 'user':
                        echo "👤 Logged in as User<br> <br>";
                        echo '<button onclick="window.location.href=\'../user/dashbord.php\'">User Home</button>';
                        break;

                    default:
                        echo "⚠️ Unknown role. Please contact admin.";
                        break;
                }

            } else {
                echo "❌ Your account is not active. Please verify your email.";
            }
        } else {
            echo "❌ Invalid email or password!";
        }
    } else {
        echo "❌ User not found!";
        echo '<br><br><a href="../register.html"><button type="button">Register</button></a><br><br>';
    }

    $stmt->close();
    $conn->close();
}
?>
