<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

error_reporting(E_ALL);
ini_set('display_errors', 0); // Hide errors in JSON output

$servername = "localhost";
$username = "root";
$password = "";
$database = "bus_tracking"; // Updated database name

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

if (!isset($_GET['route_id']) || !is_numeric($_GET['route_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid or missing route_id"]);
    exit;
}

$route_id = intval($_GET['route_id']);
$sql = "SELECT schedule_id, stop_name, departure_time FROM bus_schedules WHERE route_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "SQL preparation failed"]);
    exit;
}

$stmt->bind_param("i", $route_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$stmt->close();
$conn->close();

if (empty($data)) {
    http_response_code(404);
    echo json_encode(["message" => "No schedules found for the given route_id"]);
    exit;
}

http_response_code(200);
echo json_encode($data, JSON_PRETTY_PRINT);
?>
