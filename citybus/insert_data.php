<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $latitude = $_POST['lat'];
    $longitude = $_POST['long'];
    $timestamp = $_POST['timestamp'];

    $insert = "INSERT INTO temporary_bus (latitude, longitude, timestamp) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'sss', $latitude, $longitude, $timestamp);
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Query preparation failed"]);
    }
}
?>
