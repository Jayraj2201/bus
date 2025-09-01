<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredCode = $_POST['verification_code'];
    $actualCode = $_SESSION['verification_code'] ?? null;
    $expiry = $_SESSION['code_expiry'] ?? 0;

    if (time() > $expiry) {
        echo "❌ Verification code expired!";
        exit;
    }

    if ($enteredCode == $actualCode) {
        // Verification successful
        $_SESSION['authenticated'] = true; // Set session flag for successful login
        header("Location: collage/login.html"); // Redirect to user dashboard
        exit;
    } else {
        echo "❌ Incorrect verification code!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code - BusTracker</title>
</head>
<body>
    <h2>Enter Verification Code</h2>
    <form action="verify.php" method="POST">
        <label for="verification_code">Verification Code:</label>
        <input type="text" name="verification_code" required>
        <button type="submit">Verify</button>
    </form>
</body>
</html>
