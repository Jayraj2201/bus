<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "Itm_BusTracking";

    $conn = new mysqli($host, $user, $pass, $dbname);

    if ($conn->connect_error) {
        die("❌ Database Connection Failed: " . $conn->connect_error);
    }

    $route_name   = $_POST['route_name'] ?? '';
    $start_time   = $_POST['start_time'] ?? '';
    $total_stops  = $_POST['total_stops'] ?? 0;
    $start_point  = $_POST['start_point'] ?? '';
    $end_point    = $_POST['end_point'] ?? '';
    $stops        = $_POST['stops'] ?? '';
    $fare         = $_POST['fare'] ?? 0;

    // ✅ Changed table name here
    $sql = "INSERT INTO bus_routes (route_name, start_time, total_stops, start_point, end_point, stops, fare) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssisssd", $route_name, $start_time, $total_stops, $start_point, $end_point, $stops, $fare);

        if ($stmt->execute()) {
            echo "✅ Route added successfully!";
        } else {
            echo "❌ Error adding route: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "❌ SQL Prepare Failed: " . $conn->error;
    }

    $conn->close();
} else {
    echo "❌ Access Denied: Please submit the form.";
}
?>
