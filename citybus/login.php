<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    if (empty($email) || empty($password)) {
        echo "<script>alert('Please fill in all fields'); window.location.href='login.html';</script>";
        exit();
    }
    
    $stmt = $conn->prepare("SELECT id, role, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect based on role
            switch ($user['role']) {
                case 'admin':
                    header("Location: admin.php");
                    break;
                case 'driver':
                    header("Location: driver.php");
                    break;
                case 'user':
                    header("Location: user.php");
                    break;
                default:
                    session_destroy();
                    echo "<script>alert('Invalid role'); window.location.href='login.html';</script>";
                    exit();
            }
            exit();
        } else {
            echo "<script>alert('Invalid password'); window.location.href='login.html';</script>";
        }
    } else {
        echo "<script>alert('User not found'); window.location.href='login.html';</script>";
    }
} else {
    header("Location: login.html");
    exit();
}
?>
