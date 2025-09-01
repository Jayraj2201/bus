<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "Itm_BusTracking";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['id'])) {
    http_response_code(400);
    echo "Invalid request";
    exit;
}

$id = $data['id'];
unset($data['id']);

$updates = [];
foreach ($data as $column => $value) {
    $updates[] = "`$column` = '" . $conn->real_escape_string($value) . "'";
}
$sql = "UPDATE bus_routes SET " . implode(", ", $updates) . " WHERE id = '$id'";

if ($conn->query($sql)) {
    echo "Route updated successfully";
} else {
    echo "Update failed: " . $conn->error;
}

$conn->close();
?>
