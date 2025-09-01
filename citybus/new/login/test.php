
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'config.php';

if ($conn->connect_error) {
    die("❌ Database Connection Failed: " . $conn->connect_error);
} else {
    echo "✅ Database Connected Successfully!";
}
?>
