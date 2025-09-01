<?php
// Database Connection File
$servername = "localhost";  
$username = "root";  
$password = "";  
$database = "BusTracking";  

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "❌ Database Connection Failed: " . $conn->connect_error]));
}

// Ensure request is GET or POST
header("Content-Type: application/json");  // Set response type

if ($_SERVER["REQUEST_METHOD"] === "POST" || $_SERVER["REQUEST_METHOD"] === "GET") {
    
    // Determine if data is coming from GET or POST
    $latitude = isset($_REQUEST['lat']) ? trim($_REQUEST['lat']) : null;
    $longitude = isset($_REQUEST['long']) ? trim($_REQUEST['long']) : null;
    $timestamp = isset($_REQUEST['timestamp']) ? trim($_REQUEST['timestamp']) : null;

    // Validate required parameters
    if (!$latitude || !$longitude) {
        echo json_encode(["status" => "error", "message" => "Missing latitude or longitude"]);
        exit;
    }

    // If timestamp is missing, use current server time
    if (!$timestamp) {
        $timestamp = date("Y-m-d H:i:s");  // Get current timestamp
    } else {
        // Try converting input to correct DATETIME format
        $timestamp = date("Y-m-d H:i:s", strtotime($timestamp));
    }

    // Validate input
    if (!is_numeric($latitude) || !is_numeric($longitude)) {
        echo json_encode(["status" => "error", "message" => "Invalid latitude or longitude"]);
        exit;
    }

    // SQL query
    $insert = "INSERT INTO temporary_bus (latitude, longitude, timestamp) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert);

    if ($stmt) {
        $stmt->bind_param('sss', $latitude, $longitude, $timestamp);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            $error = "Insert Error: " . $stmt->error;
            file_put_contents("error_log.txt", $error . PHP_EOL, FILE_APPEND);  // Log error
            echo json_encode(["status" => "error", "message" => $error]);
        }
        $stmt->close();
    } else {
        $error = "Query Preparation Failed: " . $conn->error;
        file_put_contents("error_log.txt", $error . PHP_EOL, FILE_APPEND);  // Log error
        echo json_encode(["status" => "error", "message" => $error]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "405 Method Not Allowed"]);
}

// Close connection
$conn->close();
?>
