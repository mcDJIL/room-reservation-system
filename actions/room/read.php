<?php
require_once __DIR__ . '/../../config/connection.php';

if ($conn->connect_error) {
    die('Koneksi Gagal: ' . $conn->connect_error);
}

$rooms = [];
$sql = "SELECT r.id, r.room_name, r.capacity, r.is_active, b.name AS building_name 
        FROM rooms r 
        LEFT JOIN buildings b ON r.building_id = b.id
        WHERE r.is_active = 1";

$result = mysqli_query($conn, $sql);
if ($result === false) {
    die('Query error: ' . mysqli_error($conn));
}

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rooms[] = $row;
    }
}

mysqli_close($conn);
?>