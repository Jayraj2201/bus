<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$database = "itm_bustracking"; // ✅ Corrected database name

// ✅ Corrected MySQL Connection
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["error" => "Database connection failed", "details" => $conn->connect_error]));
}

// ✅ Debug: Check if route_id is being received
if (!isset($_GET['route_id']) || !is_numeric($_GET['route_id'])) {
    http_response_code(400);
    die(json_encode(["error" => "Invalid or missing route_id"]));
}

$route_id = intval($_GET['route_id']);

// ✅ Check if table `bus_routes` exists
$table_check = $conn->query("SHOW TABLES LIKE 'bus_routes'");
if ($table_check->num_rows == 0) {
    http_response_code(500);
    die(json_encode(["error" => "Table 'bus_routes' does not exist"]));
}

// ✅ Fetch route details from `bus_routes`
$sql = "SELECT id, route_name, start_time, total_stops, start_point, end_point, stops, fare FROM bus_routes WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    die(json_encode(["error" => "SQL preparation failed", "sql_error" => $conn->error]));
}

$stmt->bind_param("i", $route_id);
$stmt->execute();
$result = $stmt->get_result();

$data = $result->fetch_assoc();
$stmt->close();
$conn->close();

// ✅ Check if data exists
if (!$data) {
    http_response_code(404);
    die(json_encode(["message" => "No route found for the given route_id"]));
}

// ✅ Return JSON response
http_response_code(200);
echo json_encode($data, JSON_PRETTY_PRINT);
?>
