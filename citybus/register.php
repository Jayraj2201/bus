<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    http_response_code(200);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "Itm_BusTracking";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

if (!isset($_GET['bus_id']) || !is_numeric($_GET['bus_id'])) {
    echo json_encode(["error" => "Invalid or missing bus_id"]);
    exit;
}

$bus_id = intval($_GET['bus_id']);

$sql = "SELECT route_name, start_time, total_stops, start_point, end_point, stops, fare 
        FROM bus_routes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $bus_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $row["fare"] = "Rs. " . number_format($row["fare"], 2) . "/-";
    echo json_encode($row);
} else {
    echo json_encode(["error" => "No route found"]);
}

$stmt->close();
$conn->close();

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
