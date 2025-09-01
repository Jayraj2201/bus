<?php
require 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $code = trim($_POST['verification_code']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND verification_code = ? AND is_verified = 0");
    $stmt->bind_param("si", $email, $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $update = $conn->prepare("UPDATE users SET is_verified = 1, status = 1 WHERE email = ?");
        $update->bind_param("s", $email);
        $update->execute();

        $message = "<div class='success'>✅ Email verified successfully! You can now log in.</div>";
    } else {
        $message = "<div class='error'>❌ Invalid verification code or already verified!</div>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - BusTracker</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .verify-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 40px;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: #4a6cf7;
            text-align: center;
            margin-bottom: 30px;
        }

        .logo span {
            color: #333;
        }

        .verify-form h2 {
            font-size: 26px;
            margin-bottom: 25px;
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            font-size: 16px;
        }

        .form-group input:focus {
            border-color: #4a6cf7;
            box-shadow: 0 0 0 2px rgba(74, 108, 247, 0.2);
            outline: none;
        }

        .btn {
            display: block;
            width: 100%;
            background-color: #4a6cf7;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #3a5ddb;
        }

        .message {
            margin-top: 20px;
            text-align: center;
        }

        .success {
            color: green;
            font-weight: bold;
        }

        .error {
            color: red;
            font-weight: bold;
        }

        .back-to-home {
            text-align: center;
            margin-top: 20px;
        }

        .back-to-home a {
            color: #555;
            text-decoration: none;
        }

        .back-to-home a:hover {
            color: #4a6cf7;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="logo">Bus<span>Tracker</span></div>

        <form class="verify-form" method="POST">
            <h2>Verify Your Email</h2>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" required placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label for="verification_code">Verification Code</label>
                <input type="text" name="verification_code" id="verification_code" required placeholder="Enter code">
            </div>

            <button type="submit" class="btn">Verify</button>
        </form>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="back-to-home">
            <a href="./../index.html">← Back to Home</a>
        </div>
    </div>
</body>
</html>
